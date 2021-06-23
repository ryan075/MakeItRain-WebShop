<?php
global $product;
$settings          = get_option( 'wcgs_settings' );
$gallery           = array();
$product_id        = $product->get_id();
$default_variation = $product->get_default_attributes();
if ( ! empty( $default_variation ) ) {
	$include_feature_image = isset( $settings['include_feature_image'] ) ? $settings['include_feature_image'] : false;
	if ( $include_feature_image ) {
		// $feature_image_src = get_the_post_thumbnail_url( $product_id );
		array_push(
			$gallery,
			wcgs_image_meta( $product_id )
		);
	}
	$selected_variations     = array();
	$attributes              = array();
	$product_variation_args  = array(
		'post_type'      => 'product_variation',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'post_parent'    => $product_id,
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
	);
	$product_variation_array = get_posts( $product_variation_args );
	$variations              = array();
	foreach ( $product_variation_array as $variation ) {
		$_temp_variations = array();
		foreach ( $default_variation as $key => $value ) {
			$variation_value          = get_post_meta( $variation->ID, 'attribute_' . $key, true );
			$_temp_variations[ $key ] = $variation_value;
		}
		if ( $_temp_variations == $default_variation ) {
			$variations[] = $variation->ID;
		}
	}
	foreach ( $variations as $variation ) {
		$image_id = get_post_thumbnail_id( $variation );

		array_push(
			$gallery,
			wcgs_image_meta( $image_id )
		);

		$woo_gallery_slider = get_post_meta( $variation, 'woo_gallery_slider', true );
		$gallery_arr        = substr( $woo_gallery_slider, 1, -1 );
		$gallery_multiple   = strpos( $gallery_arr, ',' ) ? true : false;
		if ( $gallery_multiple ) {
			$gallery_array = explode( ',', $gallery_arr );
			foreach ( $gallery_array as $gallery_item ) {
				/* $image_url     = wp_get_attachment_url( $gallery_item ); */
				array_push(
					$gallery,
					wcgs_image_meta( $gallery_item )
				);
			}
		} else {
			$gallery_array = $gallery_arr;
			array_push(
				$gallery,
				wcgs_image_meta( $gallery_array )
			);
		}
	}
} else {
	$image_id              = $product->get_image_id();
	$include_feature_image = isset( $settings['include_feature_image'] ) ? $settings['include_feature_image'] : false;
	if ( $include_feature_image && $image_id ) {
		array_push(
			$gallery,
			wcgs_image_meta( $image_id )
		);
	}
	$gallery_image_source = isset( $settings['gallery_image_source'] ) ? $settings['gallery_image_source'] : 'uploaded';
	if ( 'attached' === $gallery_image_source ) {
		$post                = get_post( $product_id );
		$wcgs_post_content   = $post->post_content;
		$wcgs_search_pattern = '~<img [^\>]*\ />~';
		preg_match_all( $wcgs_search_pattern, $wcgs_post_content, $post_images );
		$wcgs_number_of_images = count( $post_images[0] );
		if ( $wcgs_number_of_images > 0 ) {
			foreach ( $post_images[0] as $image ) {
				$class_start     = substr( $image, strpos( $image, 'class="' ) + 7 );
				$class_end       = substr( $class_start, 0, strpos( $class_start, '" ' ) );
				$image_class_pos = strpos( $class_end, 'wp-image-' );
				$image_class_tmp = substr( $class_end, $image_class_pos + 9 );
				array_push(
					$gallery,
					wcgs_image_meta( $image_class_tmp )
				);
			}
		}
	} else {
		$attachment_ids = $product->get_gallery_image_ids();
		foreach ( $attachment_ids as $attachment_id ) {
			/* $image_url     = wp_get_attachment_url( $attachment_id ); */
			array_push(
				$gallery,
				wcgs_image_meta( $attachment_id )
			);
		}
	}
}
?>
<div id="wpgs-gallery" class="woocommerce-product-gallery horizontal" style='min-width: <?php echo esc_attr( $settings['gallery_width'] ); ?>%;' data-id="<?php echo $product_id; ?>">
	<div class="gallery-navigation-carousel horizontal always">
		<?php foreach ( $gallery as $slide ) { ?>
			<div class="wcgs-thumb">
				<img alt="<?php echo esc_html( $slide['cap'] ); ?>" src="<?php echo esc_url( $slide['thumb_url'] ); ?>" data-image="<?php echo esc_url( $slide['full_url'] ); ?>" />
			</div>
		<?php	} ?>

	</div>
	<div class="wcgs-carousel horizontal">
			<?php
			foreach ( $gallery as $slide ) {
				?>
					<a class="wcgs-slider-image" data-fancybox="view" href="<?php echo esc_url( $slide['full_url'] ); ?>">
						<img alt="<?php echo esc_html( $slide['cap'] ); ?>" src="<?php echo esc_url( $slide['url'] ); ?>" data-image="<?php echo esc_url( $slide['full_url'] ); ?>" />
					</a>
				<?php
			}
			?>
	</div>
	<?php
	if ( $settings['preloader'] ) {
		?>
	<div class="wcgs-gallery-preloader" style="opacity: 1; z-index: 9999;"></div>
	<?php } ?>
</div>
<?php
