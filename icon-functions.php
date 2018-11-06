<?php

/**
 * SVG icons related functions and filters
 *
 * @since 1.1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Enqueue icons style
 */
if ( ! function_exists( 'icons_styles' ) ) {

	function icons_styles() {

		wp_enqueue_style( 'icons-style', get_stylesheet_directory_uri() . '/includes/icons/assets/icons.css', array(), '1.0.0', 'all' );
	}
}

add_action( 'wp_enqueue_scripts', 'icons_styles' );
/**
 * Return SVG markup.
 *
 * @param array $args  {
 *                     Parameters needed to display an SVG.
 *
 * @type string $icon  Required SVG icon filename.
 * @type string $title Optional SVG title.
 * @type string $desc  Optional SVG description.
 * }
 * @return string SVG markup.
 */
function get_svg( $args = array() ) {

	// Make sure $args are an array.
	if ( empty( $args ) ) {
		return esc_html__( 'Please define default parameters in the form of an array.', 'some' );
	}
	// Define an icon.
	if ( false === array_key_exists( 'icon', $args ) ) {
		return esc_html__( 'Please define an SVG icon filename.', 'some' );
	}
	// Set defaults.
	$defaults = array(
		'icon'     => '',
		'title'    => '',
		'desc'     => '',
		'fallback' => false,
	);
	// Parse args.
	$args = wp_parse_args( $args, $defaults );
	// Set aria hidden.
	$aria_hidden = ' aria-hidden="true"';
	// Set ARIA.
	$aria_labelledby = '';
	// Set url to icons.
	$url_svg = get_stylesheet_directory_uri() . '/includes/icons/assets/icons.svg';
	/*
	 * Some theme doesn't use the SVG title or description attributes; non-decorative icons are described with .screen-reader-text.
	 *
	 * However, child themes can use the title and description to add information to non-decorative SVG icons to improve accessibility.
	 *
	 * Example 1 with title: <?php echo get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ) ) ); ?>
	 *
	 * Example 2 with title and description: <?php echo get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ), 'desc' => __( 'This is the description', 'textdomain' ) ) ); ?>
	 *
	 * See https://www.paciellogroup.com/blog/2013/12/using-aria-enhance-svg-accessibility/.
	 */
	if ( $args['title'] ) {
		$aria_hidden     = '';
		$unique_id       = uniqid();
		$aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';
		if ( $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
		}
	}
	// Begin SVG markup.
	$svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';
	// Display the title.
	if ( $args['title'] ) {
		$svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';
		// Display the desc only if the title is already set.
		if ( $args['desc'] ) {
			$svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
		}
	}
	/*
	 * Display the icon.
	 *
	 * The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
	 *
	 * See https://core.trac.wordpress.org/ticket/38387.
	 */
	$svg .= ' <use href="' . esc_url( $url_svg ) . '#icon-' . esc_html( $args['icon'] ) . '"; xlink:href="' . esc_url( $url_svg ) . '#icon-' . esc_html( $args['icon'] ) . '"></use> ';

	// Add some markup to use as a fallback for browsers that do not support SVGs.
	if ( $args['fallback'] ) {
		$svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) . '"></span>';
	}
	$svg .= '</svg>';

	$allowed_html = array(
		'use' => array(
			'href'       => true,
			'xlink:href' => true,
		),
		'svg' => array(
			'class'       => true,
			'aria-hidden' => true,
			'title'       => true,
			'path'        => true,
		),
	);

	return wp_kses( $svg, $allowed_html );
}

/**
 * Display an SVG.
 *
 * @param  array $args Parameters needed to display an SVG.
 */
function do_svg( $args = array() ) {

	echo get_svg( $args ); // WCS XSS ok
}

/**
 * Display SVG icons in social links menu.
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 *
 * @return string  $item_output The menu item output with social icon.
 */
