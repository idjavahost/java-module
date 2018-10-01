<?php
/**
 * JavaHost OpenSID Module.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */

// Base constant
define('JAVAPATH', __DIR__);
define('JAVADIR', 'vendor/java');


// Include everything on this module.
get_instance()->load->add_package_path(JAVAPATH);
