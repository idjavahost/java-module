<link rel="stylesheet" href="<?= base_url().JAVADIR ?>/views/assets/style.min.css?ver=<?= time() ?>">
<script type="text/javascript" src="<?= base_url().JAVADIR ?>/views/assets/script.min.js?ver=<?= time() ?>"></script>
<?php echo java_action('java_theme_config_header', $main); ?>
<div class="content-wrapper">
	<section class="content-header">
		<h1>Theme Options</h1>
		<ol class="breadcrumb">
			<li><a href="<?=site_url('hom_sid')?>"><i class="fa fa-home"></i> Home</a></li>
			<li class="active">Theme Options</li>
		</ol>
	</section>
	<section class="content" id="maincontent">
        <form id="java-options-form" action="<?= base_url() ?>java_theme/save" method="post" class="form-horizontal">
            <div class="container">
            	<div class="row">
                    <div class="col-lg-8 col-md-10 java-tab-container is-loading">
                        <div class="col-xs-12 message-wrap"></div>
                        <div class="col-sm-2 col-xs-3 java-tab-menu">
                          <div class="list-group">
                           <?php $l=0; foreach ($main as $lkey => $ldata): ?>
                             <a href="#<?= $lkey ?>-options" class="list-group-item text-center<?= ($l===0)?' active':'' ?>" tabindex="<?= ($l+1)?>">
                               <h4 class="jicon <?= $ldata['icon'] ?>"></h4><br/><?= $ldata['label'] ?>
                             </a>
                           <?php $l++; endforeach; unset($l); ?>
                          </div>
                        </div>
                        <div class="col-sm-10 col-xs-9 java-tab">
                        <?php $i=0; foreach ($main as $groupkey => $groups): ?>
                            <div id="<?= $groupkey ?>-options" class="java-tab-content<?= ($i===0)?' active':'' ?>">
                                <h2 class="java-tab-heading"><i class="jicon <?= $groups['icon'] ?>"></i> <strong><?= $groups['label'] ?></strong></h2>
                                <fieldset id="<?= $groupkey ?>-set">
                                <?php $builder->set_group($groupkey); ?>
                                <?php foreach ($groups['fields'] as $field): ?>
                                    <?php echo $builder->build($field, 'horizontal') ?>
                                <?php endforeach; ?>
                                <?php echo java_action('java_theme_config_'.$groupkey, $groups); ?>
                                </fieldset>
                            </div>
                        <?php $i++; endforeach; unset($i); ?>
                            <div class="form-submit">
                                <?php echo java_action('java_theme_config_buttons', $main); ?>
                                <button type="reset" class="btn btn-default"><i class="jicon icon-renew"></i> <span>Reset</span></button>
                                <button type="submit" class="btn btn-success"><i class="jicon icon-check"></i> <span>Simpan</span></button>
                            </div>
                        </div>
                        <div class="col-xs-12 java-copyright">
                            <small>Theme options ini dibuat dan dikembangkan oleh developer team <a href="https://idjavahost.com/" target="_blank" title="ID JavaHost - Hosting, Domain, Server">ID JavaHost</a>. Hak Cipta <?= date('Y') ?>.</small>
                            <small class="pull-right"><a href="https://desa.idjavahost.com/report" target="_blank"><i class="fa fa-bug fa-fw"></i> Laporkan Kesalahan</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        // echo '<br><br><pre>';
        // var_dump($main);
        // echo '</pre>';
        ?>
	</section>
</div>
<div class="modal modal-java-preview" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-center"><img src=""></div>
    </div>
</div>
