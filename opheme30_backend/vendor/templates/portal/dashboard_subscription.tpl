<div id="subscription-info" class="col-md-6 col-sm-6">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<h3>Your subscription is {if is_numeric(strpos($Data.company.id, 'opheme')) && $smarty.const.__oBILLING_ENABLED__ eq true}<a href="https://billing.opheme.com/">{$Data.user.allowance.name}</a>{else}<span class="blue">{$Data.user.allowance.name}</span>{/if}</h3>
			</div>
		</div>
		<div class="panel-body">
			{if isset($Data.user.allowance.accountTimeLimit)}
			<div class="row">
				<div class="col-md-12">
					Account Time Limit: <strong>{$Data.user.allowance.accountTimeLimit}</strong>
					{if isset($Data.user.allowance.accountTimeLeftSeconds)}
						(Left: <strong>{secs_to_h($Data.user.allowance.accountTimeLeftSeconds)}</strong>)
					{/if}
				</div>
			</div>
			{/if}
			<div class="row">
				{if isset($Data.company.modules.discover)}
					{if isset($Data.user.allowance.discoversLimit)}
						<div class="counter-container {if isset($Data.company.modules.campaign)}col-md-6 col-xs-6{else}col-md-12 col-xs-12{/if}">
							<h1>{$Data.user.allowance.discoversLeft} / {$Data.user.allowance.discoversLimit}</h1>
							<h4>Available Discovers</h4>
						</div>
					{/if}
				{/if}
				{if isset($Data.company.modules.campaign)}
					{if isset($Data.user.allowance.campaignsLimit)}
						<div class="counter-container {if isset($Data.company.modules.discover)}col-md-6 col-xs-6{else}col-md-12 col-xs-12{/if}">
							<h1>{$Data.user.allowance.campaignsLeft} / {$Data.user.allowance.campaignsLimit}</h1>
							<h4>Available Campaigns</h4>
						</div>
					{/if}
				{/if}
			</div>
			<div class="row">
				<div class="col-md-12">
					{if isset($Data.user.allowance.jobTimeLimit)}
						Time Limit Per Task : <strong>{$Data.user.allowance.jobTimeLimit}</strong>
						<br>
					{/if}
					{if isset($Data.user.allowance.jobMessageLimit)}
						Message Limit Per Task: <strong>{$Data.user.allowance.jobMessageLimit}</strong>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>