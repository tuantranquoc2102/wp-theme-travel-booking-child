<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*************************************************************************************************/
/**
 * Enqueue parent and child theme styles
 */
add_action( 'wp_enqueue_scripts', function() {
    // Load style parent theme
    wp_enqueue_style( 'travel-booking-parent-style', get_template_directory_uri() . '/style.css', [], '1.0.0' );

    // Load style child theme (override)
    wp_enqueue_style( 'travel-booking-child-style', get_stylesheet_directory_uri() . '/style.css', array('travel-booking-parent-style'), '1.0.1' );

    // Load sticky header CSS with high version and in footer for max override
    wp_enqueue_style( 'tb-child-sticky-header', get_stylesheet_directory_uri() . '/css/sticky-header.css', array('travel-booking-child-style'), '999.999.999', 'all' );

    // Load sticky header JS
    wp_enqueue_script( 'tb-child-sticky-header', get_stylesheet_directory_uri() . '/js/sticky-header.js', array(), null, true );
}, 99 );

// Add a custom class to body for even higher CSS specificity if needed
add_filter('body_class', function($classes) {
    $classes[] = 'tb-child-sticky-header-active';
    return $classes;
});

/*************************************************************************************************/
/**
 * Register customizer settings: 
 * Heading colors H1..H5
 */
function tb_child_customize_register( $wp_customize ) {

	// Section: Typography Colors (tên hiển thị)
	$wp_customize->add_section( 'tb_typography_colors', array(
		'title'    => __( 'Typography Colors', 'tb-child' ),
		'priority' => 30,
		'description' => __( 'Set colors for headings H1 - H5', 'tb-child' ),
	) );

	// defaults
	$defaults = array(
		'h1_color' => '#111111',
		'h2_color' => '#222222',
		'h3_color' => '#333333',
		'h4_color' => '#444444',
		'h5_color' => '#555555',
	);

	// helper to add setting + control
	$headings = array(
		'h1_color' => __( 'H1 Color', 'tb-child' ),
		'h2_color' => __( 'H2 Color', 'tb-child' ),
		'h3_color' => __( 'H3 Color', 'tb-child' ),
		'h4_color' => __( 'H4 Color', 'tb-child' ),
		'h5_color' => __( 'H5 Color', 'tb-child' ),
	);

	foreach ( $headings as $key => $label ) {
		// setting
		$wp_customize->add_setting( $key, array(
			'default'           => $defaults[ $key ],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage', // for live preview
		) );

		// color control
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, array(
			'label'    => $label,
			'section'  => 'tb_typography_colors',
			'settings' => $key,
			'priority' => 10,
		) ) );
	}
}
add_action( 'customize_register', 'tb_child_customize_register' );

/**
 * Print dynamic heading colors in head
 */
function tb_child_print_heading_colors() {
	$h1 = get_theme_mod( 'h1_color', '#111111' );
	$h2 = get_theme_mod( 'h2_color', '#222222' );
	$h3 = get_theme_mod( 'h3_color', '#333333' );
	$h4 = get_theme_mod( 'h4_color', '#444444' );
	$h5 = get_theme_mod( 'h5_color', '#555555' );

	// Safely escape
	$h1 = esc_attr( $h1 );
	$h2 = esc_attr( $h2 );
	$h3 = esc_attr( $h3 );
	$h4 = esc_attr( $h4 );
	$h5 = esc_attr( $h5 );

	$custom_css = "
		/* Dynamic heading colors from Customizer */
		h1, .entry-content h1 { color: {$h1} !important; }
		h2, .entry-content h2 { color: {$h2} !important; }
		h3, .entry-content h3 { color: {$h3} !important; }
		h4, .entry-content h4 { color: {$h4} !important; }
		h5, .entry-content h5 { color: {$h5} !important; }
	";

	echo "<style id='tb-child-heading-colors'>\n" . $custom_css . "\n</style>\n";
}
add_action( 'wp_head', 'tb_child_print_heading_colors', 11 );

// 🔹 Enqueue Customizer preview (chạy trong khung xem trực tiếp)
function travel_booking_child_customizer_preview_js() {
    wp_enqueue_script(
        'travel-booking-child-customizer-preview',
        get_stylesheet_directory_uri() . '/js/customizer-preview.js',
        array( 'customize-preview' ),
        false,
        true
    );
}
add_action( 'customize_preview_init', 'travel_booking_child_customizer_preview_js' );

/*************************************************************************************************/
/**
 * Register customizer settings: WP Travel Engine Plugin
 * - Trip Title Color
 * - Trip Price Color
 * - Book Button Color
 */
