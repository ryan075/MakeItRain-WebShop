<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: gallery
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'WCGS_Field_gallery' ) ) {
  class WCGS_Field_gallery extends WCGS_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'add_title'   => esc_html__( 'Add Gallery', 'woo-gallery-slider' ),
        'edit_title'  => esc_html__( 'Edit Gallery', 'woo-gallery-slider' ),
        'clear_title' => esc_html__( 'Clear', 'woo-gallery-slider' ),
      ) );

      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo $this->field_before();

      echo '<ul>';

      if( ! empty( $this->value ) ) {

        $values = explode( ',', $this->value );

        foreach ( $values as $id ) {
          $attachment = wp_get_attachment_image_src( $id, 'thumbnail' );
          echo '<li><img src="'. $attachment[0] .'" /></li>';
        }

      }

      echo '</ul>';
      echo '<a href="#" class="button button-primary wcgs-button">'. $args['add_title'] .'</a>';
      echo '<a href="#" class="button wcgs-edit-gallery'. $hidden .'">'. $args['edit_title'] .'</a>';
      echo '<a href="#" class="button wcgs-warning-primary wcgs-clear-gallery'. $hidden .'">'. $args['clear_title'] .'</a>';
      echo '<input type="text" name="'. $this->field_name() .'" value="'. $this->value .'"'. $this->field_attributes() .'/>';

      echo $this->field_after();

    }

  }
}
