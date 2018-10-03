<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['general'] = array(
    'label'   => 'General',
    'icon'    => 'icon-cog',
    'order'   => 1,
    'fields'  => array(
        array(
            'id' => 'enable',
            'label' => 'Homepage Image',
            'type' => 'toggle',
            'value' => '1',
            'default' => '1',
            'options' => array('on' => 'On', 'off' => 'Off'),
            'help' => 'Aktifkan image slider pada halaman home.'
        ),
        array(
            'id' => 'logo',
            'label' => 'Logo',
            'type' => 'upload',
            'filetype' => 'image',
            'filepath' => 'logo',
            'maxwidth' => 100,
            'maxheight' => 100,
            'extensions' => array('png', 'jpg'),
        ),
        array(
            'id' => 'logotext',
            'label' => 'Teks Logo',
            'help' => 'Akan ditampilan setelah gambar logo di header, kosongkan jika ingin menggunakan nama desa.'
        ),
        array(
            'id' => 'favicon',
            'label' => 'Favicon',
            'type' => 'upload',
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
            'filetype' => 'image',
            'filepath' => 'icon',
            'maxwidth' => 180,
            'maxheight' => 180,
            'extensions' => array('png'),
        ),
        array(
            'id' => 'themecolor',
            'label' => 'Mobile Color',
            'help' => 'Digunakan untuk warna tema browser pada Android, iPhone, dan Windows Phone.',
            'type' => 'colorpicker',
            'default' => '#f1f1f1'
        ),
    )
);

foreach (glob(FCPATH.'themes/java-*') as $javathemedir) {
    if (file_exists($javathemedir.'/config.php')) {
        include_once $javathemedir.'/config.php';
    }
}

$config['sosmed'] = array(
    'label'   => 'Sosial Media',
    'icon'    => 'icon-announcement',
    'order'   => 90,
    'fields'  => array(
        array(
            'id' => 'enable',
            'label' => 'Sosial Media',
            'type' => 'toggle',
            'value' => '1',
            'default' => '1',
            'help' => 'Tampilkan sosial media di header.'
        ),
        array(
            'id' => 'facebook',
            'label' => 'Facebook Page',
            'prefix' => 'fa fa-facebook',
            'default' => 'https://facebook.com/'
        ),
        array(
            'id' => 'twitter',
            'label' => 'Twitter Profile',
            'prefix' => 'fa fa-twitter',
            'default' => 'https://twitter.com/'
        ),
        array(
            'id' => 'gplus',
            'label' => 'Google Plus Page',
            'prefix' => 'fa fa-google-plus',
            'default' => 'https://plus.google.com/'
        ),
        array(
            'id' => 'instagram',
            'label' => 'Instagram URL',
            'prefix' => 'fa fa-instagram',
            'default' => 'https://instagram.com/'
        ),
        array(
            'id' => 'youtube',
            'label' => 'YouTube Channel',
            'prefix' => 'fa fa-youtube',
            'default' => 'https://youtube.com/'
        ),
    )
);

$config['seo'] = array(
    'label'   => 'SEO Settings',
    'icon'    => 'icon-extension',
    'order'   => 99,
    'fields'  => array(
        array(
            'id' => 'title',
            'label' => 'Page Title',
            'required' => true,
            'default' => 'Official Web Desa'
        ),
        array(
            'id' => 'description',
            'type' => 'textarea',
            'default' => 'Official Website Desa',
            'label' => 'Meta Description',
            'min' => 10,
            'max' => 300
        ),
        array(
            'id' => 'keyword',
            'label' => 'Meta Keyword',
            'default' => 'OpenSID,opensid,sid,SID,SID CRI,SID-CRI,sid cri,sid-cri,Sistem Informasi Desa,sistem informasi desa',
            'help' => 'Pisahkan setiap keyword dengan koma.'
        ),
        array(
            'id' => 'facebook_app_id',
            'label' => 'Facebook App ID',
            'help' => 'Buat aplikasi dan dapatkan <a href="https://developers.facebook.com/apps/" target="_blank">App ID dari sini</a>.'
        ),
        array(
            'id' => 'twitter_type',
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
            'label' => 'Twitter Website',
            'help' => 'Masukkan twitter username untuk website ini'
        ),
        array(
            'id' => 'twitter_creator',
            'label' => 'Twitter Creator',
            'help' => 'Masukkan twitter username untuk sebagai pembuat tweet dari website ini'
        ),
    )
);
