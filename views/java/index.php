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
                        <a href="#" class="list-group-item active text-center" tabindex="1">
                          <h4 class="glyphicon glyphicon-plane"></h4><br/>Flight
                        </a>
                        <a href="#" class="list-group-item text-center">
                          <h4 class="glyphicon glyphicon-road"></h4><br/>Train
                        </a>
                        <a href="#" class="list-group-item text-center">
                          <h4 class="glyphicon glyphicon-home"></h4><br/>Hotel
                        </a>
                        <a href="#" class="list-group-item text-center">
                          <h4 class="glyphicon glyphicon-cutlery"></h4><br/>Restaurant
                        </a>
                        <a href="#" class="list-group-item text-center">
                          <h4 class="glyphicon glyphicon-credit-card"></h4><br/>Credit Card
                        </a>
                      </div>
                    </div>
                    <div class="col-sm-10 col-xs-9 java-tab">
                        <!-- flight section -->
                        <div class="java-tab-content active">
                            <center>
                              <h1 class="glyphicon glyphicon-plane" style="font-size:14em;color:#55518a"></h1>
                              <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                              <h3 style="margin-top: 0;color:#55518a">Flight Reservation</h3>
                            </center>
                        </div>
                        <!-- train section -->
                        <div class="java-tab-content">
                            <center>
                              <h1 class="glyphicon glyphicon-road" style="font-size:12em;color:#55518a"></h1>
                              <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                              <h3 style="margin-top: 0;color:#55518a">Train Reservation</h3>
                            </center>
                        </div>

                        <!-- hotel search -->
                        <div class="java-tab-content">
                            <center>
                              <h1 class="glyphicon glyphicon-home" style="font-size:12em;color:#55518a"></h1>
                              <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                              <h3 style="margin-top: 0;color:#55518a">Hotel Directory</h3>
                            </center>
                        </div>
                        <div class="java-tab-content">
                            <center>
                              <h1 class="glyphicon glyphicon-cutlery" style="font-size:12em;color:#55518a"></h1>
                              <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                              <h3 style="margin-top: 0;color:#55518a">Restaurant Diirectory</h3>
                            </center>
                        </div>
                        <div class="java-tab-content">
                            <center>
                              <h1 class="glyphicon glyphicon-credit-card" style="font-size:12em;color:#55518a"></h1>
                              <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                              <h3 style="margin-top: 0;color:#55518a">Credit Card</h3>
                            </center>
                        </div>
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
