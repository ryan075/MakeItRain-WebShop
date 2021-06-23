<?php

class WCGS_Public_Settings {
	use WCGS_Public_Default, WCGS_Public_fontsMap;

	public function __construct( $settings ) {
		if ( is_array( $settings ) ) {
			foreach ( $settings as $key => $value ) {
				$this->setData( $key, $value );
			}
		}
		$this->fonts_map_func();
	}

	public function setData( $key, $value ) {
		$is_arr = is_array( $value ) ? true : false;
		if ( $is_arr ) {
			$name_arr = explode( '_', $key );
			$name     = $name_arr[0] . '_' . $name_arr[1];
			foreach ( $value as $k => $v ) {
				$variable_name            = $name . '_' . $k;
				$default_value            = $variable_name;
				$this->{$name . '_' . $k} = isset( $v ) ? $v : $default_value;
			}
		} else {
			$this->{$key} = $value;
		}
	}

	public function fonts_map_func() {
		foreach ( $this->fonts_map as $key => $value ) {
			if ( $key === $this->navigation_icon ) {
				$this->navigation_left_icon  = $value[0];
				$this->navigation_right_icon = $value[1];
			}
			if ( $key === $this->thumbnailnavigation_icon ) {
				$this->thumbnailnavigation_left_icon  = $value[0];
				$this->thumbnailnavigation_right_icon = $value[1];
			}
		}
	}

}
