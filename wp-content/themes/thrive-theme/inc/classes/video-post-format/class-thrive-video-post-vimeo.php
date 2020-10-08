<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Thrive_Video_Post_Vimeo extends Thrive_Video_Post_Format {

	const EMBED_SRC = 'https://player.vimeo.com/video/';

	public function get_defaults() {
		$defaults = [
			'url'                => [
				'type'        => 'input',
				'label'       => __( 'Video Url', THEME_DOMAIN ),
				'value'       => '',
				'placeholder' => 'e.g. https://vimeo.com/[video_id]',
				'default'     => '',
			],
			'autoplay'           => [
				'type'    => 'checkbox',
				'label'   => __( 'Autoplay', THEME_DOMAIN ),
				'class'   => 'thrive-autoplay-checkbox',
				'value'   => '',
				'default' => '',
				'notice'  => __( 'Note: Autoplay is muted by default.', THEME_DOMAIN ),
			],
			'hide_user_image'    => [
				'type'     => 'checkbox',
				'label'    => __( 'Hide the user image', THEME_DOMAIN ),
				'value'    => '',
				'default'  => '',
				'alias'    => 'modestbranding',
				'inverted' => true,
			],
			'hide_byline'        => [
				'type'     => 'checkbox',
				'label'    => __( 'Hide the "by" line', THEME_DOMAIN ),
				'value'    => '',
				'default'  => '',
				'alias'    => 'byline',
				'inverted' => true,
			],
			'hide_title'         => [
				'type'     => 'checkbox',
				'label'    => __( 'Hide title bar', THEME_DOMAIN ),
				'value'    => '',
				'default'  => '',
				'alias'    => 'showinfo',
				'inverted' => true,
			],
			'start_time_minutes' => [
				'type'    => 'input',
				'label'   => '',
				'value'   => '',
				'default' => 0,
			],
			'start_time_seconds' => [
				'type'    => 'input',
				'label'   => '',
				'value'   => '',
				'default' => 0,
			],
		];

		return array_merge( Thrive_Video_Post_Format::get_general_defaults(), $defaults );
	}

	/**
	 * See the parent function for description.
	 *
	 * @param $has_thumbnail
	 *
	 * @return mixed|string
	 */
	public function render( $has_thumbnail ) {
		$options = $this->get_video_options_meta();
		$src     = $options['url']['value'];

		/* if no src is set, return empty */
		if ( empty( $src ) ) {
			return Thrive_Video_Post_Format_Main::render_placeholder();

		}

		$attr = [
			'src'             => $this->get_vimeo_embed_code( $src, $options, $has_thumbnail ),
			'data-src'        => $src,
			'class'           => 'tcb-video',
			'data-provider'   => Thrive_Video_Post_Format_Main::VIMEO,
			'allowfullscreen' => null,
			'frameborder'     => 0,
			'data-autoplay'   => $has_thumbnail || empty( $options['autoplay']['value'] ) ? 0 : 1,
			'data-code'       => '39129233',
		];

		return TCB_Utils::wrap_content( '', 'iframe', '', '', $attr );
	}

	/**
	 * @param $src
	 * @param $options
	 * @param $has_thumbnail
	 *
	 * @return string
	 */
	private function get_vimeo_embed_code( $src, $options ) {
		if ( ! preg_match( '/.com\/\D*(\d+)/', $src, $m ) ) {
			return '';
		}

		$video_id = $m[1];

		$src          = static::EMBED_SRC . $video_id;
		$query_string = $this->parse_query_attributes( $options );

		$src .= empty( $query_string ) ? '?' : ( '?' . $query_string );

		return $src;
	}

	/**
	 * Build the URL query string out of the options.
	 *
	 * @param $options
	 * @param $has_thumbnail
	 *
	 * @return string
	 */
	private function parse_query_attributes( $options ) {
		$video_query_attr = [];

		if ( ! empty ( $options['hide_user_image']['value'] ) ) {
			$video_query_attr['portait'] = 0;
		}
		if ( ! empty ( $options['hide_byline']['value'] ) ) {
			$video_query_attr['byline'] = 0;
		}
		if ( ! empty ( $options['hide_title']['value'] ) ) {
			$video_query_attr['title'] = 0;
		}

		$query_string = http_build_query( $video_query_attr, '', '&' );

		/* calculate the start time (format is #t=1m2s); This cannot be added as a normal query arg, it does not work when added with '&'. */
		$time = Thrive_Video_Post_Format::get_start_time( $options );

		if ( ! empty( $time ) ) {
			$query_string .= '#t=' . $time;
		}

		return $query_string;
	}

	public function render_options() {
		include THEME_PATH . '/inc/templates/admin/video-post-format/vimeo.php';
	}
}
