<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Abstract Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'WCGS_Abstract' ) ) {
  abstract class WCGS_Abstract {

    public $abstract     = '';
    public $output_css   = '';
    public $typographies = array();

    public function __construct() {

      // Check for embed custom css styles
      if( ! empty( $this->args['output_css'] ) ) {
        add_action( 'wp_head', array( &$this, 'add_output_css' ), 100 );
      }

    }

    public function add_output_css() {

      $this->output_css = apply_filters( "wcgs_{$this->unique}_output_css", $this->output_css, $this );

      if ( ! empty( $this->output_css ) ) {
        echo '<style type="text/css">'.  $this->output_css . '</style>';
      }

    }

  }
}
