<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JavaHost OpenSID Module.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */

function java_theme() {
    $CI =& get_instance();
    $CI->db->where('key','web_theme');
    $themedb = $CI->db->get('setting_aplikasi',1,0)->row('value');
    if (empty($themedb)) {
        $theme = 'default';
    } else {
        $theme = preg_replace("/desa\//", "", strtolower($themedb));
    }
    return $theme;
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

function java_upload_url($relpath) {
    return base_url().'desa/upload/theme/'.$relpath;
}

function java_get_article_categories() {
    $CI =& get_instance();
    $cats = array();
    foreach ($CI->db->get('kategori')->result() as $cat) {
        if (!empty($cat) && $cat->enabled == 1)
            $cats[ $cat->id ] = $cat->kategori;
    }
    log_message('error', print_r($cats,true));
    return $cats;
}

/**
 * Retrieve metadata from a file.
 *
 * @param string $file            Path to the file.
 * @param array  $default_headers List of headers, in the format array('HeaderKey' => 'Header Name').
 * @return array Array of file headers in `HeaderKey => Header Value` format.
 */
function java_file_data($file, $headers) {
    $fp = fopen( $file, 'r' );
    $file_data = fread( $fp, 8192 );
    fclose( $fp );
    $file_data = str_replace( "\r", "\n", $file_data );
    foreach ( $headers as $field => $regex ) {
        if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] )
            $headers[ $field ] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
        else
            $headers[ $field ] = '';
    }
    return $headers;
}

function java_get_widgets() {
    $CI =& get_instance();
    $widgets = array();
    $paths = FCPATH.'themes/'.java_theme().'/widgets';
    if (!is_dir($paths)) return $widgets;
    $headerdata = array('name' => 'Nama', 'icon' => 'Ikon', 'desc' => 'Deskripsi', 'options' => 'Pengaturan');
    foreach (glob($paths.'/*.php') as $widget) {
        $wdata = java_file_data($widget, $headerdata);
        $wdata['id'] = basename($widget, '.php');
        $wdata['path'] = $widget;
        $widgets[ $wdata['id'] ] = $wdata;
    }
    return $widgets;
}


/** ================================================================= */
/**   * HOOKS FOR THEMES                                              */
/** ================================================================= */

/**
 * HTML Head content.
 *
 * @return void
 */
function java_theme_head() {

    if ($favicon = java_config('general/favicon')) {
        $icopath = base_url().'desa/upload/theme/icon/';
        $favicon = file_exists(JAVAUPLOAD.'favicon.ico') ? 'favicon.ico' : 'favicon-16x16.png';
        echo '<link rel="shortcut icon" href="'.$icopath.$favicon.'">';
        echo '<link rel="apple-touch-icon" sizes="180x180" href="'.$icopath.'apple-touch-icon.png">';
        echo '<link rel="icon" type="image/png" sizes="32x32" href="'.$icopath.'favicon-32x32.png">';
        echo '<link rel="icon" type="image/png" sizes="16x16" href="'.$icopath.'favicon-16x16.png">';
    } else {
        echo '<link rel="shortcut icon" href="'.base_url().'favicon.ico">';
    }

    if ($mcolor = java_config('general/themecolor')) {
        echo '<meta name="msapplication-TileColor" content="'.$mcolor.'">';
        echo '<meta name="theme-color" content="'.$mcolor.'">';
    }

    if ($fbappid = java_config('seo/facebook_app_id')) {
        echo '<meta property="fb:app_id" content="'.trim($fbappid).'">';
    }

    if ($twtype = java_config('seo/twitter_type')) {
        echo '<meta name="twitter:card" content="'.trim($twtype).'">';
    }

    if ($twweb = java_config('seo/twitter_website')) {
        echo '<meta name="twitter:site" content="@'.trim($twweb,'@').'">';
    }

    if ($twauth = java_config('seo/twitter_creator')) {
        echo '<meta name="twitter:creator" content="@'.trim($twauth,'@').'">';
    }
}
java_action('java_theme_head', 'java_theme_head');

/**
 * Page title modifications.
 *
 * @param  string $title
 * @return string
 */
function java_theme_page_title($title) {
    if ($prefix = java_config('seo/title_prefix')) {
        $title = trim($prefix) . ' ' . $title;
    }
    if ($suffix = java_config('seo/title_suffix')) {
        $title = $title . ' ' . trim($suffix);
    }
    return $title;
}
java_filter('java_theme_page_title', 'java_theme_page_title');

/**
 * Page meta keywords modifications.
 *
 * @param  string $keyword
 * @return string
 */
function java_theme_meta_keyword($keyword) {
    if ($xword = java_config('seo/keyword')) {
        $keyword = trim($xword) . ', ' . $keyword;
    }
    return $keyword;
}
java_filter('java_theme_meta_keyword', 'java_theme_meta_keyword');

/**
 * Page meta description modifications.
 *
 * @param  string $description
 * @return string
 */
function java_theme_meta_description($description) {
    if ($xdesc = java_config('seo/description')) {
        $description = trim(htmlspecialchars($xdesc, ENT_QUOTES));
    }
    return $description;
}
java_filter('java_theme_meta_description', 'java_theme_meta_description');
