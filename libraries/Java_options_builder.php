<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JavaHost OpenSID Module.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */

class Java_options_builder {

    protected $CI;
    protected $_config;
    protected $_groupId;

    public function __construct($config = null)
    {
        $this->CI =& get_instance();

        if ($config !== null) {
            $this->_config = $config;
        }
    }

    public function set_group($groupId)
    {
        $this->_groupId = $groupId;
        return $this;
    }

    /**
     * Build form field.
     *
     * @param  array  $data
     * Sample:
     * $data = array(
     *   'id' => 'enable',
     *   'label' => 'Aktifkan Tema',
     *   'type' => 'toggle',
     *   'value' => '1',
     *   'default' => '1',
     *    'help' => 'Aktifkan pratampil tema untuk melihat sekilas tema anda.'
     * )
     *
     * @param  string $type bootstrap form type
     * @return string
     */
    public function build(array $data, $type = 'default')
    {
        if (!isset($data['id'])) {
            return '';
        }

        if (!isset($data['type'])) {
            $data['type'] = 'text';
        } elseif ($data['type'] == 'checkbox' || $data['type'] == 'switch') {
            $data['type'] = 'toggle';
        }

        $type_builder = 'render_'.$data['type'];

        if ( method_exists( $this, $type_builder ) ) {
            $field = call_user_func(array( $this, $type_builder ), $data);
            return $this->_label_render($type, $data, $field);
        }

        return '';
    }

    protected function _label_render($type, $data, $field = '')
    {
        if ($data['type'] == 'separator') {
            $html  = '<div class="form-group type-separator field-'.$this->_groupId.'--'.$data['id'].'">';
            $html .= '<div class="col-xs-12"><h4>'.ucwords($data['label']).'</h4></div>';
            $html .= '</div>';
            return $html;
        }

        $html = '<div class="form-group type-'.$data['type'].' field-'.$this->_groupId.'--'.$data['id'].'">';
        $req = (isset($data['required']) && $data['required']===true) ? '<small class="required">*</small> ':'';
        $help = '';
        if (!empty($data['help']) && $data['type'] != 'toggle') {
            $help = '<i class="jicon icon-info help-tip" data-title="'.$data['label'].'" data-content="<small>'.htmlspecialchars($data['help']).'</small>"></i>';
        }
        switch ($type) {
            case 'horizontal':
                $html .= '<label for="'.$this->_groupId.'-'.$data['id'].'" class="col-sm-3 control-label">'.$req.$data['label'].$help.'</label>';
                $html .= '<div class="col-sm-9">'.$field.'</div>';
                break;
            case 'inline':
                $html .= '<label for="'.$this->_groupId.'-'.$data['id'].'" class="sr-only">'.$data['label'].'</label>';
                $html .= $field;
                break;
            default:
                $html .= '<label for="'.$this->_groupId.'-'.$data['id'].'">'.$req.$data['label'].$help.'</label>';
                $html .= $field;
                break;
        }
        $html .= '</div>';
        return $html;
    }

    public function render_separator(array $data)
    {
        return '';
    }

    public function render_toggle(array $data)
    {
        $attrs = array(
            'type'          => 'checkbox',
            'value'         => !empty($data['default']) ? $data['default'] : '1',
            'name'          => $this->_groupId.'['.$data['id'].']',
            'id'            => $this->_groupId.'-'.$data['id'],
            'data-toggle'   => 'toggle'
        );

        if ($data['value'] == $attrs['value']) {
            $attrs['checked'] = 'checked';
        }
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';

        if (!empty($data['options'])) {
            if (isset($data['options']['on']))
                $attrs['data-on'] = trim($data['options']['on']);
            if (isset($data['options']['off']))
                $attrs['data-off'] = trim($data['options']['off']);
        }

        $html = '<label class="checkbox-inline">';
        $html .= java_build_html('input', $attrs);
        $html .= !empty($data['help']) ? ' '.$data['help'] : '';
        $html .= '</label>';
        return $html;
    }

