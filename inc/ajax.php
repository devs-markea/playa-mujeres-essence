<?php

// ─────────────────────────────────────────────────────────────────────────────
// Newsletter — reCAPTCHA v3 + Mailchimp
// ─────────────────────────────────────────────────────────────────────────────

if ( ! function_exists( 'handle_newsletter_submit' ) ) {

    function handle_newsletter_submit() {

        $recaptcha = defined('SECRET_API_RECAPTCHA') ? SECRET_API_RECAPTCHA : '';
        $mailchimp = defined('KEY_API_MAILCHIMP') ? KEY_API_MAILCHIMP : '';

        $email = sanitize_email($_POST['email']);
        $token = $_POST['recaptcha_token'];

        // Validar email
        if (!is_email($email)) {
            wp_send_json_error([
                'status'   => 'invalid_email',
                'response' => 'Please enter a valid email address.'
            ]);
        }

        // 1. Verificar reCAPTCHA
        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $recaptcha,
                'response' => $token
            ]
        ]);

        $result = json_decode(wp_remote_retrieve_body($response), true);

        if (!$result['success'] || $result['score'] < 0.5) {
            wp_send_json_error([
                'status'   => 'captcha_failed',
                'response' => 'Security verification failed. Please try again.'
            ]);
        }

        // 2. Enviar a Mailchimp
        $api_key         = $mailchimp;
        $list_id         = 'bed36c7058';
        $dc              = substr($api_key, strpos($api_key, '-') + 1);
        $subscriber_hash = md5(strtolower($email));
        $url             = "https://$dc.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

        $body = json_encode([
            'email_address' => $email,
            'status'        => 'subscribed',
            'status_if_new' => 'subscribed'
        ]);

        $args = [
            'method'  => 'PUT',
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
                'Content-Type'  => 'application/json'
            ],
            'body'    => $body,
            'timeout' => 30
        ];

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            wp_send_json_error([
                'status'   => 'server_error',
                'response' => 'Server connection error.',
            ]);
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        // 3. Manejo de respuestas
        switch ($response_code) {
            case 200:
                wp_send_json_success([
                    'status'   => 'updated',
                    'response' => 'Welcome back! Your subscription is now active.'
                ]);
                break;

            case 201:
                wp_send_json_success([
                    'status'   => 'created',
                    'response' => 'You have been successfully subscribed.'
                ]);
                break;

            case 400:
                if (isset($response_body['title'])) {
                    switch ($response_body['title']) {
                        case 'Member Exists':
                            $error_msg = 'You are already subscribed.';
                            break;
                        case 'Invalid Resource':
                            $error_msg = 'Invalid email address.';
                            break;
                        default:
                            $error_msg = 'Error processing the subscription.';
                            break;
                    }
                    wp_send_json_error([
                        'status'   => 'exists_or_invalid',
                        'response' => $error_msg
                    ]);
                } else {
                    wp_send_json_error([
                        'status'   => 'bad_request',
                        'response' => 'Bad request.'
                    ]);
                }
                break;

            default:
                wp_send_json_error([
                    'status'   => 'unknown_error',
                    'response' => 'Unexpected error. Please try again later.',
                    'code'     => $response_code
                ]);
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Blog — Load More posts
// ─────────────────────────────────────────────────────────────────────────────

if ( ! function_exists( 'pm_load_more_posts_ajax' ) ) {

    function pm_load_more_posts_ajax() {
        check_ajax_referer( 'pm_load_more', 'nonce' );

        $per_page    = 8;
        $page        = max( 1, absint( $_POST['page'] ?? 1 ) );
        $exclude_raw = isset( $_POST['exclude'] ) ? sanitize_text_field( wp_unslash( $_POST['exclude'] ) ) : '';
        $exclude     = $exclude_raw ? array_filter( array_map( 'absint', explode( ',', $exclude_raw ) ) ) : array();
        $category_id = absint( $_POST['category_id'] ?? 0 );
        $lang        = isset( $_POST['lang'] ) ? sanitize_key( $_POST['lang'] ) : '';

        $args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $per_page,
            'paged'               => $page,
            'ignore_sticky_posts' => true,
        );

        if ( ! empty( $exclude ) ) {
            $args['post__not_in'] = $exclude;
        }

        if ( $category_id ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                ),
            );
        }

        if ( $lang && function_exists( 'pll_current_language' ) ) {
            $args['lang'] = $lang;
        }

        $query    = new WP_Query( $args );
        $has_more = $query->found_posts > ( $page * $per_page );

        if ( ! $query->have_posts() ) {
            wp_send_json_success( array( 'html' => '', 'has_more' => false ) );
        }

        ob_start();
        foreach ( $query->posts as $post ) {
            $post_id        = (int) $post->ID;
            $post_title     = get_the_title( $post_id );
            $post_permalink = get_permalink( $post_id );
            $post_terms     = get_the_terms( $post_id, 'category' );
            $post_term_name = ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) ? $post_terms[0]->name : '';
            ?>
            <div class="col-12 col-lg-6 mb-4 mb-lg-5" data-blog-listing-grid-item>
                <article class="blog-listing__card card">
                    <a href="<?php echo esc_url( $post_permalink ); ?>" class="blog-listing__card-media-link" aria-label="<?php echo esc_attr( $post_title ); ?>">
                        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                            <?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'blog-listing__card-image card-img-left example-card-img-responsive' ) ); ?>
                        <?php else : ?>
                            <span class="blog-listing__card-image blog-listing__card-image--placeholder card-img-left example-card-img-responsive"></span>
                        <?php endif; ?>
                    </a>
                    <div class="blog-listing__card-content card-body d-flex flex-column justify-content-center">
                        <?php if ( $post_term_name ) : ?>
                            <p class="blog-listing__card-taxonomy card-text"><?php echo esc_html( $post_term_name ); ?></p>
                        <?php endif; ?>
                        <h3 class="blog-listing__card-title card-title h5 h4-sm">
                            <a href="<?php echo esc_url( $post_permalink ); ?>"><?php echo esc_html( $post_title ); ?></a>
                        </h3>
                        <a href="<?php echo esc_url( $post_permalink ); ?>" class="blog-listing__card-link card-text">Read more</a>
                    </div>
                </article>
            </div>
            <?php
        }
        $html = ob_get_clean();

        wp_send_json_success( array( 'html' => $html, 'has_more' => $has_more ) );
    }
}
