<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JavaHost OpenSID Module.
 *
 * @since       0.0.1
 * @author      Rizal Fauzie <rizal@fauzie.my.id>
 * @copyright   PT. Java Digital Nusantara © 2018
 */

class Java_theme extends CI_Controller {

    public function __construct()
	{
		parent::__construct();
        session_start();
        $this->modul_ini = 13;
        $this->load->model('user_model');
		$grup	= $this->user_model->sesi_grup($_SESSION['sesi']);
		if ($grup != 1 AND $grup != 2 AND $grup != 3)
		{
			if (empty($grup))
				$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
			else
				unset($_SESSION['request_uri']);
			redirect('siteman');
		}
		$this->load->model('header_model');
        $this->load->model('web_artikel_model');
        $this->load->library('java_theme_config');
        $this->load->helper('java');
    }

    public function index()
    {
        $data = array('p'=>1,'o'=>1,'cat'=>0);

        $nav['act'] = 13;
		$nav['act_sub'] = 201;
        $header = $this->header_model->get_data();

		$data['main'] = $this->java_theme_config->get_configs();
		$data['builder'] = $this->java_theme_config->get_builder();

        $this->load->view('header', $header);
		$this->load->view('nav', $nav);
		$this->load->view('java/index', $data);
		$this->load->view('footer');
    }

    public function save()
    {
        $response = array('success' => false);

        if ($postdata = $this->input->post()) {
            if (!$this->java_theme_config->save_config($postdata)) {
                $response['message'] = $this->java_theme_config->last_error();
            } else {
                $response['success'] = true;
            }
        }

        header('Content-type:application/json;charset=utf-8');
        echo json_encode($response); exit();
    }

    public function upload($group, $field)
    {
        $found = false;
        $is_image = false;
        $configs = $this->java_theme_config->get_configs();
        $response = array('status' => 'fail', 'message' => 'File gagal di upload.');
        $max_size = java_file_upload_max_size();
        $max_w = 0;
        $max_h = 0;
        $types = '';
        $relpath = '';

        if (isset($configs[ $group ]) && isset($configs[ $group ]['fields'])) {
            foreach ($configs[ $group ]['fields'] as $fkey => $fdata) {
                if ($fdata['id'] == $field && $fdata['type'] == 'upload') {
                    if (isset($fdata['filepath'])) $relpath = trim($fdata['filepath'],'/');
                    if (isset($fdata['extensions'])) $types = implode($fdata['extensions'],'|');
                    if (isset($fdata['maxsize'])) $max_size = intval($fdata['maxsize']);
                    if ($fdata['filetype'] == 'image') {
                        if (isset($fdata['maxwidth'])) $max_w = intval($fdata['maxwidth']);
                        if (isset($fdata['maxheight'])) $max_h = intval($fdata['maxheight']);
                        $is_image = true;
                    }
                    $found = true;
                    break;
                }
            }
        }

        if ($found !== false && !empty($_FILES['file'])) {
            try {
                $fullpath = JAVAUPLOAD.$relpath.DS;

                if (!is_dir($fullpath)) {
                    mkdir($fullpath, 0777, true);
                }

                if (is_array($_FILES['file']['error'])) {
                    throw new RuntimeException('Parameter salah.');
                }

                switch ($_FILES['file']['error']) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        throw new RuntimeException('Tidak ada file terkirim.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('Ukuran file melebihi yang diperbolehkan.');
                    default:
                        throw new RuntimeException('Error tidak diketahui.');
                }

                $uploadconf = array(
                    'upload_path'       => $fullpath,
                    'allowed_types'     => $types,
                    'max_size'          => $max_size,
                    'file_ext_tolower'  => true,
                    'overwrite'         => true,
                    'remove_spaces'     => true
                );
                $this->load->library('upload', $uploadconf);

                if (!$this->upload->do_upload('file')) {

                    if (file_exists($fullpath.$_FILES['file']['name'])) {
                        unlink($fullpath.$_FILES['file']['name']);
                    }

                    if (!move_uploaded_file(
                        $_FILES['file']['tmp_name'],
                        $fullpath.$_FILES['file']['name']
                    )) {
                        throw new RuntimeException('Gagal mengunggah berkas.');
                    }

                    $image_data = array(
                        'rel_path'  => $relpath,
                        'file_path' => $fullpath,
                        'full_path' => $fullpath.$_FILES['file']['name'],
                        'file_name' => $_FILES['file']['name']
                    );

                } else {
                    $image_data = (array)$this->upload->data();
                    $image_data['rel_path'] = $relpath;
                }

                $this->load->library('image_lib');

                if ($group == 'general' && $field == 'favicon') {
                    $this->generate_favicon($image_data);
                }
                elseif ($is_image && $max_w>1 && $max_h>1) {

                    $this->image_lib->initialize(array(
                        'image_library'     => 'gd2',
                        'source_image'      => $image_data['full_path'],
                        'new_image'         => $image_data['full_path'],
                        'maintain_ratio'    => true,
                        'create_thumb'      => false,
                        'width'             => $max_w,
                        'height'            => $max_h
                    ));

                    if (!$this->image_lib->resize()) {
                        throw new RuntimeException($this->image_lib->display_errors('',''));
                    }
                    log_message('debug', print_r(array('image'=>$is_image, 'w' => $max_w, 'h' => $max_h),true));
                }

                $response = array(
                    'status' => 'success',
                    'message' => 'Upload success!',
                    'data' => $image_data
                );

            } catch (RuntimeException $e) {
                $response['message'] = $e->getMessage();
            } catch (Exception $e) {
                log_message('error', $e->getMessage() ."\n". $e->getTraceAsString());
            }
        }

        header('Content-type:application/json;charset=utf-8');
        echo json_encode($response); exit();
    }

