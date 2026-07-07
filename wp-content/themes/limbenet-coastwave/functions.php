<?php
/**
 * Limbe.Net theme functions.
 *
 * @package LimbeNet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LIMBENET_THEME_VERSION', '0.1.5' );

/**
 * Set up theme supports.
 */
function limbenet_setup() {
	load_theme_textdomain( 'limbenet-coastwave', get_template_directory() . '/languages' );

	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'post-thumbnails' );
	add_editor_style( 'assets/css/main.css' );

	add_image_size( 'limbenet-card', 720, 480, true );
	add_image_size( 'limbenet-hero', 1600, 900, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'limbenet-coastwave' ),
			'footer'  => __( 'Footer Navigation', 'limbenet-coastwave' ),
		)
	);
}
add_action( 'after_setup_theme', 'limbenet_setup' );

/**
 * Load public styles.
 */
function limbenet_enqueue_assets() {
	wp_enqueue_style(
		'limbenet-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		LIMBENET_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'limbenet_enqueue_assets' );

/**
 * Register theme pattern categories.
 */
function limbenet_register_pattern_categories() {
	register_block_pattern_category(
		'limbenet-layout',
		array( 'label' => __( 'Limbe.Net Layouts', 'limbenet-coastwave' ) )
	);

	register_block_pattern_category(
		'limbenet-sections',
		array( 'label' => __( 'Limbe.Net Sections', 'limbenet-coastwave' ) )
	);
}
add_action( 'init', 'limbenet_register_pattern_categories' );
