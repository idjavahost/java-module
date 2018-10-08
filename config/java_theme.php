<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (file_exists(FCPATH.'themes/'.java_theme().'/config.php')) {
    include_once FCPATH.'themes/'.java_theme().'/config.php';
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
            'order' => 1,
            'value' => '1',
            'default' => '1',
            'options' => array('on' => 'On', 'off' => 'Off'),
            'help' => 'Aktifkan image slider pada halaman home.'
        ),
        array(
            'id' => 'sosmed',
            'label' => 'Tampilkan Sosmed',
            'type' => 'toggle',
            'order' => 10,
            'value' => '1',
            'default' => '1',
            'options' => array('on' => 'On', 'off' => 'Off'),
            'help' => 'Tampilkan sosial media ikon di header.'
        ),
        array(
            'id' => 'logotext',
            'label' => 'Teks Logo',
            'order' => 20,
            'help' => 'Akan ditampilan setelah gambar logo di header, kosongkan jika ingin menggunakan nama desa.'
        ),
        array(
            'id' => 'logo',
            'label' => 'Logo',
            'type' => 'upload',
            'order' => 30,
            'filetype' => 'image',
            'filepath' => 'logo',
            'maxwidth' => 100,
            'maxheight' => 100,
            'extensions' => array('png', 'jpg'),
        ),
        array(
            'id' => 'favicon',
            'label' => 'Favicon',
            'type' => 'upload',
            'help' => 'Unggah gambar yang cukup besar, akan otomatis membuat icon untuk safari tab, iphone, dan android icon.',
            'order' => 40,
            'filetype' => 'image',
            'filepath' => 'icon',
            'maxsize' => 2097152,
            'extensions' => array('png'),
        ),
        array(
            'id' => 'themecolor',
            'label' => 'Mobile Color',
            'order' => 50,
            'help' => 'Digunakan untuk warna tema browser pada Android, iPhone, dan Windows Phone.',
            'type' => 'colorpicker',
            'default' => '#f1f1f1'
        ),
    )
));

$config['footer'] = java_filters('java_theme_config_footer', array(
    'label'   => 'Footer',
    'icon'    => 'icon-widgets',
    'order'   => 20,
    'fields'  => array(
        array(
            'id' => 'copyright',
            'label' => 'Tambahan Teks',
            'help' => 'Tambahan teks untuk footer copyright.'
        ),
        array(
            'id' => 'widgets',
            'label' => 'Footer Widgets',
            'help' => 'Atur, urut, dan sesuaikan widget untuk bagian footer.',
            'type' => 'widgets'
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
            'order' => 10,
            'type' => 'select',
            'default' => 'kiri',
            'options' => array(
                'kiri' => 'Sidebar Kiri',
                'kanan' => 'Sidebar Kanan'
            )
        ),
        array(
            'id' => 'search',
            'label' => 'Tampilkan Pencarian',
            'type' => 'toggle',
            'order' => 20,
            'value' => '1',
            'default' => '1',
            'options' => array('on' => 'Ya', 'off' => 'Tidak'),
            'help' => 'Tampilkan bidang pencarian di sidebar.'
        ),
        array(
            'id' => 'config',
            'order' => 30,
            'label' => 'Sidebar Artikel',
            'help' => 'Atur, urut, dan sesuaikan widget untuk sidebar kanan atau kiri di semua halaman artikel.',
            'type' => 'widgets'
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
