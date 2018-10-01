<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Java_options_builder {

    protected $CI;
    protected $_config;

    public function __construct($config = null)
    {
        $this->CI =& get_instance();

        if ($config !== null) {
            $this->_config = $config;
        }
    }

    public function build(array $data)
    {
        if (!isset($data['type']) || !isset($data['id'])) {
            return '';
        }

        $type_builder = '_render_'.$data['type'];

        if ( method_exists( $this, $type_builder ) ) {
            return call_user_func(array( $this, $type_builder ), $data);
        }

        return '';
    }
}
