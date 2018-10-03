<?php
function is_server_connected() {
    $connected = fopen("http://www.google.com:80/","r");
    return ($connected);
}
function java_download_module() {
    $url = 'https://github.com/idjavahost/java-module/archive/master.zip';
    $zipFile = FCPATH."vendor/java.zip"; // Local Zip File Path
    $zipResource = fopen($zipFile, "w");
    // Get The Zip File From Server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FILE, $zipResource);
    $page = curl_exec($ch);
    if (!$page) {
        return "Error :- ".curl_error($ch);
    }
    curl_close($ch);

    /* Open the Zip file */
    $zip = new ZipArchive;
    $extractPath = FCPATH.'vendor';
    if($zip->open($zipFile) != "true"){
        return "Error :- Gagal membuka zip module archive.";
    }
    /* Extract Zip File */
    $zip->extractTo($extractPath);
    $zip->close();
    rename(FCPATH.'vendor/java-module-master', FCPATH.'vendor/java');
    return true;
}
$themepath  = FCPATH.$this->theme_folder.'/'.$this->theme.'/assets/';

if (isset($_GET['action'])) {
    $response = array('success' => false, 'message' => 'Terjadi kesalahan, silahkan coba lagi.');

    try {
        switch ($this->input->get('action')) {
            case 'validate':
                $response['data'] = array();
                $response['data'][] = array(
                    'msg' => 'Cek folder <code>'. FCPATH . 'desa</code>...',
                    'res' => (is_dir(FCPATH.'desa') && is_writable(FCPATH.'desa'))
                );
                $response['data'][] = array(
                    'msg' => 'Cek OpenSID config <code>'. FCPATH . 'desa/config/config.php</code>...',
                    'res' => is_writable(FCPATH.'desa/config/config.php')
                );
                break;
            case 'install':
                $excludes = FCPATH.'.git/info/exclude';
                $configfile = FCPATH.'desa/config/config.php';

                if (!is_dir(FCPATH.'vendor/java')) {
                    $downres = java_download_module();
                    if ($downres !== true) {
                        throw new Exception($downres);
                    }
                }
                copy(FCPATH.'vendor/java/controllers/Java_theme.php', APPPATH.'controllers/Java_theme.php');

                if (is_dir(FCPATH.'.git') && is_writable($excludes)) {
                    $ex_data = "\n.htaccess\nthemes/java-*\nvendor/java\ndonjo-app/controllers/Java_theme.php\n";
                    file_put_contents($excludes, $ex_data);
                }

                $CI =& get_instance();
                $q = $CI->db->query("SELECT url FROM setting_modul WHERE url = 'java_theme'");
                if ($q->num_rows() == 0) {
                    $CI->db->insert('setting_modul', array(
                        'modul' => 'Theme Options',
                        'url'   => 'java_theme',
                        'aktif' => '1',
                        'ikon'  => 'fa-paint-brush',
                        'urut'  => '99',
                        'level' => '4',
                        'hidden'=> '0',
                        'parent'=> '13'
                    ));
                }
                $CI->db->query("
                CREATE TABLE IF NOT EXISTS `java_config` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `config_key` varchar(255) NULL,
                    `config_value` text NULL,
                    `update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE `UNIQUE_KEY` (`config_key`),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB;
                ");

                $file_data  = file_get_contents($configfile);
                $file_data .= "\n\nif (!defined('FCPATH')) define('FCPATH', dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR);";
                $file_data .= "\nrequire_once FCPATH.'vendor/java/autoload.php';\n";
                file_put_contents($configfile, $file_data);

                $response['data'] = array();
                $response['data'][] = array(
                    'msg' => 'Install module JavaHost di <code>'. FCPATH.'vendor/java ...',
                    'res' => (is_dir(FCPATH.'vendor/java') && is_writable(FCPATH.'vendor/java'))
                );
                break;
        }
        foreach ($response['data'] as $data) {
            if (isset($data['res']) && $data['res'] === true) {
                $response['success'] = true;
            } else {
                $response['success'] = false;
                break;
            }
        }

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-type: application/json');
    echo json_encode($response);
    exit();
}
?>
<div id="page">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-8 text-center">
                <img src="<?= base_url().$this->theme_folder.'/'.$this->theme; ?>/assets/img/javahost_logo.png" alt="">
                <h1>Selesaikan Instalasi Tema</h1>
                <p class="lead outer">Selangkah lagi anda akan akan bisa menggunakan tema dari kami.</p>
                <?php if (!is_server_connected()): ?>
                    <div class="alert alert-danger"><h4>Mohon Maaf, Setup Theme Memerlukan Koneksi Internet</h4><small>Hubungkan terlebih dahulu ke internet atau coba periksa kembali koneksi server anda.</small></div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <span id="validate" class="step-group active" data-desc="Disini akan mengecek semua file yang dibutuhkan oleh tema kami." tabindex="1">
                                    <span class="num">1</span>
                                    <span class="txt">Validation</span>
                                </span>
                                <span id="install" class="step-group" data-desc="Mohon tunggu instalasi module Java pada OpenSID anda." tabindex="2">
                                    <span class="num">2</span>
                                    <span class="txt">Install</span>
                                </span>
                                <span id="finish" class="step-group" data-desc="Instalasi selesai. Terima kasih telah menggunakan tema kami." tabindex="3">
                                    <span class="num">3</span>
                                    <span class="txt">Finish</span>
                                </span>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted">Disini akan mengecek semua file yang dibutuhkan oleh tema kami.</h6>
                            <div class="card-content">
                                <div id="validate-content" class="step-content">
                                    <ul class="list-group list-group-flush" style="display:none">
                                        <li class="list-group-item">
                                            <span>Cek folder <code><?= FCPATH . 'desa' ?></code>...</span>
                                            <span class="state loading">&#10019;</span>
                                        </li>
                                    </ul>
                                </div>
                                <div id="install-content" class="step-content" style="display:none">
                                    <p>&nbsp;</p>
                                    <p class="lead">Menginstall Module JavaHost ...</p>
                                    <p>&nbsp;</p>
                                </div>
                                <div id="finish-content" class="step-content" style="display:none">
                                    <p><span class="icon">&#9787;</span></p>
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-6 text-right">
                                            <a href="<?= base_url() ?>/siteman">
                                                <h4>&#8592; Admin Area</h4>
                                            </a>
                                        </div>
                                        <div class="col-6 text-left">
                                            <a href="<?= base_url() ?>/first">
                                                <h4>Halaman Depan &#8594;</h4>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-lg btn-success btn-start">&#9733; Mulai</button>
                            <div class="loader" style="display:none">Loading...</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    var steps = {
        validate: {
            complete: false,
            required: []
        },
        install: {
            complete: false,
            required: [ 'validate' ]
        },
        finish: {
            complete: false,
            required: [ 'validate', 'install' ]
        }
    }, activeStep = 'validate',
    stateLoading = '<span class="state loading">&#10019;</span>',
    stateFailed  = '<span class="state failed">&#10006;</span>',
    stateFinish   = '<span class="state finish">&#10004;</span>';

    var showStep = function(step) {
        $('.step-group').removeClass('active');
        $('#'+step).addClass('active');
        $('.card-subtitle').html( $('#'+step).attr('data-desc') );
        $('.step-content').hide();
        $('#'+step+'-content').show();
    };

    var checkRequire = function(step) {
        var isValid = false;
        if (step.required) {
            for (var i = 0; i < step.required.length; i++) {
                var reqstep = step.required[i];
                if (steps[reqstep] && steps[reqstep].complete === true) {
                    isValid = true;
                } else {
                    isValid = false;
                }
            }
        }
        return isValid;
    };

    var markFinish = function(arrs) {
        for (var i = 0; i < arrs.length; i++) {
            var step = arrs[i];
            $('#'+step).removeClass('active fail').addClass('done');
        }
    };

    var apendProgress = function(action, data, nextData) {
        var wrap = $('#'+action+'-content>ul');
        var lastLi = ($('li', wrap).length > 1) ? $('li', wrap).last() : $('li', wrap);
        var content = '<li class="list-group-item"><span>'+ data.msg +'</span>';
        if (data.res === true) {
            content += stateFinish;
        } else {
            content += stateFailed;
        }
        content += '</li>';
        if (typeof nextData !== 'undefined') {
            content += '<li class="list-group-item"><span>'+ nextData.msg +'</span>';
            content += stateLoading;
        }
        if (wrap.length) {
            lastLi.remove();
            wrap.append(content).show();
        }
    };

    $('.step-group').on('click', function(e) {
        e.preventDefault();
        var id = $(this).attr('id'), step = steps[id];
        if (!step.complete && checkRequire(step)) {
            $(this).addClass('active');
            markFinish(step.required);
            showStep( $(this).attr('id') );
        }
    });

    $('.btn-start').on('click', function(e) {
        e.preventDefault();
        var el = $(this);
        $(this).hide();
        $('.card-body .loader').show();
        $('#validate-content>ul').show();
        $.getJSON('/first?action=validate', function(response) {
            if (response.success && response.data) {
                steps.validate.complete = true;
                $.each(response.data, function(i, data) {
                    var out = (3000 * (i+1));
                    setTimeout(function() {
                        apendProgress('validate', data, response.data[i+1]);
                        if (typeof response.data[i+1] === 'undefined') {
                            steps.validate.complete = true;
                            $('.card-body .loader').hide();
                            $('.card-body').append('<button type="button" class="btn btn-lg btn-success btn-continue">Lanjutkan &xrarr;</button>');
                            el.remove();
                        }
                    }, out);
                });
            } else {
                var msg = response.message ? response.message : 'Terjadi kesalahan, silahkan coba lagi.';
                alert(msg);
                window.location.reload(true);
            }
        });
    });

    $(document).on('click', 'button.btn-continue', function(e) {
        e.preventDefault();
        $('#install').addClass('active');
        markFinish(steps.install.required);
        showStep('install');
        $(this).hide();
        $('.card-body .loader').show();
        $.getJSON('/first?action=install', function(response) {
            if (response.success && response.data) {
                $('#finish').addClass('active');
                markFinish(steps.finish.required);
                showStep('finish');
                $('button.btn-continue').remove();
                $('.card-body .loader').hide();
                steps.install.complete = true;
                steps.finish.complete = true;
            } else {
                var msg = response.message ? response.message : 'Terjadi kesalahan, silahkan coba lagi.';
                alert(msg);
                window.location.reload(true);
            }
        });
    });
});
</script>
