{*<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			Sign Up
		</div>
	</div>*}
	<form id="register" action="/register/process" method="post">
		{*<div class="panel-body">*}
			<div class="form-group">
				<input type="email" class="form-control" name="email" placeholder="email" required="required" value="{if isset($Data.formData.email)}{$Data.formData.email}{/if}">
			</div>
			<div class="form-group">
				<input type="text" class="form-control" name="firstName" placeholder="first name (only, not last name)" required="required" value="{if isset($Data.formData.firstName)}{$Data.formData.firstName}{/if}">
			</div>
			<div class="form-group">
				<input type="password" class="form-control" id="password" name="password" placeholder="password" required="required" value="">
			</div>
			<div class="form-group">
				<input type="password" class="form-control" name="confirm-password" placeholder="confirm password" required="required" value="">
			</div>
			{*<div class="form-group">
				<input type="text" class="form-control" name="token" placeholder="secret token - ignore if unknown" value="">
			</div>*}
			<div class="form-group">
				<label class="checkbox">
					<input id="terms" name="terms" type="checkbox" required="required"{if isset($Data.formData.terms)} checked="checked"{/if}> I have read and agreed to <a id="terms-button" href="#terms-content" type="button">Terms &amp; Conditions</a>
				</label>
			</div>
			{*
			<label class="form-control">
				<img src="/php/captcha_image.php?rand=<?php echo rand(); ?>" id='captchaimg' style="margin: 0px">
			</label>
			<input type="text" class="form-control" name="captcha_code" placeholder="captcha code seen above" required="required" value="">
			<label class="form-control captcha">
				Can't read the image? click <a href='javascript:refreshCaptcha();'>here</a> to refresh.
			</label>
			*}
			<input type="submit" class="btn btn-primary btn-block" value="Sign Up">
		{*</div>*}
	</form>
	{*<div id="login-button-wrapper" class="panel-footer">
		<div id="login-button" class="text-center button-footer">
			Log In
		</div>
	</div>
</div>*}