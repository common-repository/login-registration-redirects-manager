<?php
/**
 * Editor field class
 */

class LRR_Field_Editor {

	/**
	 * Editor field
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

		$wpautop       = $field->addon( 'wpautop' ) ? $field->addon( 'wpautop' ) : true;
		$media_buttons = $field->addon( 'media_buttons' ) ? $field->addon( 'media_buttons' ) : false;
		$textarea_rows = $field->addon( 'textarea_rows' ) ? $field->addon( 'textarea_rows' ) : 10;
		$teeny         = $field->addon( 'teeny' ) ? $field->addon( 'teeny' ) : false;

		$settings = array(
			'textarea_name' => $field->input_name(),
			'editor_css'    => null,
			'wpautop'       => $wpautop,
			'media_buttons' => $media_buttons,
			'textarea_rows' => $textarea_rows,
			'teeny'         => $teeny,
		);

		wp_editor( stripslashes( $field->value() ), $field->input_id(), $settings );

	}

	/**
	 * Sanitize input value
	 * @param  string $value Saved value
	 * @return string        Sanitized content
	 */
	public function sanitize( $value ) {

		return wp_kses_post( $value );

	}

}
