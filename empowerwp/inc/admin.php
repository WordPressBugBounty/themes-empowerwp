<?php

add_action( 'admin_init', 'empower_admin_init_welcome_notice', 0 );
add_action( 'admin_init', 'empower_admin_init_enqueue_assets' );

add_action( 'wp_ajax_empower_dismiss_welcome_popup', 'empower_admin_ajax_welcome_notice_dismiss' );

function empower_admin_init_enqueue_assets() {

	wp_enqueue_style(
		'empower_admin_init_welcome_notice',
		empower_get_stylesheet_directory_uri() . "/assets/admin/admin.css",
		array(),
		mesmerize_get_version()
	);
	wp_enqueue_script(
		'empower_admin_init_welcome_notice',
		empower_get_stylesheet_directory_uri() . "/assets/admin/admin.js",
		array( 'jquery' ),
		mesmerize_get_version()
	);


    $welcome_notice_data = [
        'companion_disable_popup' => wp_create_nonce('companion_disable_popup')
    ];
    wp_localize_script('empower_admin_init_welcome_notice', 'welcome_notice_data', $welcome_notice_data);

}

function empower_admin_init_welcome_notice() {
	global $pagenow;

	if ( $pagenow === "update.php" ) {
		return;
	}

	if ( ! get_option( 'empower_welcome_notice_dismissed', false ) ) {


		if ( class_exists( "\\Mesmerize\\Companion" ) ) {
			return;
		}

		add_action( 'admin_notices', 'empower_welcome_notice_render_content', 0 );
	}
}

add_filter('mesmerize_start_with_front_page_name', function () { return 'Empower'; });

function empower_welcome_notice_cntent() {
	wp_enqueue_style( 'mesmerize_customizer_css',
								get_template_directory_uri() . '/customizer/css/companion-install.css' );	
	ob_start();
	mesmerize_require( "/customizer/start-with-frontpage.php" );
	$popup = ob_get_clean();

	$popup = preg_replace( '#<div class="image-wrapper".*?</div>#ms', "<div class='image-scroll'></div>", $popup );

	return $popup;
}

function empower_welcome_notice_render_content() {
	?>
    <div class="notice is-dismissible empower-welcome-notice">
        <div class="notice-content-wrapper">
			<?php echo empower_welcome_notice_cntent(); ?>
        </div>
    </div>
	<?php
}

function empower_admin_ajax_welcome_notice_dismiss() {
    check_ajax_referer('companion_disable_popup');
	update_option( 'empower_welcome_notice_dismissed', true );
}
