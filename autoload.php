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
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
define('JAVAUPLOAD', FCPATH.'desa'.DS.'upload'.DS.'theme'.DS);

// Include everything on this module.
if(function_exists('get_instance')) get_instance()->load->add_package_path(JAVAPATH);
// Java Event listener required to load first.
if(!class_exists('Java_events')) include_once(JAVAPATH.'/libraries/Java_events.php');
