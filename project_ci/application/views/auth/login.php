<?php //$this->load->view('frontend/header'); ?>

	
<?php //$this->load->view('frontend/footer'); ?>
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url();?>assets/apple-icon2.png">
<link rel="icon" type="image/png" href="<?php echo base_url();?>assets/img/favicon2.png">
<link href="<?php echo base_url();?>assets/login/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="<?php echo base_url();?>assets/login/css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
<script src="<?php echo base_url();?>assets/login/js/bootstrap.min.js"></script>
<link href="<?php echo base_url();?>assets/login/css/customcss.css" rel="stylesheet">
<script src="<?php echo base_url();?>assets/login/js/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!DOCTYPE html>
<html>
    
<head>
	<title>SIGOTIK BBM</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>

<body>
	<div class="container h-100">
		<div class="d-flex justify-content-center h-100">
			<div class="user_card">
				<div class="d-flex justify-content-center">
					<div class="brand_logo_container">
						<img src="<?php echo base_url();?>assets/img/kkp3.png" class="brand_logo" alt="Logo">
					</div>
				</div>
				<div class="d-flex justify-content-center form_container">
					<form method="POST" action="<?php echo $login_url;?>">
					<?php if($error != ""): ?>
					<div>
						<div class="alert alert-danger alert-dismissible" role="alert" style="text-align: center;">
							<strong><?php echo $error;?></strong>
						</div>
					</div>
					<?php endif; ?>
						<div class="input-group mb-3">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fas fa-user"></i></span>
							</div>
							<input type="text" name="username" id="username" class="form-control input_user" placeholder="USERNAME...">
						</div>
						<div class="input-group mb-2">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
							</div>
							<input type="password" name="password" id="password" class="form-control input_pass" value="" placeholder="PASSWORD...">
						</div>
						<div class="d-flex justify-content-center mt-3 login_container">
							<input type="submit" id="login" class="btn login_btn" value="Log-In">
					   </div>
					</form>
				</div>
		
				<div class="mt-4">
					<div class="d-flex justify-content-center links">
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
