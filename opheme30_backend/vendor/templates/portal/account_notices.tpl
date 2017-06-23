{if 
	!isset($smaOk) || 
	$Data.user.account.allSet eq false || 
	isset($trialEnded) || isset($noDiscs) || isset($noCamps)
}
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div id="message-container">
					{if !isset($smaOk)}
						<div class="notouch alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							Before {$Data.company.brand} can send out or retrieve Tweets on your behalf we need your permission. {*<a href="/account">*}Authorise{*</a>*} if you want to use {$Data.company.brand}. We promise never to update your timeline or post Tweets on your behalf without permission.
						</div>
					{/if}
					{if $Data.user.account.allSet eq false}
						<div class="notouch alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							You must update all {*<a href="/account">*}Account{*</a>*} details before using {$Data.company.brand}.
						</div>
					{/if}
					{if isset($trialEnded)}
						<div class="notouch alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							Your Account Subscription has expired! Please consider {if $Data.company.id eq 'opheme' && $smarty.const.__oBILLING_ENABLED__ eq true}<a href="https://billing.opheme.com/">upgrading your subscription</a>{else}upgrading your subscription{/if}.
						</div>
					{/if}
					{if isset($noDiscs) && $Data.moduleName eq 'discover' || isset($noCamps) && $Data.moduleName eq 'campaign'}
						<div class="notouch alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							You have run out of
							{if isset($noDiscs) && $Data.moduleName eq 'discover'}
								Discovers
							{elseif isset($noCamps) && $Data.moduleName eq 'campaign'}
								Campaigns
							{/if}
							allowance! Please consider {if $Data.company.id eq 'opheme' && $smarty.const.__oBILLING_ENABLED__ eq true}<a href="https://billing.opheme.com/">upgrading your subscription</a>{else}upgrading your subscription{/if}. Alternatively, you could remove an older one to make room for your next!
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
{/if}