<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JavaHost OpenSID Module.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */

function java_format_price($price) {
    return 'Rp '.number_format($price, 0, ',', '.');
}

function java_config($field) {
    $CI =& get_instance();
    $CI->load->library('java_theme_config');
    return $CI->java_theme_config->get_config($field);
}

function java_is_serialize($data) {
    if ( !is_string( $data ) )
        return false;
    $data = trim( $data );
    if ( 'N;' == $data )
        return true;
    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
        return false;
    switch ( $badions[1] ) {
        case 'a' :
        case 'O' :
        case 's' :
            if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                return true;
            break;
    }
    return false;
}

function java_build_html($tag, $attrs = array(), $closure = false) {
    $html = "<$tag";
    if (!empty($attrs)) {
        foreach ($attrs as $key => $value) {
            if ($value === null) {
                $html .= ' '.$key;
            } else {
                $html .= ' '.$key.'="'.htmlspecialchars($value, ENT_QUOTES).'"';
            }
        }
    }
    $html .= ($closure === false) ? '/>' : ">".$closure."</$tag>";
    return $html;
}


function java_file_upload_max_size() {
    static $max_size = -1;
    if ($max_size < 0) {
        $post_max_size = java_parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }
        $upload_max = java_parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

function java_parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

function java_format_byte($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GG', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}
