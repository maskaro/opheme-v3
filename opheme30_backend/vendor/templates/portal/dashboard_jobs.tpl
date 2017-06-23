<div id="current-jobs" class="col-md-6 col-sm-6">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<h3>You currently have running:</h3>
			</div>
		</div>
		<div class="panel-body">
			<div class="row">
				{if isset($Data.company.modules.discover)}
					<a href="/discover">
						<div id="discover-counter" class="counter-container {if isset($Data.company.modules.campaign)}col-md-6 col-xs-6{else}col-md-12 col-xs-12{/if}">
							<h1>{$Data.user.jobs.discoverCount}</h1>
							<h4>Discovers</h4>
						</div>
					</a>
				{/if}
				{if isset($Data.company.modules.campaign)}
					<a href="/campaign">
						<div id="campaign-counter" class="counter-container {if isset($Data.company.modules.discover)}col-md-6 col-xs-6{else}col-md-12 col-xs-12{/if}">
							<h1>{$Data.user.jobs.campaignCount}</h1>
							<h4>Campaigns</h4>
						</div>
					</a>
				{/if}
			</div>
			{*if isset($smaOk) && $Data.user.account.allSet eq true*}
			<br>
			<div class="row">
				<div class="counter-container col-md-12">
					<form id="account" action="/account/save-emailNotification" method="post" class="form-inline">
						<div class="form-group">
							<label for="email-frequency">Send me related Updates once every</label>
							<div class="btn-group" data-toggle="buttons">
								{foreach $Data.moduleData.emailFrequencyArray as $key => $value}
									<label class="btn btn-default{if $Data.user.account.email_notification_frequency eq $key} active{/if}">
										<input type="radio" name="email-frequency" value="{$key}"{if $Data.user.account.email_notification_frequency eq $key} checked="checked"{/if}>
										&nbsp; {$value}
									</label>
								{/foreach}
							</div>
							{*<select name="email-frequency" class="form-control">
								{foreach $Data.moduleData.emailFrequencyArray as $value}
									<option value="{$value}"{if $Data.user.account.email_notification_frequency eq $value} selected{/if}>{$value}</option>
								{/foreach}
							</select>*}
						</div>
						<br><br>
						<button type="submit" class="btn btn-success">Save Preference</button>
					</form>
				</div>
			</div>
			{*/if*}
		</div>
	</div>
</div>