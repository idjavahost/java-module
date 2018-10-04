<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

foreach (glob(FCPATH.'themes/java-*') as $javathemedir) {
    if (file_exists($javathemedir.'/config.php')) {
        include_once $javathemedir.'/config.php';
    }
}

$config['general'] = java_filters('java_theme_config_general', array(
    'label'   => 'General',
    'icon'    => 'icon-logo',
    'order'   => 0,
    'fields'  => array(
        array(
            'id' => 'enable',
            'label' => 'Homepage Image',
            'type' => 'toggle',
            'order' => 10,
            'value' => '1',
            'default' => '1',
            'options' => array('on' => 'On', 'off' => 'Off'),
            'help' => 'Aktifkan image slider pada halaman home.'
        ),
        array(
            'id' => 'logo',
            'label' => 'Logo',
            'type' => 'upload',
            'order' => 20,
            'filetype' => 'image',
            'filepath' => 'logo',
            'maxwidth' => 100,
            'maxheight' => 100,
            'extensions' => array('png', 'jpg'),
        ),
        array(
            'id' => 'logotext',
            'label' => 'Teks Logo',
            'order' => 30,
            'help' => 'Akan ditampilan setelah gambar logo di header, kosongkan jika ingin menggunakan nama desa.'
        ),
        array(
            'id' => 'favicon',
            'label' => 'Favicon',
            'type' => 'upload',
            'order' => 40,
            'filetype' => 'image',
            'filepath' => 'icon',
            'maxwidth' => 50,
            'maxheight' => 50,
            'extensions' => array('png', 'ico'),
        ),
        array(
            'id' => 'appleicon',
            'label' => 'Apple Touch Icon',
            'type' => 'upload',
            'order' => 50,
            'filetype' => 'image',
            'filepath' => 'icon',
            'maxwidth' => 180,
            'maxheight' => 180,
            'extensions' => array('png'),
        ),
        array(
            'id' => 'themecolor',
            'label' => 'Mobile Color',
            'order' => 60,
            'help' => 'Digunakan untuk warna tema browser pada Android, iPhone, dan Windows Phone.',
            'type' => 'colorpicker',
            'default' => '#f1f1f1'
        ),
    )
));

$config['sidebar'] = java_filters('java_theme_config_sidebar', array(
    'label'   => 'Sidebar',
    'icon'    => 'icon-sidebar',
    'order'   => 30,
    'fields'  => array(
        array(
            'id' => 'sidebar_pos',
            'label' => 'Posisi Sidebar',
            'help' => 'Tentukan posisi sidebar ketika berada di desktop.',
            'type' => 'select',
            'default' => 'kiri',
            'options' => array(
                'kiri' => 'Sidebar Kiri',
                'kanan' => 'Sidebar Kanan'
            )
        ),
        array(
            'id' => 'config',
            'label' => 'Sidebar Artikel',
            'help' => 'Atur, urut, dan sesuaikan widget untuk sidebar kanan atau kiri di semua halaman artikel.',
            'type' => 'sidebar'
        ),
    )
));

$config['sosmed'] = java_filters('java_theme_config_sosmed', array(
    'label'   => 'Sosial Media',
    'icon'    => 'icon-announcement',
    'order'   => 90,
    'fields'  => array(
        array(
            'id' => 'enable',
            'order' => 10,
            'label' => 'Sosial Media',
            'type' => 'toggle',
            'value' => '1',
            'default' => '1',
            'help' => 'Tampilkan sosial media di header.'
        ),
        array(
            'id' => 'facebook',
            'order' => 20,
            'label' => 'Facebook Page',
            'prefix' => 'fa fa-facebook',
            'default' => 'https://facebook.com/'
        ),
        array(
            'id' => 'twitter',
            'order' => 30,
            'label' => 'Twitter Profile',
            'prefix' => 'fa fa-twitter',
            'default' => 'https://twitter.com/'
        ),
        array(
            'id' => 'gplus',
            'order' => 40,
            'label' => 'Google Plus Page',
            'prefix' => 'fa fa-google-plus',
            'default' => 'https://plus.google.com/'
        ),
        array(
            'id' => 'instagram',
            'order' => 50,
            'label' => 'Instagram URL',
            'prefix' => 'fa fa-instagram',
            'default' => 'https://instagram.com/'
        ),
        array(
            'id' => 'youtube',
            'order' => 60,
            'label' => 'YouTube Channel',
            'prefix' => 'fa fa-youtube',
            'default' => 'https://youtube.com/'
        ),
    )
));

$config['seo'] = java_filters('java_theme_config_seo', array(
    'label'   => 'SEO Settings',
    'icon'    => 'icon-extension',
    'order'   => 99,
    'fields'  => array(
        array(
            'id' => 'title_prefix',
            'order' => 10,
            'label' => 'Title Prefix',
            'help' => 'Teks awalan yang akan di terapkan pada judul di semua halaman.'
        ),
        array(
            'id' => 'title_suffix',
            'order' => 20,
            'label' => 'Title Suffix',
            'help' => 'Teks akhiran yang akan di terapkan pada judul di semua halaman.'
        ),
        array(
            'id' => 'description',
            'order' => 30,
            'type' => 'textarea',
            'default' => 'Official Website Desa',
            'label' => 'Meta Description',
            'min' => 10,
            'max' => 300
        ),
        array(
            'id' => 'keyword',
            'order' => 40,
            'label' => 'Meta Keyword',
            'default' => 'OpenSID,opensid,sid,SID,SID CRI,SID-CRI,sid cri,sid-cri,Sistem Informasi Desa,sistem informasi desa',
            'help' => 'Pisahkan setiap keyword dengan koma.'
        ),
        array(
            'id' => 'facebook_app_id',
            'order' => 50,
            'label' => 'Facebook App ID',
            'help' => 'Buat aplikasi dan dapatkan <a href="https://developers.facebook.com/apps/" target="_blank">App ID dari sini</a>.'
        ),
        array(
            'id' => 'twitter_type',
            'order' => 60,
            'label' => 'Twitter Card Type',
            'help' => 'Jenis card ditampilkan ketika halaman website anda di bagikan di twitter.',
            'type' => 'select',
            'default' => 'summary',
            'options' => array(
                'summary' => 'Keterangan',
                'summary_large_image' => 'Keterangan dengan Gambar',
                'app' => 'Aplikasi',
                'player' => 'Player'
            )
        ),
        array(
            'id' => 'twitter_website',
            'order' => 70,
            'label' => 'Twitter Website',
            'help' => 'Masukkan twitter username untuk website ini'
        ),
        array(
            'id' => 'twitter_creator',
            'order' => 80,
            'label' => 'Twitter Creator',
            'help' => 'Masukkan twitter username untuk sebagai pembuat tweet dari website ini'
        ),
    )
));
