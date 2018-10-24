<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JavaHost OpenSID Module.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */

/**
 * Get current active theme.
 *
 * @return string
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

/**
 * Get theme options value.
 *
 * @param  string $field "groupname/fieldname"
 * @return mixed
 */
function java_config($field) {
    $CI =& get_instance();
    $CI->load->library('java_theme_config');
    return $CI->java_theme_config->get_config($field);
}

/**
 * Get theme options flag (true/false).
 *
 * @param  string $field "groupname/fieldname"
 * @return boolean
 */
function java_config_flag($field) {
    $val = java_config($field);
    return (bool) $val;
}

function register_shortcode($tagname, $func) {
    global $shortcodes;
    if (!is_array($shortcodes)) {
        $shortcodes = array();
    }
    if (!isset($shortcodes[ $tagname ])) {
        $shortcodes[ $tagname ] = $func;
        return true;
    }
    return false;
}

register_shortcode('contact_form', function ($artikel) {
    if (java_config_flag('contact/enable')) {
        $ci = &get_instance();
        $template = str_replace('layouts/artikel.tpl.php', 'partials/contact.php', $ci->template);
        $ci->load->view($template, array('artikel' => $artikel));
    }
});

function java_get_content($artikel) {
    global $shortcodes;
    $content = isset($artikel['isi']) ? trim($artikel['isi']) : '';
    if (!empty($content) && strpos($content, '[') !== false) {
        preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
        $tagnames = array_intersect( array_keys($shortcodes), $matches[1] );
        if (!empty($tagnames)) {
            foreach ($tagnames as $tagname) {
                if (is_callable($shortcodes[ $tagname ])) {
                    $callFunc = $shortcodes[ $tagname ];
                    ob_start();
                    $callFunc($artikel);
                    $x_content = ob_get_contents();
                    ob_end_clean();
                } elseif (is_string($shortcodes[ $tagname ])) {
                    $x_content = trim($shortcodes[ $tagname ]);
                } else {
                    $x_content = '';
                }
                $content = str_replace("[$tagname]", $x_content, $content);
            }
        }
    }
    return $content;
}

/**
 * Check if variable is serialized array/
 *
 * @param  mixed  $data
 * @return boolean
 */
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

/**
 * Check if string is json string.
 *
 * @param  mixed  $string
 * @return boolean
 */
function java_is_json($string) {
    @json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * Build HTML tag.
 *
 * @param  string $tag
 * @param  array  $attrs
 * @return string
 */
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

/**
 * Get default PHP ma upload size.
 *
 * @return string
 */
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

/**
 * Parse size string from php.ini
 *
 * @param  string $size
 * @return integer
 */
function java_parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

/**
 * Format numeric to byte calculations.
 *
 * @param  integer  $size
 * @param  integer $precision
 * @return string
 */
function java_format_byte($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GG', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

/**
 * Search array inside multidimensional array.
 *
 * @param  string $id
 * @param  array $array
 * @param  string $field
 * @return mixed
 */
function java_array_search($id, $array, $field = 'id') {
    foreach ($array as $k => $item) {
        if ($item[ $field ] === $id) {
            return $k;
        }
    }
    return null;
}

/**
 * Shorthand to get upload URL by theme options.
 *
 * @param  string $relpath
 * @return string
 */
function java_upload_url($relpath) {
    return base_url().'desa/upload/theme/'.$relpath;
}

/**
 * FOrmat date to human radable.
 *
 * @param  string|integer $ts
 * @return string
 */
function java_date($ts) {
    if(!ctype_digit($ts)) {
        $ts = strtotime($ts);
    }
    $diff = time() - $ts;
    if($diff == 0) {
        return 'baru saja';
    } elseif($diff > 0) {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) {
            if($diff < 60) return 'baru saja';
            if($diff < 120) return '1 menit lalu';
            if($diff < 3600) return floor($diff / 60) . ' menit lalu';
            if($diff < 7200) return '1 jam lalu';
            if($diff < 86400) return floor($diff / 3600) . ' jam lalu';
        }
        if($day_diff == 1) { return 'Kemarin'; }
        if($day_diff < 7) { return $day_diff . ' hari lalu'; }
        if($day_diff < 31) { return ceil($day_diff / 7) . ' minggu lalu'; }
        if($day_diff < 60) { return 'bulan lalu'; }
        return date('j F Y', $ts);
    } else {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) {
            if($diff < 120) { return 'dalam beberapa menit'; }
            if($diff < 3600) { return 'dalam ' . floor($diff / 60) . ' menit'; }
            if($diff < 7200) { return '1 jam lagi'; }
            if($diff < 86400) { return 'dalam ' . floor($diff / 3600) . ' jam'; }
        }
        if($day_diff == 1) { return 'Besok'; }
        if($day_diff < 4) { return date('l', $ts); }
        if($day_diff < 7 + (7 - date('w'))) { return 'minggu depan'; }
        if(ceil($day_diff / 7) < 4) { return 'dalam ' . ceil($day_diff / 7) . ' minggu'; }
        if(date('n', $ts) == date('n') + 1) { return 'bulan depan'; }
        return date('j F Y', $ts);
    }
}

