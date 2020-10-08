<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Thrive_Audio_Post_Soundcloud extends Thrive_Audio_Post_Format {

	public function get_defaults() {
		$defaults = [
			'url'          => [
				'type'        => 'textarea',
				'label'       => __( 'Audio Soundcloud url', THEME_DOMAIN ),
				'value'       => '',
				'placeholder' => 'Add a Soundcloud url.',
				'default'     => '',
			],
			'auto_play'    => [
				'type'    => 'checkbox',
				'label'   => __( 'Autoplay', THEME_DOMAIN ),
				'class'   => 'thrive-autoplay-checkbox',
				'value'   => '',
				'default' => '',
				'notice'  => '',
			],
			'show_artwork' => [
				'type'    => 'checkbox',
				'label'   => __( 'Hide Artwork', THEME_DOMAIN ),
				'value'   => 'true',
				'default' => '',
				'notice'  => '',
			],
			'show_user'    => [
				'type'    => 'checkbox',
				'label'   => __( 'Hide User', THEME_DOMAIN ),
				'value'   => 'true',
				'default' => '',
				'notice'  => '',
			],
		];

		return $defaults;
	}

	public function render() {
		$options    = $this->get_audio_options_meta();
		$src        = $options['url']['value'];
		$attributes = [];
		/* if no src is set, return empty */
		if ( empty( $src ) ) {
			return Thrive_Audio_Post_Format_Main::render_placeholder();

		}

		$attributes['show_artwork'] = $options['show_artwork']['value']==='false' ? 'false':'true';

		$attributes['show_user'] = $options['show_user']['value']==='false' ? 'false':'true';

		$attr = [
			'src'           => THRIVE_THEME_SOUNDCLOUD_EMBED_URL . $src . '&' . http_build_query( $attributes, '', '&' ),
			'scrolling'     => 'no',
			'frameborder'   => 'no',
			'allow'         => 'autoplay',
			'width'         => '100%',
			'height'        => '100%',
			'data-provider' => 'soundcloud',
			'data-autoplay' => empty( $options['auto_play']['value'] ) ? 0 : 1,
		];

		$content = TCB_Utils::wrap_content( '', 'iframe', '', 'tcb-audio', $attr );
		$content = TCB_Utils::wrap_content( $content, 'div', '', 'tve_audio_container' );

		return $content;
	}

	public function render_options() {
		include THEME_PATH . '/inc/templates/admin/audio-post-format/soundcloud.php';
	}
}
