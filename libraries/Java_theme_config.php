<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Java_options_builder.php';

class Java_theme_config {

    protected $CI;
    protected $_configs;
    protected $_builder;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('java');
        $this->CI->config->load('java_theme', true);
    }

    public function get_configs()
    {
        if ($this->_configs === null) {
            $options = (array)$this->CI->config->item('java_theme');
            $values  = (array)$this->CI->db->get('java_config')->result();
            $this->_configs = $this->_mergefields($options, $values);
        }
        return $this->_configs;
    }

    public function get_builder()
    {
        if ($this->_builder === null) {
            $this->_builder = new Java_options_builder($this);
        }
        return $this->_builder;
    }

    private function _mergefields($options, $values)
    {
        $dbdata = array();
        foreach ($values as $dbval) {
            if (!isset($dbval['config_value'])) continue;
            if (java_is_serialize($dbval['config_value'])) {
                $dbdataval = unserialize($dbval['config_value']);
            } elseif (is_numeric($dbval['config_value'])) {
                $dbdataval = (int)$dbval['config_value'];
            } else {
                $dbdataval = trim($dbval['config_value']);
            }
            $dbdata[ trim($dbval['config_key']) ] = $dbdataval;
        }

        usort($options, function($a, $b) {
            return isset($a['order']) && isset($b['order']) && ($a['order'] - $b['order']);
        });

        foreach ($options as $groupkey => &$groupdata) {
            if (!isset($groupdata['fields'])) continue;
            foreach ($groupdata['fields'] as &$data) {
                $dbkey = "$groupkey/".$data['id'];
                if (isset($dbdata[ $dbkey ])) {
                    $data['value'] = $dbdata[ $dbkey ];
                } elseif (isset($data['default'])) {
                    $data['value'] = $data['default'];
                } else {
                    $data['value'] = null;
                }
            }
        }
        return $options;
    }
}