    protected function generate_favicon($image)
    {
        // Apple Touch Icon
        $this->image_lib->initialize(array(
            'image_library'     => 'gd2',
            'source_image'      => $image['full_path'],
            'new_image'         => 'apple-touch-icon.png',
            'maintain_ratio'    => true,
            'width'             => 180,
            'height'            => 180
        ));
        if (!$this->image_lib->resize()) {
            throw new RuntimeException($this->image_lib->display_errors('',''));
        }
        $this->image_lib->clear();

        // Microsoft Win 8 & 10 Tiles.
        $this->image_lib->initialize(array(
            'image_library'     => 'gd2',
            'source_image'      => $image['full_path'],
            'new_image'         => 'mstile-150x150.png',
            'maintain_ratio'    => true,
            'width'             => 150,
            'height'            => 150
        ));
        if (!$this->image_lib->resize()) {
            throw new RuntimeException($this->image_lib->display_errors('',''));
        }
        $this->image_lib->clear();

        // 32x32 PNG Favicon
        $this->image_lib->initialize(array(
            'image_library'     => 'gd2',
            'source_image'      => $image['full_path'],
            'new_image'         => 'favicon-32x32.png',
            'maintain_ratio'    => true,
            'width'             => 32,
            'height'            => 32
        ));
        if (!$this->image_lib->resize()) {
            throw new RuntimeException($this->image_lib->display_errors('',''));
        }
        $this->image_lib->clear();

        // 16x16 PNG Favicon
        $this->image_lib->initialize(array(
            'image_library'     => 'gd2',
            'source_image'      => $image['full_path'],
            'new_image'         => 'favicon-16x16.png',
            'maintain_ratio'    => true,
            'width'             => 16,
            'height'            => 16
        ));
        if (!$this->image_lib->resize()) {
            throw new RuntimeException($this->image_lib->display_errors('',''));
        }
        $this->image_lib->clear();

        /**
         * Generate favicon for old browser (.ico)
         */
        $this->load->library('java_ico', $image['full_path']);
        $resultico = $this->java_ico->save_ico($image['file_path'].'favicon.ico');

        if ($resultico === false || !file_exists($image['file_path'].'favicon.ico')) {
            //throw new RuntimeException('Gagal membuat favicon.ico');
        }

        return $this;
    }
}