/**
 * Get all articles categories.
 *
 * @return array
 */
function java_get_article_categories() {
    $CI =& get_instance();
    $cats = array();
    foreach ($CI->db->get('kategori')->result() as $cat) {
        if (!empty($cat) && $cat->enabled == 1)
            $cats[ $cat->id ] = $cat->kategori;
    }
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

/**
 * Get all available widgets from theme.
 *
 * @return array
 */
function java_get_widgets() {
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

/**
 * Get homepage articles.
 *
 * @return array
 */
function java_get_home_articles() {
    $CI =& get_instance();
    $ex_headline = (java_config('homepage/show_headline') !== '1');
    $limit = 6;
    $excludes = explode(',', java_config('homepage/excludes'));
    $excludes[] = '999';
    $comq = "
    SELECT COUNT(*) FROM komentar WHERE komentar.id_artikel = a.id AND komentar.enabled = '1'
    ";
    $CI->db->select(array('a.*', '('.$comq.') AS komentar', 'u.nama AS owner', 'k.kategori AS kategori', ''));
    $CI->db->join('user u', 'a.id_user = u.id', 'left');
    $CI->db->join('kategori k', 'a.id_kategori = k.id', 'left');
    $CI->db->where('a.enabled', '1');
    $CI->db->where('a.tgl_upload <', date('Y-m-d H:i:s'));
    $CI->db->where_not_in('a.id_kategori', $excludes);
    if ($ex_headline) $CI->db->where('a.headline <>', '1');
    $CI->db->limit($limit);
    $CI->db->order_by('a.tgl_upload', 'DESC');
    return $CI->db->get('artikel a')->result_array();
}

/**
 * Get all article categories.
 *
 * @return array
 */
function java_get_categories($tipe = null) {
    $CI =& get_instance();
    $CI->db->select(array('id', 'kategori'));
    if ($tipe !== null) $CI->db->where('tipe', $tipe);
    $categories = $CI->db->get('kategori')->result_array();
    $cats = array();
    foreach ($categories as $cat) {
        if (!empty($cat['id']) && !empty($cat['kategori'])) {
            $cats[ intval($cat['id']) ] = trim($cat['kategori']);
        }
    }
    return $cats;
}

function java_proceed_ajax_form($form) {

    $CI =& get_instance();
    $result = array('success' => false);

    if ((int)$CI->session->userdata($form) === 1) {
        $result['message'] = 'Anda sudah pernah mengirimkan email melalui form ini sebelumnya, silahkan coba lagi lain kali.';
        return $result;
    }

    if ($form == 'contact_form') {
        $captcha = $CI->input->post('captcha');
        $is_captcha = java_config_flag('contact/captcha');
        $secret = java_config('contact/secret_key');
        $mailfrom = java_config('contact/mailfrom');
        $mailto = java_config('contact/mailto');

        if ($is_captcha && !empty($secret)) {
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$captcha}");
            $captcha_response = json_decode($verify);

            if ($captcha_response->success === false) {
                $result['message'] = 'Gagal verifikasi captcha, silahkan coba reload halaman ini.';
                return $result;
            }
        }

        $CI->load->model('first_m');
        $name = $CI->input->post('name');
        $email = $CI->input->post('email');
        $subject = $CI->input->post('subject');
        $content = $CI->input->post('content');
        $base_url = base_url();
        $desa = $CI->first_m->get_data();

        if (empty($name)) {
            $result['message'] = 'Nama anda tidak boleh kosong.';
            return $result;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result['message'] = 'Alamat email tidak valid.';
            return $result;
        }

        if (empty($subject) || empty($content) || strlen($content) < 10) {
            $result['message'] = 'Judul dan isi pesan anda tidak boleh kosong.';
            return $result;
        }

        $CI->load->library('email');

        $CI->email->from($mailfrom, $name);
        $CI->email->to($mailto);

        $CI->email->subject('Form Kontak : '.$subject);
        $CI->email->message("Halo Admin,

Anda mendapatkan pesan baru dari form kontak di website OpenSID anda ($base_url).

Dari : $name
Email : $email
Judul : $subject
Pesan: $content

--------------
Email ini dikirimkan melalui form kontak dari aplikasi OpenSID.
        ");

        if (!$CI->email->send()) {
            log_message('error', print_r($CI->email->print_debugger(), true));
            $CI->session->set_userdata('contact_form_data', array(
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'content' => $content
            ));
            $result['message'] = 'Gagal mengirimkan email. Coba lagi lain kali.';
        } else {
            $CI->session->set_userdata('contact_form', 1);
            $result = array('success' => true);
        }
    }

    return $result;
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