    public function render_text(array $data)
    {
        $attrs = array(
            'type'          => 'text',
            'value'         => isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:''),
            'name'          => $this->_groupId.'['.$data['id'].']',
            'id'            => $this->_groupId.'-'.$data['id'],
            'class'         => 'form-control'
        );
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';
        if (isset($data['min'])) $attrs['minlength'] = intval($data['min']);
        if (isset($data['max'])) $attrs['maxlength'] = intval($data['max']);
        if (isset($data['prefix']) || isset($data['suffix'])) {
            $html  = '<div class="input-group">';
            if(isset($data['prefix'])) $html .= '<span class="input-group-addon"><i class="'.$data['prefix'].'"></i></span>';
            $html .= java_build_html('input', $attrs);
            if(isset($data['suffix'])) $html .= '<span class="input-group-addon"><i class="'.$data['suffix'].'"></i></span>';
            $html .= '</div>';
            return $html;
        }
        return java_build_html('input', $attrs);
    }

    public function render_textarea(array $data)
    {
        $attrs = array(
            'type'          => 'textarea',
            'rows'          => isset($data['rows']) ? intval($data['rows']) : 3,
            'name'          => $this->_groupId.'['.$data['id'].']',
            'id'            => $this->_groupId.'-'.$data['id'],
            'class'         => 'form-control'
        );
        $value = isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:'');
        $value = html_entity_decode($value);
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';
        if (isset($data['min'])) $attrs['minlength'] = intval($data['min']);
        if (isset($data['max'])) $attrs['maxlength'] = intval($data['max']);
        return java_build_html('textarea', $attrs, $value);
    }

    public function render_number(array $data)
    {
        $attrs = array(
            'type'          => 'number',
            'value'         => isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:''),
            'name'          => $this->_groupId.'['.$data['id'].']',
            'id'            => $this->_groupId.'-'.$data['id'],
            'class'         => 'form-control'
        );
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';
        if (isset($data['min'])) $attrs['min'] = intval($data['min']);
        if (isset($data['max'])) $attrs['max'] = intval($data['max']);
        if (isset($data['step'])) $attrs['step'] = intval($data['step']);
        return java_build_html('input', $attrs);
    }

    public function render_editor(array $data)
    {
        $attrs = array(
            'type'          => 'textarea',
            'rows'          => isset($data['rows']) ? intval($data['rows']) : 3,
            'name'          => $this->_groupId.'['.$data['id'].']',
            'id'            => $this->_groupId.'-'.$data['id'],
            'class'         => 'form-control wysihtml'
        );
        $value = isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:'');
        $value = html_entity_decode($value);
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';
        if (isset($data['min'])) $attrs['minlength'] = intval($data['min']);
        if (isset($data['max'])) $attrs['maxlength'] = intval($data['max']);
        if (isset($data['editor'])) {
            $attrs['data-config'] = json_encode($data['editor']);
        }
        return java_build_html('textarea', $attrs, $value);
    }

    public function render_icon_select(array $data)
    {
        $this->CI->load->helper('java_awesome');

        if (!function_exists('java_fa5_icons')) {
            return '';
        }

        $data['options']        = java_fa5_icons();
        $data['xclass']         = 'icon-select';
        $data['select_options'] = array(
            'placeholder' => 'Pilih ikon...',
            'minimum-results-for-search' => '1'
        );

        return $this->render_select($data);
    }

    public function render_select(array $data)
    {
        if (!isset($data['options'])) {
            $data['options'] = array( 'Tidak', 'Ya' );
        }
        $value = isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:'');
        $attrs = array(
            'name'          => $this->_groupId.'['.$data['id'].']',
            'id'            => $this->_groupId.'-'.$data['id'],
            'class'         => 'form-control select-control'
        );
        if (isset($data['xclass']) && trim($data['xclass'])) {
            $attrs['class'] .= ' '.trim(htmlspecialchars($data['xclass'], ENT_QUOTES));
        }
        if (isset($data['multiple']) && $data['multiple']===true) {
            $attrs['multiple'] = 'multiple';
            $attrs['name'] = $this->_groupId.'['.$data['id'].'][]';
            $value = explode(',', $value);
        }
        if (!empty($data['select_options']) && is_array($data['select_options'])) {
            foreach ($data['select_options'] as $sokey => $sodata) {
                $sokey = 'data-' . strtolower(htmlentities($sokey));
                if (is_array($sodata)) {
                    foreach ($sodata as $sodata_key => $sodata_val) {
                        $attrs[ $sokey.'--'.strtolower($sodata_key) ] = htmlentities($sodata_val, ENT_QUOTES);
                    }
                } else {
                    $attrs[ $sokey ] = htmlentities($sodata, ENT_QUOTES);
                }
            }
        }
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';

        $options = '';
        foreach ((array)$data['options'] as $opt_key => $opt_label) {
            if (isset($attrs['multiple']) && is_array($value)) {
                $selected = (in_array(trim($opt_key), $value)) ? ' selected' : '';
            } else {
                $selected = ($value == $opt_key) ? ' selected' : '';
            }
            $options .= '<option value="'.htmlentities($opt_key).'"'.$selected.'>'.htmlentities($opt_label).'</option>';
        }
        return java_build_html('select', $attrs, $options);
    }

    public function render_upload(array $data)
    {
        $id = $this->_groupId.'-'.$data['id'];
        $name = $this->_groupId.'['.$data['id'].']';
        $col_input = 'col-sm-12';
        $max_size = !empty($data['maxsize']) ? (int)$data['maxsize'] : java_file_upload_max_size();
        $filepath = isset($data['filepath']) ? trim($data['filepath'],'/').'/' : '';
        $desc = 'Max '.java_format_byte($max_size);
        if (isset($data['extensions'])) $desc .= ' ('. implode((array)$data['extensions'], ', ') . ')';
        if ($data['filetype'] == 'image') {
            if (isset($data['maxwidth'])) $desc .= ', Lebar '.intval($data['maxwidth']).'px';
            if (isset($data['maxheight'])) $desc .= ', Tinggi '.intval($data['maxheight']).'px';
        }

        if (!empty($data['value'])) {
            $txtval = $data['value'];
            $img_src = base_url().'desa/upload/theme/'.$data['value'];
            $value = '<img src="'.$img_src.'" class="img-preview img-responsive"';
            if (isset($data['maxwidth'])) $value .= ' width="'.intval($data['maxwidth']).'"';
            if (isset($data['maxheight'])) $value .= ' height="'.intval($data['maxheight']).'"';
            $value .= '/>';
        } else {
            $txtval = '';
            $value = '<img src="'.base_url().'vendor/java/views/assets/placeholder.png" class="img-responsive" />';
        }

        $options = array(
            'url' => base_url().'java_theme/upload/'.$this->_groupId.'/'.$data['id'],
            'maxFileSize' => $max_size,
            'allowedTypes' => ($data['filetype'] == 'image') ? 'image/*' : '*',
            'extFilter' => empty($data['extensions']) ? array() : (array)$data['extensions']
        );

        $html = '<div id="'.$id.'" class="dm-uploader row no-gutters" data-options=\''.json_encode($options).'\'>';
        if ($data['filetype'] == 'image') {
            $col_input = 'col-sm-10';
            $html .= '<div class="uploader-preview col-sm-2 col-xs-12">';
            $html .= '<div class="image-preview">'.$value.'</div>';
            $html .= '</div><!--.uploader-preview-->';
        }
        $html .= '  <div class="uploader-input '.$col_input.' col-xs-12">';
        $html .= '    <div class="form-group">';
        $html .= '      <input type="text" class="form-control" placeholder="Tidak ada file terpilih..." ';
        $html .= '       value="'.$txtval.'" name="'.$name.'" readonly="readonly"/>';
        $html .= '    </div>';
        $html .= '    <div class="form-group">';
        $html .= '      <button type="button" class="btn btn-primary btn-sm">';
        $html .= '          <i class="fa fa-folder-o fa-fw"></i> Pilih File';
        $html .= '          <input type="file" title="Click to add Files">';
        $html .= '      </button>';
        $html .= '      <small class="status text-muted">'.$desc.'</small>';
        $html .= '    </div><!--.form-group-->';
        $html .= '  </div><!--.uploader-input-->';
        $html .= '</div><!--.dm-uploader-->';

        return $html;
    }

    public function render_slider(array $data)
    {
        $val = isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:'');
        $id  = $this->_groupId.'-'.$data['id'];
        $attrs = array(
            'type'          => 'text',
            'value'         => $val,
            'name'          => $this->_groupId.'['.$data['id'].']',
            'class'         => 'bootstrap-slider',
            'data-provide'  => 'slider'
        );
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';

        if (!empty($data['slider']) && is_array($data['slider'])) {
            foreach ($data['slider'] as $skey => $svalue) {
                $datakey = 'data-slider-'.$skey;
                if (is_array($svalue)) $svalue = json_encode($svalue);
                $attrs[ $datakey ] = $svalue;
            }
        }
        $attrs['data-slider-id'] = $id;
        $attrs['data-slider-value'] = $val;

        $html = java_build_html('input', $attrs);
        $html .= '<span id="'.$id.'-value" class="slider-value">'.(!empty($val)?$val:'0').'</span>';
        return $html;
    }

    public function render_colorpicker(array $data)
    {
        $val = isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:'');
        $attrs = array(
            'type'          => 'text',
            'value'         => $val,
            'id'            => $this->_groupId.'-'.$data['id'],
            'name'          => $this->_groupId.'['.$data['id'].']',
            'class'         => 'form-control',
            'autocomplete'  => 'off'
        );
        $config = array('color' => $val );
        if (isset($data['required']) && $data['required']===true) $attrs['required'] = 'required';
        if (isset($data['min'])) $attrs['minlength'] = intval($data['min']);
        if (isset($data['max'])) $attrs['maxlength'] = intval($data['max']);
        if (isset($data['format'])) $config['format'] = $data['format'];
        if (isset($data['align'])) $config['align'] = $data['align'];

        $html  = '<div class="input-group colorpicker-component" data-config=\''.json_encode($config).'\'>';
        $html .= '<span class="input-group-addon"><i></i></span>';
        $html .= java_build_html('input', $attrs);
        $html .= '</div>';
        return $html;
    }

    public function render_widgets(array $data)
    {
        $source = !isset($data['source']) ? 'widget' : trim($data['source']);
        $val = isset($data['value']) ? $data['value'] : (isset($data['default'])?$data['default']:'');
        $id = $this->_groupId.'-'.$data['id'];
        $availables = java_get_widgets();
        $active = empty($val) ? array() : (!is_array($val) ? explode(',', $val) : $val);

        $attrs = array(
            'type'  => 'hidden',
            'name'  => $this->_groupId.'['.$data['id'].']',
            'value' => $val
        );

        $activehtml = '<ul id="'.$id.'-ac" class="sort-active">';
        foreach ($active as $itemid) {
            $avkey = java_array_search($itemid, $availables);
            if ($avkey === null) continue;
            $item = $availables[ $avkey ];
            $activehtml .= '<li class="item" data-id="'.$item['id'].'" data-desc="'.htmlentities($item['desc'],ENT_QUOTES).'">';
            $activehtml .= '<span class="item-block">';
            if (!empty($item['icon'])) $activehtml .= '<i class="'.$item['icon'].'"></i>';
            $activehtml .= '<span>'.$item['name'].'</span>';
            $activehtml .= '</span>';
            $activehtml .= '<span class="item-tools"><i class="fa fa-times item-remove"></i><i class="fa fa-caret-down item-detail"></i></span>';
            $activehtml .= '<div class="details">'.htmlentities($item['desc'],ENT_QUOTES);
            $activehtml .= '</div>';
            $activehtml .= '</li>';
            unset($availables[ $avkey ]);
        }
        $activehtml .= '</ul><!-- .sort-active -->';

        $avahtml = '<ul id="'.$id.'-av" class="sort-available">';
        foreach ($availables as $wkey => $widget) {
            $avahtml .= '<li class="item" data-id="'.$wkey.'" data-desc="'.htmlentities($widget['desc'],ENT_QUOTES).'">';
            $avahtml .= '<span class="item-block">';
            if (!empty($widget['icon'])) $avahtml .= '<i class="'.$widget['icon'].'"></i>';
            $avahtml .= '<span>'.$widget['name'].'</span>';
            $avahtml .= '</span>';
            $avahtml .= '</li>';
        }
        $avahtml .= '</ul><!-- .sort-available -->';

        $html  = '<div id="'.$id.'" class="sortable-component row">';
        $html .= '<div class="col-xs-4">';
        $html .= '<h5 class="head">Tersedia</h5>';
        $html .= $avahtml;
        $html .= '</div>';
        $html .= '<div class="col-xs-8">';
        $html .= '<h5 class="head">Aktif</h5>';
        $html .= $activehtml;
        $html .= java_build_html('input', $attrs);
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
