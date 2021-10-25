
<?php

/*
 * Plugin Name:       choton
 * Plugin URI:        http://www.chotonbhowmik.com/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Choton Bhowmik
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */
 //Create a function called "wporg_init" if it doesn't already exist
if ( !function_exists( 'wporg_init' ) ) {
    function wporg_init() {
        register_setting( 'wporg_settings', 'wporg_option_foo' );
    }
}
 
//Create a function called "wporg_get_foo" if it doesn't already exist
if ( !function_exists( 'wporg_get_foo' ) ) {
    function wporg_get_foo() {
        return get_option( 'wporg_option_foo' );
    }
}


//Using OOP
if ( !class_exists( 'WPOrg_Plugin' ) ) {
    class WPOrg_Plugin
    {
        public static function init() {
            register_setting( 'wporg_settings', 'wporg_option_foo' );
        }
 
        public static function get_foo() {
            return get_option( 'wporg_option_foo' );
        }
    }
 
    WPOrg_Plugin::init();
    WPOrg_Plugin::get_foo();
}

//The example below creates a link on the frontend which gives the ability to trash posts. Because this code does not check user capabilities, it allows any visitor to the site to trash posts!
/**
 * Generate a Delete link based on the homepage url.
 *
 * @param string $content   Existing content.
 *
 * @return string|null
 */
function wporg_generate_delete_link( $content ) {
    // Run only for single post page.
    if ( is_single() && in_the_loop() && is_main_query() ) {
        // Add query arguments: action, post.
        $url = add_query_arg(
            [
                'action' => 'wporg_frontend_delete',
                'post'   => get_the_ID(),
            ], home_url()
        );
 
        return $content . ' <a href="' . esc_url( $url ) . '">' . esc_html__( 'Delete Post', 'wporg' ) . '</a>';
    }
 
    return null;
}
 
 
/**
 * Request handler
 */
function wporg_delete_post() {
    if ( isset( $_GET['action'] ) && 'wporg_frontend_delete' === $_GET['action'] ) {
 
        // Verify we have a post id.
        $post_id = ( isset( $_GET['post'] ) ) ? ( $_GET['post'] ) : ( null );
 
        // Verify there is a post with such a number.
        $post = get_post( (int) $post_id );
        if ( empty( $post ) ) {
            return;
        }
 
        // Delete the post.
        wp_trash_post( $post_id );
 
        // Redirect to admin page.
        $redirect = admin_url( 'edit.php' );
        wp_safe_redirect( $redirect );
 
        // We are done.
        die;
    }
}
 
 
/**
 * Add the delete link to the end of the post content.
 */
add_filter( 'the_content', 'wporg_generate_delete_link' );
 
/**
 * Register our request handler with the init hook.
 */
add_action( 'init', 'wporg_delete_post' );



/**
 * Validate a US zip code.
 *
 * @param string $zip_code   RAW zip code to check.
 *
 * @return bool              true if valid, false otherwise.
 */
function wporg_is_valid_us_zip_code( $zip_code ) {
    // Scenario 1: empty.
    if ( empty( $zip_code ) ) {
        return false;
    }
 
    // Scenario 2: more than 10 characters.
    if ( 10 < strlen( trim( $zip_code ) ) ) {
        return false;
    }
 
    // Scenario 3: incorrect format.
    if ( ! preg_match( '/^\d{5}(\-?\d{4})?$/', $zip_code ) ) {
        return false;
    }
 
    // Passed successfully.
    return true;
}


?>