<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Thrive_Audio_Post_Custom extends Thrive_Audio_Post_Format {

	public function get_defaults() {
		$defaults = [
			'url'           => [
				'type'        => 'textarea',
				'label'       => __( 'Audio Custom Url', THEME_DOMAIN ),
				'value'       => '',
				'placeholder' => 'Add an audio URL from the Media tab, or upload an audio file.',
				'default'     => '',
			],
			'data-autoplay' => [
				'type'    => 'checkbox',
				'label'   => __( 'Autoplay', THEME_DOMAIN ),
				'class'   => 'thrive-autoplay-checkbox',
				'value'   => '',
				'default' => '',
				'notice'  => __( 'Note: Autoplay is muted by default.', THEME_DOMAIN ),
			],
			'loop'          => [
				'type'    => 'checkbox',
				'label'   => __( 'Loop', THEME_DOMAIN ),
				'value'   => '',
				'default' => '',
				'notice'  => '',
			],
			'controlsList'          => [
				'type'    => 'checkbox',
				'label'   => __( 'Allow Users to Download', THEME_DOMAIN ),
				'value'   => '',
				'default' => '',
				'notice'  => '',
			],
		];

		return $defaults;
	}

	public function render() {
		$options = $this->get_audio_options_meta();
		$src     = $options['url']['value'];

		/* if no src is set, return empty */
		if ( empty( $src ) ) {
			return Thrive_Audio_Post_Format_Main::render_placeholder();

		}

		/* if the src contains < or [, it's already a audio code -> call do_shortcode on it  */
		if ( strpos( $src, '<' ) !== false || strpos( $src, '[' ) !== false ) { //if embeded code or shortcode
			$content = do_shortcode( $src );
		} else {
			$attachment_id = attachment_url_to_postid( $src );
			$extra_attr    = 'playsinline';
			$attr          = [];
			if ( ! empty( $options['data-autoplay']['value'] ) ) {
				$attr['data-autoplay'] = 1;
			}
			if ( ! empty( $options['loop']['value'] ) ) {
				$extra_attr        .= ' loop';
				$attr['loop'] = 1;
			}
			if ( ! empty( $options['controlsList']['value'] ) ) {
				$attr['controlsList'] = 'nodownload';
			}

			$attr = array_merge( $attr, [
				'style'       => 'width:100%',
				'data-title'  => empty( $attachment_id ) ? get_the_title() : get_the_title( $attachment_id ),
				'data-id'     => $attachment_id,
				'data-provider' => 'custom',
				'controls'    => 'controls', /* this enables the control bar */
				$extra_attr   => null, /* these are added as a single attributes, without a corresponding value */
			] );

			$type = empty( $attachment_id ) ? get_post_mime_type() : get_post_mime_type( $attachment_id );

			$source_html = '<source src="' . $src . '" type="' . $type . '">';
			$audio_overlay = '<div class="audio_overlay"></div>';
			$audio_html  =  $audio_overlay . TCB_Utils::wrap_content( $source_html, 'audio', '', 'tcb-audio', $attr );
			$content     = TCB_Utils::wrap_content( $audio_html, 'div', '', 'tve_audio_container' );
		}

		return $content;
	}

	public function render_options() {
		include THEME_PATH . '/inc/templates/admin/audio-post-format/custom.php';
	}
}
