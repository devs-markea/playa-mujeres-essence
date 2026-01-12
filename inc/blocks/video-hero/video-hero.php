<?php
/**
 * Template: PM Video Hero Block
 *
 * @var array  $block      Block settings and attributes.
 * @var string $content    Block inner HTML (empty).
 * @var bool  $is_preview  True during editor preview.
 * @var int   $post_id     Post ID this block is saved to.
 */

$block_id = 'pm-video-hero-' . $block['id'];

$class_name = 'pm-video-hero-block';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// Campos ACF
$video_url            = get_field( 'video_url' );
$title                = get_field( 'title' );
$description          = get_field( 'description' );
$button_label         = get_field( 'button_label' );
$button_link          = get_field( 'button_link' );
$button_target        = get_field( 'button_target' ) ?: '_self';
$play_video_in_modal  = (bool) get_field( 'play_video_in_modal' );
$enable_header_overlap = (bool) get_field( 'enable_header_overlap' );

if ( $enable_header_overlap ) {
	$class_name .= ' pm-video-hero--overlap-header';
}
if ( $play_video_in_modal ) {
	$class_name .= ' pm-video-hero--modal-enabled';
}
?>
<section
	id="<?php echo esc_attr( $block_id ); ?>"
	class="<?php echo esc_attr( $class_name ); ?>"
	data-pm-video-hero
>
	<div class="pm-video-hero__background">
		<?php if ( $video_url ) : ?>
<iframe width="560" height="315" src="https://www.youtube.com/embed/ebqMoLyPs9M?si=MljSCyHSTJEuFZ1W" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
		<?php endif; ?>
		<div class="pm-video-hero__overlay"></div>
	</div>

	<div class="pm-video-hero__content container">
		<?php if ( $title ) : ?>
			<h1 class="pm-video-hero__title">
				<?php echo esc_html( $title ); ?>
			</h1>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<p class="pm-video-hero__description">
				<?php echo esc_html( $description ); ?>
			</p>
		<?php endif; ?>

		<?php
		// Si Play Video in Modal está activo y hay video, el botón abre el modal.
		if ( $button_label ) :

			// Botón para modal de video
			if ( $play_video_in_modal && $video_url ) : ?>
				<button
					type="button"
					class="pm-video-hero__button"
					data-pm-video-hero-btn
					data-video-src="<?php echo esc_url( $video_url ); ?>"
				>
					<?php echo esc_html( $button_label ); ?>
				</button>

			<?php
			// Botón como link normal
			elseif ( $button_link ) : ?>
				<a
					class="pm-video-hero__button"
					href="<?php echo esc_url( $button_link ); ?>"
					target="<?php echo esc_attr( $button_target ); ?>"
					rel="<?php echo ( '_blank' === $button_target ) ? 'noopener noreferrer' : 'nofollow'; ?>"
				>
					<?php echo esc_html( $button_label ); ?>
				</a>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( $is_preview ) : ?>
			<span class="pm-video-hero__badge-preview">
				Vista previa – Video Hero
			</span>
		<?php endif; ?>
	</div>

	<?php if ( $play_video_in_modal && $video_url ) : ?>
		<div
			class="pm-video-hero__modal"
			data-pm-video-hero-modal
			hidden
		>
			<div
				class="pm-video-hero__modal-backdrop"
				data-pm-video-hero-close
			></div>

			<div class="pm-video-hero__modal-dialog">
				<button
					type="button"
					class="pm-video-hero__modal-close"
					data-pm-video-hero-close
					aria-label="<?php esc_attr_e( 'Close video', 'pm-essence' ); ?>"
				>
					×
				</button>

				<video
					class="pm-video-hero__modal-video"
					controls
				></video>
			</div>
		</div>
	<?php endif; ?>
</section>