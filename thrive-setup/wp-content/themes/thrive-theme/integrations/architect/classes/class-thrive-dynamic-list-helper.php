<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class Thrive_Dynamic_List_Helper
 */
class Thrive_Dynamic_List_Helper {

	/**
	 * Generic function to get terms
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private static function get_terms( $args = [] ) {

		$args = array_merge( [
			'hide_empty' => false,
			'orderby'    => 'count',
			'order'      => 'DESC',
		], $args );

		$all_terms = get_terms( $args );
		$terms     = [];
		foreach ( $all_terms as $term ) {
			$terms[ $term->term_id ] = [
				'name' => $term->name,
				'url'  => get_term_link( $term ),
			];
		}

		return $terms;
	}

	/**
	 * Get categories for the dynamic list element
	 *
	 * @param array   $args
	 * @param Boolean $use_demo_content
	 *
	 * @return array
	 */
	public static function get_categories( $args = [], $use_demo_content = false ) {
		return static::get_terms( [
			'taxonomy' => $use_demo_content ? Thrive_Demo_Content::CATEGORY : 'category',
			'number'   => $args['limit'],
		] );
	}

	/**
	 * Get all tags for the dynamic list element
	 *
	 * @param         $args
	 * @param Boolean $use_demo_content
	 *
	 * @return array
	 */
	public static function get_tags( $args = [], $use_demo_content = false ) {
		return static::get_terms( [
			'taxonomy' => $use_demo_content ? Thrive_Demo_Content::TAG : 'post_tag',
			'number'   => $args['limit'],
		] );
	}

	/**
	 * Get authors for the dynamic list element
	 *
	 * @return array
	 */
	public static function get_authors() {
		$users = [];

		$all_with_posts = get_users( [ 'has_published_posts' => [ 'post', 'page' ] ] );

		foreach ( $all_with_posts as $user ) {
			$users[ $user->ID ] = [
				'name' => $user->get( 'display_name' ),
				'url'  => get_author_posts_url( $user->ID ),
			];
		}

		return $users;
	}

	/**
	 * Get monthly list. All the months in which posts were published are returned
	 *
	 * @return array
	 */
	public static function get_monthly_list( $args ) {
		$months = [];

		//send also the limit argument if this exists
		if ( ! empty( $args['limit'] ) ) {
			$params['limit'] = $args['limit'];
		}

		$params['echo'] = 0;
		$archives       = wp_get_archives( $params );

		if ( ! empty( $archives ) ) {
			$doc = new DOMDocument();

			if ( $doc->loadHTML( $archives ) ) {
				$links = $doc->getElementsByTagName( 'a' );

				foreach ( $links as $link ) {
					/* @var $link DOMElement */
					$months[] = [
						'name' => $link->nodeValue,
						'url'  => $link->getAttribute( 'href' ),
					];
				}
			}
		}

		return $months;
	}

	/**
	 * Get a list with all the pages
	 *
	 * @param      $args
	 * @param bool $use_demo_content
	 *
	 * @return array
	 */
	public static function get_pages( $args, $use_demo_content = false ) {
		$args ['post_type'] = 'page';

		return static::get_posts( $args, $use_demo_content );
	}

	/**
	 * Get a list with all the pages
	 *
	 * @param array $args
	 * @param bool  $use_demo_content
	 *
	 * @return array
	 */
	public static function get_posts( $args = [], $use_demo_content = false ) {
		$posts = [];

		if ( $use_demo_content ) {
			$args['post_type'] = Thrive_Demo_Content::POST_TYPE;
		} else {
			if ( empty( $args['post_type'] ) ) {
				$args['post_type'] = 'post';
			}
		}

		$all_posts = get_posts( [
			'post_type'      => $args['post_type'],
			'posts_per_page' => $args['limit'],
		] );

		foreach ( $all_posts as $post ) {
			$posts[ $post->ID ] = [
				'name' => get_the_title( $post ),
				'url'  => get_permalink( $post ),
			];
		}

		return $posts;
	}

	/**
	 * Return the last 5 comments
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public static function get_comments( $args = [] ) {
		$comments = [];
		$args     = [
			'status' => 'approve',
			'number' => $args['limit'],
		];

		foreach ( get_comments( $args ) as $comment ) {
			$post      = get_post( $comment->comment_post_ID );
			$post_name = ( isset( $post ) ) ? $post->post_title : '';

			$comments[ $comment->comment_ID ] = [
				'name' => $comment->comment_author . ' on ' . $post_name,
				'url'  => get_comment_link( $comment->comment_ID ),
			];
		}

		return $comments;
	}

	/**
	 * Get the meta links from the meta widget
	 *
	 * @return array|mixed
	 */
	public static function get_meta_list() {
		$meta_register = self::get_meta_register();
		$loginlogout   = self::get_loginlogout();

		$meta_list = [
			'loginlogout'       => $loginlogout,
			'rss2_url'          => [
				'name' => __( 'Entries RSS' ),
				'url'  => esc_url( get_bloginfo( 'rss2_url' ) ),
			],
			'comments_rss2_url' => [
				'name' => __( 'Comments RSS', THEME_DOMAIN ),
				'url'  => esc_url( get_bloginfo( 'comments_rss2_url' ) ),
			],
			'poweredby'         => [
				'name' => _x( 'WordPress.org', 'meta widget link text' ),
				'url'  => esc_url( __( 'https://wordpress.org/' ) ),
			],
		];

		if ( isset( $meta_register['name'] ) && isset( $meta_register['url'] ) ) {
			array_unshift( $meta_list, $meta_register );
		}

		$meta_list = apply_filters( 'thrive_meta_list', $meta_list );

		return $meta_list;
	}

	/**
	 * Return the login or logout url parts
	 *
	 * @return mixed
	 */
	public static function get_loginlogout() {
		if ( ! is_user_logged_in() ) {
			$url  = esc_url( wp_login_url() );
			$name = __( 'Log in', THEME_DOMAIN );
		} else {
			$url  = esc_url( wp_logout_url() );
			$name = __( 'Log out', THEME_DOMAIN );
		}

		return apply_filters( 'thrive_loginout', [ 'name' => $name, 'url' => $url ] );
	}

	/**
	 * Get the registration url parts based on the fact that the user is logged in or not and his capabilities
	 *
	 * @return mixed
	 */
	public static function get_meta_register() {
		$url  = '';
		$name = '';

		if ( ! is_user_logged_in() ) {
			if ( get_option( 'users_can_register' ) ) {
				$url  = esc_url( wp_registration_url() );
				$name = __( 'Register', THEME_DOMAIN );
			}
		} elseif ( current_user_can( 'read' ) ) {
			$url  = admin_url();
			$name = __( 'Site Admin', THEME_DOMAIN );
		}

		$register = apply_filters( 'thrive_meta_register', [ 'name' => $name, 'url' => $url ] );

		return $register;
	}
}
