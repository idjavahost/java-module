<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JavaHost OpenSID Module.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */

require_once 'Java_options_builder.php';

class Java_theme_config {

    protected $CI;
    protected $_configs;
    protected $_builder;
    protected $_values;
    protected $_last_error;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('java');
        $this->CI->config->load('java_theme', true);
    }

    public function set_config($key, $value)
    {
        if (isset($this->_values[ $key ]) ) {
            $this->_values[ $key ] = $value;
        }
        return $this;
    }

    public function get_config($key)
    {
        $configs = $this->get_values();
        if (array_key_exists($key, $configs)) {
            return $configs[ $key ];
        } else {
            $options = $this->get_configs();
            $keys = explode('/', $key);
            if (
                isset($options[ $keys[0] ]) &&
                isset($options[ $keys[0] ]['values']) &&
                isset($options[ $keys[0] ]['values'][ $keys[1] ])
            ) {
                $value = $options[ $keys[0] ]['values'][ $keys[1] ];
                if (is_numeric($value)) {
                    $value = (int)$value;
                } else {
                    $value = trim($value);
                }
                return $value;
            }
        }
        return null;
    }

    public function get_configs()
    {
        if ($this->_configs === null) {
            $options = (array)$this->CI->config->item('java_theme');
            $this->_configs = $this->_mergefields($options);
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

    public function get_values()
    {
        if ($this->_values === null) {
            $valuedb = (array)$this->CI->db->get('java_config')->result();
            $this->_values = array();
            foreach ($valuedb as $dbval) {
                if (!isset($dbval->config_value)) continue;
                if (java_is_serialize($dbval->config_value)) {
                    $dbdataval = unserialize($dbval->config_value);
                } elseif (java_is_json($dbval->config_value)) {
                    $dbdataval = json_decode($dbval->config_value, true);
                } elseif (is_numeric($dbval->config_value)) {
                    $dbdataval = (int)$dbval->config_value;
                } else {
                    $dbdataval = trim($dbval->config_value);
                }
                $this->_values[ trim($dbval->config_key) ] = $dbdataval;
            }
        }
        return $this->_values;
    }

    private function _mergefields($options)
    {
        $dbdata = $this->get_values();
        uasort($options, function($a, $b) {
            return isset($a['order']) && isset($b['order']) && ($a['order'] > $b['order']);
        });
        foreach ($options as $groupkey => &$groupdata) {
            if (!isset($groupdata['fields'])) continue;
            usort($groupdata['fields'], function($a, $b) {
                if (!(isset($a['order']) && isset($b['order']))) return 1;
                return isset($a['order']) && isset($b['order']) && ($a['order'] > $b['order']);
            });
            foreach ($groupdata['fields'] as $ky => $data) {
                $datavalue = null;
                $dbkey = trim("$groupkey/".$data['id']);
                if (array_key_exists($dbkey, $dbdata)) {
                    $datavalue = $dbdata[ $dbkey ];
                } elseif (isset($data['default'])) {
                    $datavalue = trim($data['default']);
                }
                $groupdata['fields'][ $ky ]['value'] = $datavalue;
                $groupdata['values'][ $data['id'] ] = $datavalue;
            }
        }
        return $options;
    }

    public function save_config(array $configs)
    {
        $this->CI->db->db_debug = FALSE;
        $this->_last_error = null;
        $options = $this->get_configs();
        $dbdata  = $this->get_values();
        $updates = array();
        $inserts = array();

        try {
            foreach ($options as $groupid => &$group) {
                if (!isset($configs[ $groupid ])) continue;
                foreach ($group['fields'] as $field) {
                    if (!isset($configs[ $groupid ][ $field['id'] ])) {
                        if (!in_array($field['type'], array('toggle','checkbox','switch'))) continue;
                        $configs[ $groupid ][ $field['id'] ] = '0';
                    }
                    $value = trim($configs[ $groupid ][ $field['id'] ]);
                    $dbkey = trim($groupid.'/'.$field['id']);
                    if (array_key_exists($dbkey, $dbdata) && !isset($updates[ $dbkey ])) {
                        if ($dbdata[ $dbkey ] != $value) {
                            $updates[ $dbkey ] = array(
                                'config_key' => $dbkey,
                                'config_value' => $value,
                                'update' => date('Y-m-d H:i:s')
                            );
                        }
                    } elseif (!isset($inserts[ $dbkey ])) {
                        $inserts[ $dbkey ] = array(
                            'config_key' => $dbkey,
                            'config_value' => $value
                        );
                    }
                }
            }

            if (!empty($updates))
                $this->CI->db->update_batch('java_config', $updates, 'config_key');

            if (!empty($inserts))
                $this->CI->db->insert_batch('java_config', $inserts);

            if (($sqlerr = $this->CI->db->error()) && !empty($sqlerr['message'])) {
                throw new Exception($sqlerr['message']);
            }

            return true;

        } catch (Exception $e) {
            $this->_last_error = $e->getMessage();
        }

        return false;
    }

    public function last_error()
    {
        return $this->_last_error;
    }
}
