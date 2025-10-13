<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>SIGOTIK BBM</title>
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url();?>assets/apple-icon2.png">
		<link rel="icon" type="image/png" href="<?php echo base_url();?>assets/img/kkp3.png">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <?php $this->load->view('backend/styles.php'); ?>
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="<?php base_url();?>" style="padding:5px;width: 90%;">
                        <img src="<?php echo base_url(); ?>assets/img/kkp3.png" width="23%" alt="logo" class="logo-default"/>
					</a>
                    <div class="menu-toggler sidebar-toggler">
                        <span></span>
                    </div>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                    <span></span>
                </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN TOP NAVIGATION MENU -->
				
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
						<li class="dropdown dropdown-notification" id="notifpesan">
							<a href="javascript:;" class="dropdown-toggle">
                                <i class="fa fa-envelope" title="Notifikasi"></i>
								<span id="notif"><div id="vali"></div></span>
                            </a>
							<ul class="dropdown-menu dropdown-extended">
								<?php
									// $ci = & get_instance();
									// $userid = $this->session->userdata('userid');
									// $sql = $ci->db->query("SELECT * FROM dat_notif WHERE conf_user_id = '".$userid."' AND `status` = '0'");
									// foreach($sql->result() AS $list){
								?>
										<!--<li style="height: 100px;overflow-y: auto;padding: 10px;">
											<a href="javascript:;" >
												<div id="notifpesan"><font style="color: #c6cac6;;">- Tidak ada pesan masuk -</font></div>
											</a>
										</li>-->
								<?php
									// }
								?>
                            </ul>
						</li>
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <span class="username username-hide-on-mobile">Hai, <?php echo ucwords($this->session->userdata('user_realname')); ?> </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li id="my-profile">
                                    <a href="javascript:;">
                                        <i class="fa fa-user-secret"></i>
										Profil
									</a>
                                </li>
								<li class="divider"> </li>
                                <li>
                                    <a href="<?php echo base_url().'auth/logout'; ?>">
                                        <i class="fa fa-power-off"></i> Keluar </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <div class="page-sidebar-wrapper">
                <!-- BEGIN SIDEBAR -->
                <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                <div id="menusidebar" class="page-sidebar navbar-collapse collapse">
                <?php $this->load->view('backend/sidebar.php'); ?>
                </div>
                <!-- END SIDEBAR -->
            </div>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <!-- START PAGE CONTENT -->
                <div class="page-content" id="main-content">