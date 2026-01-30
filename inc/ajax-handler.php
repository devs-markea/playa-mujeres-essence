<?php
/*
Plugin Name: Newsletter Recaptcha + Mailchimp
*/

if ( ! function_exists( 'handle_newsletter_submit' ) ) {

    function handle_newsletter_submit() {
        
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
                'secret' => '',
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
        $api_key = '';
        $list_id = '';
        $dc = substr($api_key, strpos($api_key, '-') + 1);
        
        // Crear hash del email (requerido para PUT)
        $subscriber_hash = md5(strtolower($email));
        
        // URL para el miembro específico
        $url = "https://$dc.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

        $body = json_encode([
            'email_address' => $email,
            'status' => 'subscribed',
            'status_if_new' => 'subscribed'
        ]);

        $args = [
            'method' => 'PUT',
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
                'Content-Type' => 'application/json'
            ],
            'body' => $body,
            'timeout' => 30
        ];

        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            wp_send_json_error([
                'status'   => 'server_error',
                'response' => 'Server connection error.',
                'debug' => $response->get_error_message()
            ]);
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        // 3. Manejo de respuestas
        switch ($response_code) {
            case 200:
                // Miembro actualizado (reactivado o actualizado)
                wp_send_json_success([
                    'status'   => 'updated',
                    'response' => 'Welcome back! Your subscription is now active.'
                ]);
                break;
                
            case 201:
                // Nuevo miembro creado
                wp_send_json_success([
                    'status'   => 'created',
                    'response' => 'You have been successfully subscribed.'
                ]);
                break;
                
            case 400:
                // Error en la solicitud
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
                    'code' => $response_code
                ]);
        }
    }
}
