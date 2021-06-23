<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: Custom_import
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SPWPS_Field_custom_import' ) ) {
	class SPWPS_Field_custom_import extends SPWPS_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}
		public function render() {
			echo $this->field_before();
			$wps_shortcodelink = admin_url( 'edit.php?post_type=sp_wps_shortcodes' );
				echo '<p><input type="file" id="import" accept=".json"></p>';
				echo '<p><button type="button" class="import">Import</button></p>';
				echo '<a id="wps_shortcode_link_redirect" href="' . $wps_shortcodelink . '"></a>';
			echo $this->field_after();
		}
	}
}
