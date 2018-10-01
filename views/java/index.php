<link rel="stylesheet" href="<?= base_url().JAVADIR ?>/views/assets/style.min.css?ver=<?= time() ?>">
<script type="text/javascript" src="<?= base_url().JAVADIR ?>/views/assets/script.min.js?ver=<?= time() ?>"></script>
<div class="content-wrapper">
	<section class="content-header">
		<h1>Theme Options</h1>
		<ol class="breadcrumb">
			<li><a href="<?=site_url('hom_sid')?>"><i class="fa fa-home"></i> Home</a></li>
			<li class="active">Theme Options</li>
		</ol>
	</section>
	<section class="content" id="maincontent">
        <div class="container">
        	<div class="row">
                <div class="col-lg-8 col-md-10 java-tab-container">
                    <div class="col-sm-2 col-xs-3 java-tab-menu">
                      <div class="list-group">
                       <?php $l=0; foreach ($main as $lkey => $ldata): ?>
                         <a href="#group-<?= $lkey ?>" class="list-group-item text-center<?= ($l===0)?' active':'' ?>" tabindex="<?= ($l+1)?>">
                           <h4 class="jicon <?= $ldata['icon'] ?>"></h4><br/><?= $ldata['label'] ?>
                         </a>
                       <?php $l++; endforeach; unset($l); ?>
                      </div>
                    </div>
                    <div class="col-sm-10 col-xs-9 java-tab">
                    <?php $i=0; foreach ($main as $groupkey => $groups): ?>
                        <div id="group-<?= $groupkey ?>" class="java-tab-content<?= ($i===0)?' active':'' ?>">
                            <h1 class="jicon <?= $groups['icon'] ?>"></h1>
                        </div>
                    <?php $i++; endforeach; unset($i); ?>
                    </div>
                </div>
          </div>
        </div>

		<br><br>
        <?php
        var_dump($main)
        ?>
	</section>
</div>
