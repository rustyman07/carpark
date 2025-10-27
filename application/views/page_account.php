
<section  ng-controller="PageAccount" ng-init="init('<?php echo $url; ?>')" class="d-none-first  container pt-3">
	
	<div ng-show="!f.TOKEN" class="loading" style="min-height: 70vh;">
		<div><i class="fa fa-user myPulse"></i><p>Loading Your Account</p></div>
	</div>

	<div ng-hide="response=='' && responseErr==''" class="form-response">
		<div ng-show="response" ng-bind-html="response" ng-class="responseClass" class="response-success"></div>
		<div ng-show="responseErr" ng-bind-html="responseErr" ng-class="responseClass" class="response-error"></div>
	</div>

	<div ng-show="f.TOKEN" class="card card-custom mx-auto myFadeInDown" style="max-width: 800px;">
		<div class="card-body ">
			<h4 class="card-title display-6 mb-5">Your Account</h4>
			<div class="row">
				<div class="col-md-7 form-group">
					
					<fieldset class="fieldset-child">
						<legend>User Information</legend>
						<form ng-submit="submitForm()" role="form"  >
							<label class="col-form-label">Name *</label>
							<input ng-model="f.NAME" type="text" class="form-control" autofocus required>
							<label class="col-form-label">Job Title *</label>
							<input ng-model="f.JOBTITLE" type="text" class="form-control" required>
							<label class="col-form-label">Username *</label>
							<input ng-model="f.USERNAME" type="text" minlength="5" class="form-control" required>
							<button type="submit" class="btn btn-secondary btn-block mt-2"><i class="fa fa-check-circle-o fa-fw fa-lg" ></i>Save</button>
						</form>
					</fieldset>

				</div>
				<div class="col-md-5">
					<fieldset class="fieldset-child">
						<legend>Password</legend>
						<button ng-show="!f.passwordPanel" ng-click="f.passwordPanel = !f.passwordPanel" type="button" class="btn btn-primary btn-block" style="margin-top: 2.2em;">Change Password</button>
						<form ng-if="f.passwordPanel" ng-submit="submitPasswordForm()" class="myFadeInDown" role="form"  >
							<label class="col-form-label">Current Password</label>
							<input ng-model="f.currentPassword" type="password" class="form-control" autofocus required>
							<label class="col-form-label">New Password</label> 
							<input ng-model="f.newPassword" type="password" minlength="5" class="form-control" required>
							<label class="col-form-label">Re-Type Password</label>
							<input ng-model="f.retypePassword" type="password" minlength="5" class="form-control" required>
							<div class="d-flex justify-content-between mt-2">
								<button type="submit" class="btn btn-secondary btn-block mr-1"><i class="fa fa-check-circle-o fa-fw fa-lg" ></i>Save</button>
								<button ng-click="f.passwordPanel = false;" type="button" class="btn btn-secondary"><i class="fa fa-times-circle fa-fw fa-lg"></i>Cancel</button>
							</div>
						</form>
					</fieldset>
				</div>
			</div>	
			
		</div>
	</div>

</section>

<script src="<?php echo base_url('assets/js/pageAccount.js') ?>"></script>