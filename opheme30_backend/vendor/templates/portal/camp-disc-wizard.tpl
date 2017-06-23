<div id="editor-container">
	{if $Data.moduleName eq "campaign"}
		<script>window["companyName"] = "{$Data.user.account.business_type}"; window["companyUrl"] = "{$Data.company.domain}"; window["homeLocation"] = "{$Data.user.account.home_location}";</script>
	{else}
		<script>window["homeLocation"] = "{$Data.user.account.home_location}";</script>
	{/if}
	<script>
		window["authKeys"] = [];
		{foreach $Data.user.authorisation as $authModule => $authKeys}
			{if count($authKeys) gt 0}
				window["authKeys"]["{$authModule}"] = [];
				{foreach $authKeys as $auth}
					window["authKeys"]["{$authModule}"]["{$auth.id}"] = "{$auth.average_message_time_of_followers}";
				{/foreach}
			{/if}
		{/foreach}
	</script>
	<form id="editor" action="/{$Data.moduleName}/process" method="post" enctype="multipart/form-data">
		<div id="wizard_info">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label">Which Social Media Accounts would you like to use with this {$Data.title}?</label>
						<div class="checkbox-wrapper">
							{foreach $Data.user.authorisation as $authModule => $authKeys}
								{if count($authKeys) gt 0}
									<label for="authKeyUse_{$authModule}" class="checkbox">
										<input type="checkbox" id="authKeyUse_{$authModule}" name="authKeyUse_{$authModule}" class="sm-require-one"> {ucfirst($authModule)}
									</label>
								{/if}
							{/foreach}
						</div>
						{foreach $Data.user.authorisation as $authModule => $authKeys}
							{if count($authKeys) gt 0}
								<select style="display: none" multiple name="authKey_{$authModule}[]" id="authKeys_{$authModule}" class="form-control"> {*onchange="this.form.authKeyType.value=($('#editor [name=authKeyId]')[0].options[$('#editor [name=authKeyId]')[0].selectedIndex].text.split('-')[0].toLowerCase().trim());$('#optimal-time').html(window['authKeys'][this.form.authKeyType.value][$('#editor [name=authKeyId]')[0].options[$('#editor [name=authKeyId]')[0].selectedIndex].value]);"*}>
									{*<option value="----------">----------</option>*}
									{foreach $authKeys as $auth}
										<option value="{$auth.id},{$authModule}">{ucfirst($authModule)} - @{$auth.screen_name}</option>
									{/foreach}
								</select>
							{/if}
						{/foreach}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label more-info-tooltip" for="name" data-toggle="tooltip" data-placement="right" title="Give your {$Data.title} a cool name">{$Data.title} Name</label>
						<input type="text" class="form-control" required="required" name="name" value="" placeholder="{$Data.moduleName} name">
					</div>
				</div>
			</div>
			{if $Data.moduleName eq "campaign"}
				{*<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">Assign your {$Data.title} a Category</label>
							<select name="category" class="form-control">
								{foreach $Data.moduleData.cats as $cat}
									<option value="{$cat}">{$cat}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>*}
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">Choose the hourly response limit for this {$Data.title}</label>
							<input type="text" class="form-control" required="required" name="hourly_limit" value="1" {* onchange="$('#slider-range-max').slider('option', 'value', this.value)"*} readonly style="text-align: center">
							<div id="slider-range-max"></div>
						</div>
					</div>
				</div>
			{else}{* discover *}
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">What type of messages would you like this {$Data.title} to retrieve?</label>
							<select name="messageLifeSpanLimit" class="form-control">
								{foreach $Data.moduleData.lifeSpans as $cat}
									<option value="{$cat@key}">{$cat}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
			{/if}
		</div>
		<div id="wizard_filters">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="more-info-tooltip control-label" for="filter" data-toggle="tooltip" data-placement="right" title="Give your {$Data.title} one word or a phrase to look for. Space separated, wrapping multi-word terms in quotes.">Enter some Search Terms (e.g. with 4 terms: "car hire" discount "last day" London) - Optional</label>
						<input type="text" class="form-control" id="filter" name="filter" value="" placeholder="{$Data.moduleName} search term">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="more-info-tooltip control-label" for="filter-ex" data-toggle="tooltip" data-placement="right" title="Give your {$Data.title} keywords to base message exclusion on. Space separated, wrapping multi-word terms in quotes.">Enter some Exclusion Terms (e.g. with 4 terms: coffee "big discount" "this Sunday" London) - Optional</label>
						<input type="text" class="form-control" name="filter_ex" value="" placeholder="{$Data.moduleName} exclusion terms, space separated">
					</div>
				</div>
			</div>
		</div>
		{if $Data.moduleName eq "campaign"}
			<div id="wizard_user">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="more-info-tooltip control-label" for="response-text" data-toggle="tooltip" data-placement="right" title="This is the Tweet your recipients will receive">{$Data.title} Tweet - Characters left: <span id="response_text_count">110</span></label>
							<input type="text" class="form-control" required="required" id="response_text" name="response_text" value="" placeholder="{$Data.moduleName} response (10-110 characters)">
							<p class="help-block">
								Your Tweet should include the recipient name, your organisation name and the recipient name.
								Use <span id="response_text_percent_r">%r to indicate where the recipient name will appear</span> and
								<span id="response_text_percent_c">%c to indicate where the organisation name will appear</span>.
								For example: <strong>Hi %r, here at %c we've got some special deals tonight!</strong>
							</p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="more-info-tooltip control-label" for="response-preview" data-toggle="tooltip" data-placement="right" title="{$Data.title} response message preview">{$Data.title} Response Preview</label>
							<div class="row" style="border-style: solid; border-width: 1px;">
								<div class="col-md-12">
									<div id="container">
										<h4 id="response_preview"><span id="response_preview_text">(no response message entered yet)</span> <a>{$Data.company.brand_url}/url/xf1d4</a></h4>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="wizard_campaign">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="more-info-tooltip control-label" for="banner" data-toggle="tooltip" data-placement="right" title="Give your {$Data.title} a meaningful banner">{$Data.title} Banner (Allowed image types: PNG, JPG/JPEG)</label>
							<input type="file" accept="image/*" class="form-control" id="banner" name="banner">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="more-info-tooltip control-label" for="text" data-toggle="tooltip" data-placement="right" title="Give your {$Data.title} a meaningful text">{$Data.title} Message (min. 5 characters, can use HTML tags)</label>
							<textarea rows="4" class="form-control" required="required" id="text" name="text" value="" placeholder="{$Data.moduleName} text (min 5 characters)"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="more-info-tooltip control-label" for="preview" data-toggle="tooltip" data-placement="right" title="{$Data.title} message overall preview">{$Data.title} Preview</label>
							<div class="row" style="border-style: solid; border-width: 1px; font-size: 75%; margin-left: auto; margin-right: auto; margin-bottom: 20px;">
								<div class="col-md-12">
									<div id="container">
										<h4 style="font-size: inherit">Hey there, <strong id="preview_username">@Username</strong>!<br>You have received a message from <strong><a>{$Data.user.account.business_type}</a></strong>.</h4>
										<div class="row">
											<div class="col-md-12" style="padding-top: 5px; padding-bottom: 0px;">
												<label class="control-label" for="text" style="font-size: inherit">Message from <strong><a>{$Data.user.account.business_type}</a></strong></label>
												<span style="display: block"><span id="preview_text">(no message set yet)</span></span>
											</div>
										</div>
										<div class="row" id="preview_banner_container" style="display: none">
											<div class="col-md-12" style="padding-top: 5px; padding-bottom: 0px;">
												<label class="control-label" for="banner" style="font-size: inherit">{$Data.title} Banner</label>
												<span style="display: block"><img src="/images/banner_placeholder.png" title="Banner" id="preview_banner" style="max-width: 300px"></span>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12" style="padding-top: 5px; padding-bottom: 0px;">
												<label class="control-label" for="tweet-created-at" style="font-size: inherit">Date of tweet</label>
												<span>{$smarty.now|date_format:"%T %D"}</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{/if}
		<div id="wizard_running">
			<div class="row">
				<div class="col-md-12">
					<div class="form-inline">
						<div class="form-group">
							<div class="checkbox-container">
								<label>Which days of the week would you like the {$Data.title} to be active on?</label>
								<div class="checkbox-wrapper everyday">
									<input type="checkbox" id="day-all" value="All">
									<label class="checkbox" id="tickAllDays">Every Day</label>
								</div>
								{foreach from=$Data.moduleData.days item=day}
									<div class="checkbox-wrapper">
										<input type="checkbox" id="day-{strtolower($day)}" name="days[]" value="{$day}">
										<label for="day-{strtolower($day)}" class="checkbox">{substr($day, 0, 3)}</label>
									</div>
								{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label>What times would you like the {$Data.title} to run between?</label>
						<label class="alert alert-info" style="text-align: center">Optimal Time of Day has been calculated as being: <strong><span id="optimal-time">None Yet</span></strong></label>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="startTimeField">Start {$Data.title} Time:</label>
									<input class="form-control" type="text" value="" id="startTimeField" name="time_start" placeholder="09:00">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="endTimeField">End {$Data.title} Time:</label>
									<input class="form-control" type="text" value="" id="endTimeField" name="time_end" placeholder="17:00">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label">When would you like the {$Data.title} to start and end?</label>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="startDateField">Start {$Data.title} Date:</label>
									<input class="form-control" type="text" value="" id="startDateField" name="date_start" placeholder="click to set" data-date-format="dd-mm-yyyy">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="endDateField">End {$Data.title} Date:</label>
									<input class="form-control" type="text" value="" id="endDateField" name="date_end" placeholder="click to set" data-date-format="dd-mm-yyyy">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="wizard_location">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label">Where would you like the {$Data.title} to originate?</label>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<p class="help-block" for="centre_lat">Enter location below, click on the map or click on <i class="fa fa-lg fa-map-marker"></i>  button to get your current location</p>
									<div class="row">
										<div class="col-md-12">
											<div class="input-group">
												<input class="form-control" style="text-align: center" id="googleLocationSearch" value="" type="text" placeholder="Enter a postcode or location" onkeyup="if (this.value.length > 2) { return getLocationGoogle(this.value, event); }" >
												<div class="input-group-btn">
													<a id="click_get_client_coords" class="btn btn-primary"><i class="fa fa-lg fa-map-marker"></i></a>
													<a onclick="if ($('#googleLocationSearch').val().length > 2) { return getLocationGoogle($('#googleLocationSearch').val(), 'fromButton') }" class="btn btn-primary"> Find</a>
												</div>
											</div>
											<input type="hidden" name="centre_lat" id="centre_lat" {*placeholder="latitude - click map to set or type your own"*} oninput="$('form#editor').valid();">
											<input type="hidden" name="centre_lng" id="centre_lng" {*placeholder="longitude - click map to set or type your own"*} oninput="$('form#editor').valid();">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label" for="radius">Radius 0.1 - {if $Data.moduleName eq "campaign"}1{else}10{/if} (miles)</label>
									<input class="form-control" type="text" name="radius" required="required" value="" readonly style="text-align: center">
									<div id="slider-range-radius"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			{if $Data.moduleName eq "discover"}
				<div class="jobWizardInfo">
					<div class="alert alert-info">All following {$Data.title} Wizard sections are entirely optional.</div>
				</div>
			{/if}
			<div class="jobWizardInfo">
				<div class="alert alert-info">Click <strong>Save Changes</strong> below to finish.</div>
			</div>
		</div>
		<div class="wizard-footer" style="display: none">
			<div class="row">
				<input type="hidden" name="id" value="-1">
				{*<input type="hidden" name="authKeyType" value="">*}
				<div class="col-md-6">
					<button type="button" class="btn btn-block btn-danger" onclick="$('a[href=#newJobContainer]').click();$('a[href=#currentList]').click();">Cancel</button>
				</div>
				<div class="col-md-6">
					<input type="submit" class="btn btn-block btn-success" value="Save changes">
				</div>
			</div>
		</div>
	</form>
</div>