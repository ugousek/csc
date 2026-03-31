<?php
/**
 * Performance & security optimizations.
 *
 * @package Xevos\CyberTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Increase limits for image processing (large Figma exports).
 */
@ini_set( 'max_execution_time', 600 );
@ini_set( 'memory_limit', '512M' );

/**
 * Allow SVG uploads in Media Library.
 */
add_filter( 'upload_mimes', function ( array $mimes ): array {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
} );

/**
 * Fix SVG display in Media Library grid/list views.
 */
add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes ) {
	$ext = pathinfo( $filename, PATHINFO_EXTENSION );
	if ( $ext === 'svg' ) {
		$data['type'] = 'image/svg+xml';
		$data['ext']  = 'svg';
	}
	return $data;
}, 10, 4 );

/**
 * Render SVG thumbnails in admin Media Library.
 */
add_filter( 'wp_prepare_attachment_for_js', function ( $response ) {
	if ( $response['mime'] === 'image/svg+xml' ) {
		$response['sizes'] = [
			'full' => [
				'url'    => $response['url'],
				'width'  => 200,
				'height' => 200,
			],
		];
	}
	return $response;
} );


/**
 * EWWW Image Optimizer — force WebP delivery + lazy load settings.
 */

/* Allow WebP uploads */
add_filter( 'upload_mimes', function ( array $mimes ): array {
	$mimes['webp'] = 'image/webp';
	return $mimes;
}, 20 );

/* Tell EWWW to also scan theme assets folder */
add_filter( 'ewww_image_optimizer_aux_paths', function ( array $paths ): array {
	$paths[] = get_template_directory() . '/assets/img/';
	return $paths;
} );

/* Force EWWW to generate all custom sizes on upload */
add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {
	$sizes['xevos-hero']      = [ 'width' => 1920, 'height' => 800, 'crop' => true ];
	$sizes['xevos-hero-half'] = [ 'width' => 910, 'height' => 770, 'crop' => true ];
	$sizes['xevos-card']      = [ 'width' => 507, 'height' => 293, 'crop' => true ];
	$sizes['xevos-card-sm']   = [ 'width' => 448, 'height' => 260, 'crop' => true ];
	$sizes['xevos-thumbnail'] = [ 'width' => 400, 'height' => 300, 'crop' => true ];
	$sizes['xevos-lektor']    = [ 'width' => 240, 'height' => 240, 'crop' => true ];
	$sizes['xevos-article']   = [ 'width' => 1200, 'height' => 660, 'crop' => true ];
	return $sizes;
} );

/**
 * Disable default WP sizes we don't need (save space).
 */
add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {
	unset( $sizes['medium_large'] ); // 768px — not used
	unset( $sizes['1536x1536'] );    // 2x medium_large
	unset( $sizes['2048x2048'] );    // 2x large
	return $sizes;
}, 30 );


/**
 * Admin tool: Import theme assets into WP Media Library.
 * This triggers thumbnail generation for all registered image sizes.
 *
 * wp-admin → Nástroje → Import theme obrázků
 */
add_action( 'admin_menu', function () {
	add_management_page(
		'Import theme obrázků',
		'Import theme obrázků',
		'manage_options',
		'xevos-import-theme-images',
		'xevos_import_theme_images_page'
	);
} );

function xevos_import_theme_images_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$imported = 0;
	$skipped  = 0;
	$errors   = [];

	if ( isset( $_POST['xevos_import_images'] ) && check_admin_referer( 'xevos_import_images' ) ) {

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$img_dir = get_template_directory() . '/assets/img';
		$files   = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $img_dir, RecursiveDirectoryIterator::SKIP_DOTS )
		);

		foreach ( $files as $file ) {
			if ( ! $file->isFile() ) continue;

			$ext = strtolower( $file->getExtension() );
			if ( ! in_array( $ext, [ 'png', 'jpg', 'jpeg', 'webp' ], true ) ) continue;

			$rel_path = str_replace( $img_dir, '', $file->getPathname() );
			$rel_path = ltrim( str_replace( '\\', '/', $rel_path ), '/' );

			/* Skip if already imported (check by filename in title) */
			$existing = get_posts( [
				'post_type'   => 'attachment',
				'title'       => 'theme-asset/' . $rel_path,
				'numberposts' => 1,
				'fields'      => 'ids',
			] );

			if ( $existing ) {
				$skipped++;
				continue;
			}

			/* Copy file to uploads dir */
			$upload = wp_upload_bits( basename( $file->getPathname() ), null, file_get_contents( $file->getPathname() ) );

			if ( $upload['error'] ) {
				$errors[] = $rel_path . ': ' . $upload['error'];
				continue;
			}

			$filetype = wp_check_filetype( $upload['file'] );

			$attachment_id = wp_insert_attachment( [
				'post_title'     => 'theme-asset/' . $rel_path,
				'post_mime_type' => $filetype['type'],
				'post_status'    => 'inherit',
			], $upload['file'] );

			if ( is_wp_error( $attachment_id ) ) {
				$errors[] = $rel_path . ': ' . $attachment_id->get_error_message();
				continue;
			}

			/* Generate all thumbnail sizes */
			$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
			wp_update_attachment_metadata( $attachment_id, $metadata );

			$imported++;
		}
	}
	?>
	<div class="wrap">
		<h1>Import theme obrázků do Media Library</h1>

		<?php if ( $imported > 0 || $skipped > 0 ) : ?>
			<div class="notice notice-success">
				<p><strong>Hotovo!</strong> Importováno: <?php echo $imported; ?>, přeskočeno (už existuje): <?php echo $skipped; ?></p>
				<?php if ( ! empty( $errors ) ) : ?>
					<p>Chyby:</p>
					<ul><?php foreach ( $errors as $e ) : ?><li><?php echo esc_html( $e ); ?></li><?php endforeach; ?></ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<p>Zkopíruje všechny PNG/JPG/WebP obrázky z <code>assets/img/</code> do WP Media Library a vygeneruje všechny registrované velikosti thumbnailů:</p>
		<ul>
			<li><strong>xevos-hero</strong> — 1920×800</li>
			<li><strong>xevos-hero-half</strong> — 910×770</li>
			<li><strong>xevos-card</strong> — 507×293</li>
			<li><strong>xevos-card-sm</strong> — 448×260</li>
			<li><strong>xevos-thumbnail</strong> — 400×300</li>
			<li><strong>xevos-lektor</strong> — 240×240</li>
			<li><strong>xevos-article</strong> — 1200×660</li>
			<li>+ WordPress výchozí (thumbnail, medium, large)</li>
		</ul>

		<form method="post">
			<?php wp_nonce_field( 'xevos_import_images' ); ?>
			<input type="hidden" name="xevos_import_images" value="1">
			<?php submit_button( 'Importovat a vygenerovat thumbnaily', 'primary' ); ?>
		</form>
	</div>
	<?php
}
