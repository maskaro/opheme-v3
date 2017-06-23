<div id="account-container" class="container">
	<div class="row">
		<div id="account-information" class="col-sm-6 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="panel-title">
						<h3>Account Information</h3>
					</div>
				</div>
				<form id="account" action="/account/save-details" method="post">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="first-name">First Name</label>
											<input type="text" class="form-control" required="required" name="first-name" placeholder="first name" value="{$Data.user.account.firstname}">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="last-name">Last Name</label>
											<input type="text" class="form-control" required="required" name="last-name" placeholder="last name" value="{$Data.user.account.lastname}">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="email">Email Address</label>
											<span>{$Data.user.account.email}</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="phone">Phone Number</label>
											<input type="text" class="form-control" required="required" name="phone" placeholder="phone number" value="{$Data.user.account.phone}">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="businessName">Business Name</label>
											<input type="business-type" class="form-control" required="required" name="business-type" placeholder="business name" value="{$Data.user.account.business_type}">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="business-www">Business Web Site</label>
											<input type="url" class="form-control" required="required" name="business-www" placeholder="http://www.yourbusiness.co.uk" value="{$Data.user.account.business_www}">
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label for="home-location">Home Location (City, County, Country)</label>
											<input class="form-control" type="text" required="required" name="home-location" placeholder="home location" value="{$Data.user.account.home_location}" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<button id="passwordToggle" type="button" class="btn btn-info" data-toggle="collapse" data-target="#password-container">Change Password</button>
									</div>
								</div>
								<br />
								<div id="password-container" class="collapse">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="old-password">Current Password</label>
												<input type="password" class="form-control" name="old-password" value="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="new-password">New Password</label>
												<input type="password" class="form-control" name="new-password" id="new-password" value="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="confirm-password">Confirm New Password</label>
												<input class="form-control" type="password" name="confirm-password" value="">
											</div>
										</div>
										<div class="col-md-12">
											<div class="alert alert-info">
												<b>Warning!</b><br />
												If you change your password, you will be IMMEDIATELY logged out! You will then need to login using your new password.
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="row">
							<div class="col-md-12">
								<input type="submit" class="btn btn-block btn-success" value="Save Changes" >
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		{include "authorisation.tpl"}
	</div>
</div>