function travel_booking_child_customize_register( $wp_customize ) {

    // 1️⃣ Tạo section riêng cho WP Travel Engine Colors
    $wp_customize->add_section( 'wpte_colors_section', array(
        'title'       => __( 'WP Travel Engine Colors', 'travel-booking-child' ),
        'priority'    => 35,
        'description' => __( 'Customize colors for WP Travel Engine elements.', 'travel-booking-child' ),
    ) );

    // 2️⃣ Màu tiêu đề tour
    $wp_customize->add_setting( 'wpte_trip_title_color', array(
        'default'   => '#008000',
        'transport' => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wpte_trip_title_color_control', array(
        'label'    => __( 'Trip Title Color', 'travel-booking-child' ),
        'section'  => 'wpte_colors_section',
        'settings' => 'wpte_trip_title_color',
    ) ) );

    // 3️⃣ Màu giá tour
    $wp_customize->add_setting( 'wpte_trip_price_color', array(
        'default'   => '#0073e6',
        'transport' => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wpte_trip_price_color_control', array(
        'label'    => __( 'Trip Price Color', 'travel-booking-child' ),
        'section'  => 'wpte_colors_section',
        'settings' => 'wpte_trip_price_color',
    ) ) );

    // 4️⃣ Màu nút đặt tour
    $wp_customize->add_setting( 'wpte_book_button_color', array(
        'default'   => '#008000',
        'transport' => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wpte_book_button_color_control', array(
        'label'    => __( 'Book Button Color', 'travel-booking-child' ),
        'section'  => 'wpte_colors_section',
        'settings' => 'wpte_book_button_color',
    ) ) );
}
add_action( 'customize_register', 'travel_booking_child_customize_register' );

/**
 * Print WP Travel Engine custom colors in head
 */
function travel_booking_child_wpte_customizer_css() {
    $title_color = get_theme_mod( 'wpte_trip_title_color', '#008000' );
    $price_color = get_theme_mod( 'wpte_trip_price_color', '#0073e6' );
    $button_color = get_theme_mod( 'wpte_book_button_color', '#008000' );
    ?>
    <style type="text/css">
        /* 🎨 Tiêu đề tour */
        .text-holder .trip-info .title a {
            color: <?php echo esc_attr( $title_color ); ?> !important;
        }

        /* 💰 Giá tour */
        .price-holder .new-price {
            color: <?php echo esc_attr( $price_color ); ?> !important;
        }

        /* 🚌 Nút xem chi tiết tour */
        .btn-holder .primary-btn {
            background-color: <?php echo esc_attr( $button_color ); ?> !important;
            border-color: <?php echo esc_attr( $button_color ); ?> !important;
        }
        .btn-holder .primary-btn:hover {
            opacity: 0.85;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'travel_booking_child_wpte_customizer_css' );
/*************************************************************************************************/
/**
 * Customize logo
 */
function travel_child_custom_logo_style() {
    wp_enqueue_style( 'travel-child-logo', get_stylesheet_directory_uri() . '/css/custom-logo.css', array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'travel_child_custom_logo_style' );

/**
 * Register footer widget areas (4 columns)
 */
add_action( 'widgets_init', 'travel_booking_register_footer_sidebars' );
function travel_booking_register_footer_sidebars() {
    // Register 4 sidebars (you always register 4 so admin can add widgets even when 3-col active)
    for ( $i = 1; $i <= 4; $i++ ) {
        register_sidebar( array(
            'name'          => sprintf( __( 'Footer Column %d', 'travel-booking-child' ), $i ),
            'id'            => 'footer-column-' . $i,
            'description'   => sprintf( __( 'Widgets for footer column %d', 'travel-booking-child' ), $i ),
            'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer-widget-title">',
            'after_title'   => '</h4>',
        ) );
    }
}

/**
 * Add Customizer setting + control for footer columns (3 or 4)
 */
add_action( 'customize_register', 'travel_booking_customize_footer_settings' );
function travel_booking_customize_footer_settings( $wp_customize ) {
    // Add section
    $wp_customize->add_section( 'travel_booking_footer_section', array(
        'title'    => __( 'Footer Settings', 'travel-booking-child' ),
        'priority' => 160,
    ) );

    // Setting for number of columns
    $wp_customize->add_setting( 'travel_booking_footer_columns', array(
        'default'           => '4',
        'sanitize_callback' => 'travel_booking_sanitize_footer_columns',
        'transport'         => 'refresh', // or 'postMessage' + selective refresh if you want live update
    ) );

    // Radio control (3 or 4)
    $wp_customize->add_control( 'travel_booking_footer_columns_control', array(
        'label'    => __( 'Footer Columns', 'travel-booking-child' ),
        'section'  => 'travel_booking_footer_section',
        'settings' => 'travel_booking_footer_columns',
        'type'     => 'radio',
        'choices'  => array(
            '3' => __( '3 Columns', 'travel-booking-child' ),
            '4' => __( '4 Columns', 'travel-booking-child' ),
        ),
    ) );
}

// Sanitization callback
function travel_booking_sanitize_footer_columns( $input ) {
    $valid = array( '3', '4' );
    return in_array( $input, $valid ) ? $input : '4';
}
