<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://shapedplugin.com/
 * @since      1.0.0
 *
 * @package    Woo_Gallery_Slider
 * @subpackage Woo_Gallery_Slider/public
 * @author     ShapedPlugin <support@shapedplugin.com>
 */
class Woo_Gallery_Slider_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Slider settings
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $settings The settings of Slider
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = get_option( 'wcgs_settings' );

		spl_autoload_register( array( $this, 'autoload_class' ) );
		spl_autoload_register( array( $this, 'autoload_trait' ) );

		add_action( 'wp_ajax_wcgs_action', array( $this, 'wcgs_ajax_request_function' ) );
		add_action( 'wp_ajax_nopriv_wcgs_action', array( $this, 'wcgs_ajax_request_function' ) );

		add_action( 'wp_ajax_wcgs_reset_action', array( $this, 'wcgs_ajax_request_reset_function' ) );
		add_action( 'wp_ajax_nopriv_wcgs_reset_action', array( $this, 'wcgs_ajax_request_reset_function' ) );

		new WCGS_Public_Style( $this->settings );
		add_action( 'wp_enqueue_scripts', array( 'WCGS_Public_Style', 'wcgs_stylesheet_include' ) );
		add_action( 'wp_print_scripts', array( $this, 'dequeue_script' ), 100 );
	}

	/**
	 * Autoload class files on demand
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $class requested class name.
	 */
	private function autoload_class( $class ) {
		$name = explode( '_', $class );
		if ( isset( $name[2] ) ) {
			$class_name        = strtolower( $name[2] );
			$spto_config_paths = array( 'partials' );
			foreach ( $spto_config_paths as $sptp_path ) {
				$filename = plugin_dir_path( __FILE__ ) . '/' . $sptp_path . '/class/class-public-' . $class_name . '.php';
				if ( file_exists( $filename ) ) {
					require_once $filename;
				}
			}
		}
	}

	/**
	 * Autoload trait files on demand
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $trait requested class name.
	 */
	private function autoload_trait( $trait ) {
		$name = explode( '_', $trait );
		if ( isset( $name[2] ) ) {
			$trait_name        = strtolower( $name[2] );
			$spto_config_paths = array( 'partials' );
			foreach ( $spto_config_paths as $sptp_path ) {
				$filename = plugin_dir_path( __FILE__ ) . '/' . $sptp_path . '/trait/trait-public-' . $trait_name . '.php';
				if ( file_exists( $filename ) ) {
					require_once $filename;
				}
			}
		}
	}

	/**
	 * Remove woocommerce_show_product_images and add wcgs function
	 *
	 * @since 1.0.0
	 */
	public function remove_gallery_and_product_images() {
		if ( is_product() && false === woo_gallery_slider_pro_is_activate() ) {
			// remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			// remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 40 );
			// add_action( 'woocommerce_before_single_product_summary', array( $this, 'wcgs_woocommerce_show_product_images' ), 20);
			add_filter( 'wc_get_template', array( $this, 'wpgs_gallery_template_part_override' ), 30, 2 );
		}
	}

	public function wpgs_gallery_template_part_override( $template, $template_name ) {

		$old_template = $template;

		if ( $template_name == 'single-product/product-image.php' ) {
			$template = WOO_GALLERY_SLIDER_PATH . '/public/partials/product-images.php';
		}

		return $template;
	}

	/**
	 * When variation change this method do the work
	 *
	 * @since 1.0.0
	 */
	public function wcgs_ajax_request_function() {
		if ( woo_gallery_slider_pro_is_activate() ) {
			return;
		}

		$nonce = ( ! empty( $_POST['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( 'variable_nonce' !== $nonce ) {
			exit;
		}

		$gallery    = array();
		$product_id = isset( $_POST['productID'] ) ? sanitize_text_field( wp_unslash( $_POST['productID'] ) ) : null;
		// This "POST" requests is sanitizing in the below foreach. see #L153
		$variation_array_raw = isset( $_POST['variationsArray'] ) ? $_POST['variationsArray'] : null;
		if ( $variation_array_raw ) {
			$include_feature_image = isset( $this->settings['include_feature_image'] ) ? $this->settings['include_feature_image'] : false;

			if ( $include_feature_image ) {
				$feature_image_id = get_post_thumbnail_id( $product_id );
				array_push(
					$gallery,
					wcgs_image_meta( $feature_image_id )
				);
			}
			$variation_array = array();
			foreach ( $variation_array_raw as $key => $variation ) {
				$variation_array[ sanitize_text_field( wp_unslash( $variation['attributeName'] ) ) ] = sanitize_text_field( wp_unslash( $variation['attributeValue'] ) );
			}
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
				foreach ( $variation_array as $key => $value ) {
					$variation_value          = get_post_meta( $variation->ID, $key, true );
					$_temp_variations[ $key ] = $variation_value;
				}
				if ( $_temp_variations == $variation_array ) {
					$variations[] = $variation->ID;
				}
			}
			foreach ( $variations as $key => $value ) {
				$image_id = get_post_thumbnail_id( $value );
				array_push(
					$gallery,
					wcgs_image_meta( $image_id )
				);

				$woo_gallery_slider = get_post_meta( $value, 'woo_gallery_slider', true );
				$gallery_arr        = substr( $woo_gallery_slider, 1, -1 );
				$gallery_multiple   = strpos( $gallery_arr, ',' ) ? true : false;
				if ( $gallery_multiple ) {
					$gallery_array = explode( ',', $gallery_arr );
					$count         = 1;
					foreach ( $gallery_array as $gallery_item ) {
						if ( 2 >= $count ) {
							array_push(
								$gallery,
								wcgs_image_meta( $gallery_item )
							);
						}
						$count++;
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
			$product     = wc_get_product( $product_id );
			$gallery_ids = $product->get_gallery_image_ids();
			$image_id    = $product->get_image_id();
			array_push(
				$gallery,
				wcgs_image_meta( $image_id )
			);
			foreach ( $gallery_ids as $key => $gallery_image_id ) {
				array_push(
					$gallery,
					wcgs_image_meta( $gallery_image_id )
				);
			}
		}
		wp_send_json( $gallery );
		wp_die();
	}


	/**
	 * WCGS product image area method
	 *
	 * @since 1.0.0
	 */
	public function wcgs_woocommerce_show_product_images() {
		include WOO_GALLERY_SLIDER_PATH . '/public/partials/product-images.php';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Gallery_Slider_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Gallery_Slider_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( is_singular( 'product' ) ) {
			wp_enqueue_style( 'wcgs-font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', $this->version, 'all' );
			wp_enqueue_style( 'wcgs-slick-css', plugin_dir_url( __FILE__ ) . 'css/slick.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wcgs-fancybox', plugin_dir_url( __FILE__ ) . 'css/jquery.fancybox.min.css', array(), $this->version, 'all' );
			if ( woo_gallery_slider_pro_is_activate() ) {
				wp_enqueue_style( $this->plugin_name, WP_PLUGIN_URL . '/woo-gallery-slider-pro/public/css/woo-gallery-slider-public.css', array(), $this->version, 'all' );
			} else {
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-gallery-slider-public.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Dequeue scripts for oceanwp theme support.
	 *
	 * @return void
	 */
	public function dequeue_script() {
		if ( is_singular( 'product' ) ) {
			wp_dequeue_script( 'magnific-popup' );
			wp_dequeue_script( 'oceanwp-lightbox' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Gallery_Slider_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Gallery_Slider_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( is_singular( 'product' ) ) {
			wp_enqueue_script( 'wcgs-slick-js', plugin_dir_url( __FILE__ ) . 'js/slick.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'wcgs-fancybox', plugin_dir_url( __FILE__ ) . 'js/jquery.fancybox.min.js', array( 'jquery' ), $this->version, true );
			if ( woo_gallery_slider_pro_is_activate() ) {
				wp_enqueue_script( $this->plugin_name, WP_PLUGIN_URL . '/woo-gallery-slider-pro/public/js/woo-gallery-slider-public.js', array( 'jquery' ), $this->version, true );
			} else {
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-gallery-slider-public.js', array( 'jquery', 'wcgs-slick-js' ), $this->version, true );
			}

			wp_localize_script(
				$this->plugin_name,
				'ajax_object',
				array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'nonce'         => 'variable_nonce',
					'wcgs_settings' => get_option( 'wcgs_settings' ),
				)
			);

		}
	}

}
if ( ! function_exists( 'wcgs_image_meta' ) ) {
	function wcgs_image_meta( $image_id ) {
		$settings      = get_option( 'wcgs_settings' );
		$image_size    = isset( $settings['image_sizes'] ) ? $settings['image_sizes'] : '';
		$thumb_size    = isset( $settings['thumbnails_sizes'] ) ? $settings['thumbnails_sizes'] : '';
		$image_url     = wp_get_attachment_url( $image_id );
		$image_caption = wp_get_attachment_caption( $image_id );

		// Thumb crop size.
		$thumb_width  = isset( $settings['thumb_crop_size']['width'] ) ? $settings['thumb_crop_size']['width'] : '';
		$thumb_height = isset( $settings['thumb_crop_size']['height'] ) ? $settings['thumb_crop_size']['height'] : '';
		$thumb_crop   = isset( $settings['thumb_crop_size']['unit'] ) ? $settings['thumb_crop_size']['unit'] : '';

		$image_full_src = wp_get_attachment_image_src( $image_id, 'full' );
		$sized_thumb    = wp_get_attachment_image_src( $image_id, $thumb_size )[0];
		$sized_image    = wp_get_attachment_image_src( $image_id, $image_size )[0];
		if ( ! empty( $image_url ) ) {
			return array(
				'url'       => $sized_image,
				'full_url'  => $image_url,
				'thumb_url' => $sized_thumb,
				'cap'       => $image_caption,
			);
		}
	}
}