<!DOCTYPE html>
<html lang="en" ng-app="myApplication">

<head>

  	<title>CLINIC SYSTEM</title>
	<meta name="AUTHOR" content="Dr. Carlo Antonio D. Romero">
  	<meta name="DESCRIPTION" content="Dr. Carlo Antonio D. Romero Clinic System by Cerebro Diagnostic System">
	<META NAME="KEYWORDS" CONTENT="OPHTHALMOLOGIST | EYECLINIC | CLINIC">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="expires" content="Fri, 18 Jul 2014 1:00:00 GMT" />

    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
	<link rel="icon" href="<?php echo base_url('assets/css/images/logo.png'); ?>" type="image/gif" sizes="16x16">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/bootstrap-reboot.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/bootstrap.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css'); ?>">

	<!-- Additional CSS Style  -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom-login.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/animation.css'); ?>">

 	<script src="<?php echo base_url('assets/jquery/jquery-1.7.min.js'); ?>"></script>

	<!-- Angularjs -->
    <script src="<?php echo base_url('assets/angularjs/angular.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-sanitize.min.js'); ?>"></script>

    <!-- Custom app -->
    <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>

</head>

<body>


	<section class="d-none-first container p-5" ng-controller="Login" ng-init="init('<?php echo $url; ?>')">

		<div class="card">
			<div class="card-body">
				<legend class="m-0">Clinic System</legend>{{allerror}}
				<span class="small">You are in safe hand</span>
                            
				<form name="Form" ng-submit="submitForm()" class="form">
					<div ng-if="responseErr" ng-bind-html="responseErr" class="myFadeIn pb-2"></div>
					<input ng-model="f.username" type="text"  class="form-control" placeholder="Username" autofocus required>
					<input ng-model="f.password" type="password"  class="form-control" placeholder="Password" required>
					<button ng-if="!isSubmit" type="submit" class="btn btn-primary btn-block" >Sign in</button>
					<button ng-if="isSubmit" type="button" class="btn btn-primary btn-block" disabled>Signing in <i class="fa fa-refresh fa-spin ml-1"></i></button>
				</form>

				<div class="text-center"><button ng-click="help = !help" type="button" class="btn btn-link btn-sm">Help</button></div>
				<div ng-show="help" class="alert alert-success myFadeInUp">
					<strong class="text-danger">Please Refresh the page.</strong>
					<p class="small">This problem repeats on browser that has <strong>CACHE ON</strong>. If you use chrome go to settings > Advance > Data Saver and turn it off and refresh the page.</p>
					<strong class="text-danger">Wrong Username or Password.</strong>
					<p class="small">Contact Cerebro Diagnostic System.</p>
				</div>
			</div>
			<div class="card-footer">

				<div class="social">
					<div><p class="text-muted">Follow Us</p></div>
					<a href="https://www.facebook.com/cerebrodiagnostics/" target="_blank"><i class="fa fa-facebook fa-fw"></i></a>
					<a href="https://twitter.com/cerebrocdo" target="_blank"><i class="fa fa-twitter fa-fw"></i></a>
					<a href="https://www.linkedin.com/in/cerebrodiagnostics/" target="_blank"><i class="fa fa-linkedin fa-fw"></i></a>
				</div>

				<div class="text-center small">All Right Reserved | <a href="http://cerebrodiagnostics.com" target="_blank">Cerebro Diagnostic System</a></div>
			</div>
		</div>



	</section>

	<script src="<?php echo base_url('assets/js/formLogin.js'); ?>"></script>


</body>
</html>
