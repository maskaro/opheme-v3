<div id="dashboard" class="container campaign">
	<div class="row">
		<div class="col-md-6 col-md-offset3">
			<div id="campaign-container">
				<br>
				<h4>Hey there, <strong>@{$Data.moduleData.msg.user.screen_name}</strong>!<br><br>You have received a message from <strong><a href="{$Data.moduleData.user.business_www}" target="_blank">{$Data.moduleData.user.business_type}</a></strong>.</h4>
				<br>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="tweet-text">Your tweet that triggered this message</label>
							<span class="form-control">{$Data.moduleData.msg.text}</span>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="campaign-text">Message from <strong><a href="{$Data.moduleData.user.business_www}" target="_blank">{$Data.moduleData.user.business_type}</a></strong></label>
							<span class="form-control">{$Data.moduleData.job.text}</span>
						</div>
					</div>
				</div>
				{if strlen($Data.moduleData.job.banner) > 0}
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="campaign-banner">Campaign Banner</label>
								<span class="form-control"><img src="data:{$Data.moduleData.job.banner_type};base64,{$Data.moduleData.job.banner}" title="Banner"></span>
							</div>
						</div>
					</div>
				{/if}
				<br>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="tweet-created-at">Date of tweet</label>
							<span class="form-control">{$Data.moduleData.msg.created_at}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br>
	{*<div class="row">
		<div class="col-md-6 col-md-offset3">
			<div id="blacklist-container">
				<h4>{$Data.company.brand} Marketing Preferences</h4>
				<br>
				<form id="preferences" action="/url/preferences" method="post">
					<div class="row">
						<div class="col-md-12 form-inline">
							<label>Send me offers related to</label>
							<div class="controls">
								{foreach $Data.moduleData.cats as $cat}
									<label class="control-label" class="checkbox" for="{$cat}" style="padding-top: 0px">
										<input type="checkbox" name="preferences[]" value="{$cat}"{if in_array($cat, $Data.moduleData.prefs)} checked="checked"{/if}> {$cat} 
									</label>
								{/foreach}
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<input type="password" class="form-control" id="password" name="password" placeholder="type your password to save preferences, 8 characters or more" required="required" value="">
								{if count($Data.moduleData.prefs) eq 0}
									<input type="password" class="form-control" name="confirm-password" placeholder="confirm password, same as above" required="required" value="">
								{else}
									<input type="hidden" name="prefs_exist" value="">
								{/if}
							</div>
						</div>
					</div>
					<input type="hidden" id="screen_name" name="screen_name" value="{$Data.moduleData.msg.user.screen_name}" />
					<input class="btn btn-large btn-block btn-primary" type="submit" value="Save Preferences" />
					<input type="hidden" name="url" value="{$Data.moduleData.url}">
					<input type="hidden" name="smModule" value="{$Data.moduleData.smModule}">
				</form>
			</div>
		</div>
	</div>
	<br>*}
	<div class="row">
		<div class="col-md-6 col-md-offset3">
			<div id="blacklist-container">
				<form id="blacklist" action="/url/blacklist" method="post">
					{*if count($Data.moduleData.prefs) > 0}
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<input type="password" class="form-control" name="password" placeholder="type your password to blacklist yourself, 8 characters or more" required="required" value="">
								</div>
							</div>
						</div>
						<input type="hidden" name="prefs_exist" value="">
					{/if*}
					<input type="hidden" id="screen_name" name="screen_name" value="{$Data.moduleData.msg.user.screen_name}" />
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								{if $Data.moduleData.blacklisted eq false}
									<input class="btn btn-large btn-block btn-primary" type="submit" value="I dont want to receive offers!">
									<input type="hidden" name="blacklist" value="">
								{else}
									<input class="btn btn-large btn-block btn-primary" type="submit" value="I want to receive offers again!">
								{/if}
							</div>
						</div>
					</div>
					<input type="hidden" name="url" value="{$Data.moduleData.url}">
					<input type="hidden" name="smModule" value="{$Data.moduleData.smModule}">
				</form>
			</div>
		</div>
	</div>
</div>