add_filter( 'walker_nav_menu_start_el', 'nav_menu_social_icons', 10, 4 );
function nav_menu_social_icons( $item_output, $item, $depth, $args ) {

	// Get supported social icons.

	$social_icons = social_links_icons();
	// Change SVG icon inside social links menu if there is supported URL.
	if ( 'social-menu' === $args->theme_location ) {
		foreach ( $social_icons as $attr => $value ) {
			if ( false !== strpos( $item_output, $attr ) ) {
				$item_output = str_replace( $args->link_after, '</span>' . get_svg( array( 'icon' => esc_attr( $value ) ) ), $item_output );
			}
		}
	}

	return $item_output;
}

/**
 * Returns an array of supported social links (URL and icon name).
 *
 * @return array $social_links_icons
 */
function social_links_icons() {

	// Supported social links icons.
	$social_links_icons = array(
		'codepen.io'      => 'codepen',
		'digg.com'        => 'digg',
		'dribbble.com'    => 'dribbble',
		'dropbox.com'     => 'dropbox',
		'facebook.com'    => 'facebook',
		'flickr.com'      => 'flickr',
		'foursquare.com'  => 'foursquare',
		'plus.google.com' => 'googleplus',
		'github.com'      => 'github',
		'instagram.com'   => 'instagram',
		'linkedin.com'    => 'linkedin',
		't.me'            => 'telegram',
		'pinterest.com'   => 'pinterest',
		'getpocket.com'   => 'pocket',
		'reddit.com'      => 'reddit',
		'skype.com'       => 'skype',
		'skype:'          => 'skype',
		'soundcloud.com'  => 'soundcloud',
		'spotify.com'     => 'spotify',
		'stumbleupon.com' => 'stumbleupon',
		'tumblr.com'      => 'tumblr',
		'twitch.tv'       => 'twitch',
		'twitter.com'     => 'twitter',
		'vimeo.com'       => 'vimeo',
		'vk.com'          => 'vk',
		'wordpress.org'   => 'wordpress',
		'wordpress.com'   => 'wordpress',
		'youtube.com'     => 'youtubecube',
		'viber.com'       => 'viber',
		'whatsapp.com'    => 'whatsapp',
		'ok.ru'           => 'ok',
		'my.mail.ru'      => 'mymail',
	);

	/**
	 * Filter Some theme social links icons.
	 *
	 * @param array $social_links_icons
	 */
	return apply_filters( 'nav_social_icons', $social_links_icons );
}

function get_social_icons( $item_url ) {

	$social_icons = apply_filters(
		'crossbow_social_icons',
		array(
			'codepen.io'      => 'codepen',
			'digg.com'        => 'digg',
			'dribbble.com'    => 'dribbble',
			'dropbox.com'     => 'dropbox',
			'facebook.com'    => 'fb',
			'flickr.com'      => 'flickr',
			'foursquare.com'  => 'foursquare',
			'plus.google.com' => 'googleplus',
			'github.com'      => 'github',
			'instagram.com'   => 'instagram',
			'linkedin.com'    => 'linkedin',
			't.me'            => 'telegram',
			'pinterest.com'   => 'pinterest',
			'getpocket.com'   => 'pocket',
			'reddit.com'      => 'reddit',
			'skype.com'       => 'skype',
			'skype:'          => 'skype',
			'soundcloud.com'  => 'soundcloud',
			'spotify.com'     => 'spotify',
			'stumbleupon.com' => 'stumbleupon',
			'tumblr.com'      => 'tumblr',
			'twitch.tv'       => 'twitch',
			'twitter.com'     => 'twitter',
			'vimeo.com'       => 'vimeo',
			'vk.com'          => 'vk',
			'wordpress.org'   => 'wordpress',
			'wordpress.com'   => 'wordpress',
			'youtube.com'     => 'youtubecube',
			'viber.com'       => 'viber',
			'whatsapp.com'    => 'whatsapp',
			'ok.ru'           => 'ok',
			'my.mail.ru'      => 'mymail',
			'feed'            => 'rss',
		)
	);
	$item_label   = '';
	foreach ( $social_icons as $attr => $value ) {
		if ( false !== strpos( $item_url, $attr ) ) {
			$item_label = str_replace( $item_url, esc_attr( $value ), $item_url );
		}
	}

	return $item_label;
}
