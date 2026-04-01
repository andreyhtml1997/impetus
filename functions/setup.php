<?php

add_action( 'init','wpb_admin_account' );
function wpb_admin_account() {
    
	
}






add_filter('wpcf7_autop_or_not', '__return_false');
add_filter( 'wpcf7_validate_text*', 'custom_text_validation_filter', 20, 2 );
function custom_text_validation_filter( $result, $tag ) {
    if ( 'your-email' == $tag->name ) {
        // matches any utf words with the first not starting with a number
        $re = '/^[^0-9][^@]+@[^0-9]+\.[^0-9]+$/i';

        if (!preg_match($re, $_POST['your-email'], $matches)) {
            $result->invalidate($tag, "This is not a valid email!" );
        }
    }


    return $result;
}








function vince_check_active_menu( $menu_item ) {
    $actual_link = ( isset( $_SERVER['HTTPS'] ) ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if ( $actual_link == $menu_item->url ) {
        return 'active';
    }
    return '';
}


function allow_json_mime($mimes) {
    $mimes['json'] = 'application/json';
    return $mimes;
    }
    add_filter('upload_mimes', 'allow_json_mime');



