<?php 
include_once __DIR__.'../includes/Helper.php';
//dd(url('/'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login V11</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/css/util.css">
	<link rel="stylesheet" type="text/css" href="<?php echo url('/assets/') ?>/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-l-50 p-r-50 p-t-40 p-b-20">
				<form class="login100-form validate-form" method="post" action="<?php echo url('/submit.php') ?>" >
					<input type="hidden" name="action" value="signup">
					<span class="login100-form-title p-b-25">
						Sign Up
					</span>
					<?php include_once __DIR__.'/notification.php' ?>
					<div class="wrap-input100 validate-input m-b-16" data-validate="Name is required">
						<input class="input100" type="text" name="name" required placeholder="Name">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-user"></span>
						</span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="email" required name="email" placeholder="Email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-envelope"></span>
						</span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16" data-validate = "UserName is required">
						<input class="input100" type="text" required name="username" placeholder="Username">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-envelope"></span>
						</span>
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
						<input class="input100" type="password"  name="password" minlength="6" required placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-lock"></span>
						</span>
                    </div>
                    
                    <div class="container-login100-form-btn p-t-25">
                        <button class="login100-form-btn">
                            Register Now
                        </button>
                    </div>
                    <div class="text-center w-full p-t-20">
						<span class="txt1">
							already a member?
						</span>

						<a class="txt1 bo1 hov1" href="<?php echo url('/login.php') ?>">
							Log in now							
						</a>
					</div>
					
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="<?php echo url('/assets/'); ?>/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo url('/assets/'); ?>/vendor/bootstrap/js/popper.js"></script>
	<script src="<?php echo url('/assets/'); ?>/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo url('/assets/'); ?>/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo url('/assets/'); ?>/js/main.js"></script>

</body>
</html>