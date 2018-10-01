<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['general'] = array(
    'label'   => 'General',
    'icon'    => 'icon-home',
    'order'   => 1,
    'fields'  => array(
        array(
            'id' => 'enable',
            'label' => 'Aktifkan Tema',
            'type' => 'toggle',
            'value' => '1',
            'default' => '1',
            'help' => 'Aktifkan pratampil tema untuk melihat sekilas tema anda.'
        ),
        array(
            'id' => 'enable',
            'label' => 'Aktifkan Tema',
            'type' => 'toggle',
            'value' => '1',
            'default' => '1',
            'help' => 'Aktifkan pratampil tema untuk melihat sekilas tema anda.'
        )
    )
);

foreach (glob(FCPATH.'themes/java-*') as $javathemedir) {
    if (file_exists($javathemedir.'/config.php')) {
        include_once $javathemedir.'/config.php';
    }
}
