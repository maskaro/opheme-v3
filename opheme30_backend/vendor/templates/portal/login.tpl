{if !isset($NoticeType) && isset($Data.logos.large)}
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<img id="large-logo" class="img-responsive" src="data:image/png;base64,{$Data.logos.large}" alt="Logo">		
			</div>
		</div>
	</div>
{/if}
<div class="container">
	<div class="panel-group" id="login-register">
	  	<div class="panel panel-default">
	    	<div class="panel-heading">
	      		<div class="panel-title" data-toggle="collapse" data-parent="#login-register" href="#login-panel">
	        		Login
	      		</div>
	    	</div>
	    	<div id="login-panel" class="panel-collapse collapse in">
	      		{*<div class="panel-body">*}
	        		<form id="login" action="/login/check" method="post">
						<div class="panel-body">
							<div class="form-group">
								<input type="text" class="form-control" name="email" placeholder="email@example.com" required="required" value="">
							</div>
							<div class="form-group">
								<input type="password" class="form-control" name="password" placeholder="password" required="required" value="">
							</div>
							<input type="submit" class="btn btn-primary btn-block" value="Login">
						</div>
					</form>
					<form id="forgot" action="/login/reset-password" method="post">
						<div onclick="var i, el = $(this); for (i = 0; i < 3; i++) { el = el.next().slideToggle('slow'); };$('.flipper').animate({ height: '470px' }, 'fast');" class="panel-footer">
							Forgot Password
						</div>
						<div class="panel-body" style="display:none;">
							<div class="form-group">
								<p class="help-block">Enter the email address connected with your {$Data.company.brand} Account</p>
								<input type="text" class="form-control" name="email" placeholder="email" required="required" value="">
							</div>
							<input type="submit" class="btn btn-primary btn-block" value="Reset Password">
						</div>
					</form>
	      		{*</div>*}
	    	</div>
	  	</div>
	  	{if $smarty.const.__oREGISTRATION_ACTIVE__ eq true}
	  	<div class="panel panel-default">
	    	<div class="panel-heading">
	      		<div class="panel-title" data-toggle="collapse" data-parent="#login-register" href="#register-panel">
	        		Register
	      		</div>
	    	</div>
	    	
	    	<div id="register-panel" class="panel-collapse collapse">
	      		<div class="panel-body">
					{include "register.tpl"}
	      		</div>
	    	</div>
	  	</div>
	  	{/if}
	</div>
	{*<div id="form">
		<div id="flip-toggle" class="flip-container">
			<div class="flipper">
				<div class="front">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="panel-title">
								Login
							</div>
						</div>
						<form id="login" action="/login/check" method="post">
							<div class="panel-body">
								<div class="form-group">
									<input type="text" class="form-control" name="email" placeholder="email@example.com" required="required" value="">
								</div>
								<div class="form-group">
									<input type="password" class="form-control" name="password" placeholder="password" required="required" value="">
								</div>
								<input type="submit" class="btn btn-primary btn-block" value="Login">
							</div>
						</form>
						<form id="forgot" action="/login/reset-password" method="post">
							<div onclick="var i, el = $(this); for (i = 0; i < 3; i++) { el = el.next().slideToggle('slow'); };$('.flipper').animate({ height: '470px' }, 'fast');" class="panel-footer">
								Forgot Password
							</div>
							<div class="panel-body" style="display:none;">
								<div class="form-group">
									<p class="help-block">Enter the email address connected with your {$Data.company.brand} Account</p>
									<input type="text" class="form-control" name="email" placeholder="email" required="required" value="">
								</div>
								<input type="submit" class="btn btn-primary btn-block" value="Reset Password">
							</div>
						</form>
						{if $smarty.const.__oREGISTRATION_ACTIVE__ eq true}
							<div id="register-button-wrapper" class="panel-footer">
								<div id="register-button" class="text-center button-footer">
									Sign Up
								</div>
							</div>
						{/if}
					</div>
				</div>
				{if $smarty.const.__oREGISTRATION_ACTIVE__ eq true}
					<div class="back">
						{include "register.tpl"}
					</div>
				{/if}
			</div>
		</div>*}
		{*<div class="clearfix"></div>*} {* This isn't helping, container ends up with 0px height. Fixed this in login.js with a hack. *}
		{if $smarty.const.__oREGISTRATION_ACTIVE__ eq true}{include "terms.tpl"}{/if}
	{*</div>*}
</div>
{*<style>
	#update .progress { overflow: hidden; margin-top: 40px; height: 40px; margin-bottom: 20px; background-color: #F5F5F5; border-radius: 2px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.6); box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.6); }
	#update .progress-bar { letter-spacing: 1px; font-weight: 100; float: left; width: 0; height: 100%; font-size: 16px; line-height: 36px; color: #FFF; text-align: center; background-color: #5cb85c; -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15); box-shadow: inset 0px -4px 0 rgba(0, 0, 0, 0.2); -webkit-transition: width .6s ease; transition: width .6s ease; }
</style>
<div id="update" class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="progress active">
				<div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
					100% Complete
				</div>
			</div>
		</div>
	</div>
</div>*}