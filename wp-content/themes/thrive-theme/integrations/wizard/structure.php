<?php
/**
 * Thrive Themes - https=>//thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

return [
	'steps'    => [
		[
			'id'           => 'logo',
			'sidebarLabel' => __( 'Logo', THEME_DOMAIN ),
			'section'      => 'site',
			'hasTopMenu'   => false,
		],
		[
			'id'           => 'color',
			'sidebarLabel' => __( 'Brand Colour', THEME_DOMAIN ),
			'section'      => 'site',
			'hasTopMenu'   => false,
		],
		[
			'id'                    => 'header',
			'title'                 => __( 'Header - Choose a Template', THEME_DOMAIN ),
			'sidebarLabel'          => __( 'Header', THEME_DOMAIN ),
			'section'               => 'site',
			'hasTopMenu'            => true,
			'selector'              => [
				'label' => __( 'Select a Header', THEME_DOMAIN ),
			],
			'popupMessage'          => 'You can change the <strong>Header</strong> from the top dropdown or<br>by pressing the arrow keys &lt; &gt;<br>When you are done click the <strong>Choose and Continue</strong> button.',
			'completedPopupMessage' => 'You can change the <strong>Header</strong> from the dropdown',
		],
		[
			'id'                    => 'footer',
			'title'                 => __( 'Footer - Choose a Template', THEME_DOMAIN ),
			'sidebarLabel'          => __( 'Footer', THEME_DOMAIN ),
			'section'               => 'site',
			'hasTopMenu'            => true,
			'selector'              => [
				'label' => __( 'Select a Footer', THEME_DOMAIN ),
			],
			'popupMessage'          => 'You can change the <strong>Footer</strong> from the top dropdown or<br>by pressing the arrow keys &lt; &gt;<br>When you are done click the <strong>Choose and Continue</strong> button.',
			'completedPopupMessage' => 'You can change the <strong>Footer</strong> from the dropdown',
		],
		[
			'id'                   => 'homepage',
			'title'                => __( 'Choose a Homepage', THEME_DOMAIN ),
			'sidebarLabel'         => __( 'Homepage', THEME_DOMAIN ),
			'section'              => 'site',
			'previewMode'          => 'iframe',
			'hasTopMenu'           => true,
			'hideTemplateSelector' => true,
			'selector'             => [
				'label' => __( 'Select a Page', THEME_DOMAIN ),
			],
			'narrowTemplate'       => true,
		],
		[
			'id'                    => 'post',
			'title'                 => __( 'Single Blog Post - Choose a Template', THEME_DOMAIN ),
			'sidebarLabel'          => __( 'Single Blog Post', THEME_DOMAIN ),
			'section'               => 'site',
			'hasTopMenu'            => true,
			'previewMode'           => 'iframe',
			'selector'              => [
				'label' => __( 'Select a Template', THEME_DOMAIN ),
			],
			'narrowTemplate'        => true,
			'popupMessage'          => 'You can change the <strong>Blog Post Template</strong> from the top dropdown or<br>by pressing the arrow keys &lt; &gt;<br>When you are done click the <strong>Choose and Continue</strong> button.',
			'completedPopupMessage' => 'You can change the <strong>Blog Post Template</strong> from the dropdown',
		],
		[
			'id'                    => 'blog',
			'title'                 => __( 'Blog Post List - Choose a Template', THEME_DOMAIN ),
			'sidebarLabel'          => __( 'Blog Post List', THEME_DOMAIN ),
			'section'               => 'site',
			'hasTopMenu'            => true,
			'previewMode'           => 'iframe',
			'selector'              => [
				'label' => __( 'Select a Template', THEME_DOMAIN ),
			],
			'narrowTemplate'        => true,
			'popupMessage'          => 'You can change the <strong>Blog Post List Template</strong> from the top dropdown or<br>by pressing the arrow keys &lt; &gt;<br>When you are done click the <strong>Choose and Continue</strong> button.',
			'completedPopupMessage' => 'You can change the <strong>Blog Post List Template</strong> from the dropdown',
		],
		[
			'id'                    => 'page',
			'sidebarLabel'          => __( 'Page', THEME_DOMAIN ),
			'title'                 => __( 'Page - Choose a Template', THEME_DOMAIN ),
			'section'               => 'site',
			'hasTopMenu'            => true,
			'previewMode'           => 'iframe',
			'selector'              => [
				'label' => __( 'Select a Template', THEME_DOMAIN ),
			],
			'narrowTemplate'        => true,
			'popupMessage'          => 'You can change the <strong>Page Template</strong> from the top dropdown or<br>by pressing the arrow keys &lt; &gt;<br>When you are done click the <strong>Choose and Continue</strong> button.',
			'completedPopupMessage' => 'You can change the <strong>Page Template</strong> from the top dropdown',
		],
		[
			'id'           => 'content-page',
			'sidebarLabel' => __( 'Pages', THEME_DOMAIN ),
			'section'      => 'content',
		],
		[
			'id'           => 'content-posts',
			'sidebarLabel' => __( 'Blog Posts', THEME_DOMAIN ),
			'section'      => 'content',
		],
		[
			'id'                   => 'menu',
			'title'                => __( 'Menu - Choose a menu', THEME_DOMAIN ),
			'sidebarLabel'         => __( 'Menu', THEME_DOMAIN ),
			'section'              => 'content',
			'hasTopMenu'           => true,
			'hideTemplateSelector' => true,
		],
	],
	'sections' => [
		[
			'id'           => 'site',
			'sidebarLabel' => __( 'Site Structure', THEME_DOMAIN ),
		],
		[
			'id'           => 'content',
			'sidebarLabel' => __( 'Content', THEME_DOMAIN ),
		],
	],
];
