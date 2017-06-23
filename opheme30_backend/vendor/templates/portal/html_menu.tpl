<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<!-- Be sure to leave the brand out there if you want it shown -->
			{if isset($Data.logos.small)}
				<a class="navbar-brand" href="/{$smarty.const.__oMOD_DEFAULT__}">
					<img class="img-responsive" src="data:image/png;base64,{$Data.logos.small}" />
				</a>
			{/if}
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<!-- use class="active" on li for current website, exception is dashboard as you click on opheme's logo to get there -->
				{if isset($smaOk) && $Data.user.account.allSet eq true}
					{if isset($Data.company.modules.discover)}
						<li {if $Data.moduleName eq "discover"}class="active"{/if}>
							<a id="wizard-menu-discovers" href="/discover"><i class="fa fa-search fa-lg"></i> Discovers</a>
						</li>
					{/if}
					{if isset($Data.company.modules.campaign)}
						<li {if $Data.moduleName eq "campaign"}class="active"{/if}>
							<a id="wizard-menu-campaigns" href="/campaign"><i class="fa fa-bar-chart-o fa-lg"></i> Campaigns</a>
						</li>
					{/if}
					{if isset($Data.company.modules.interaction) && isset($smaOk) && $Data.user.account.allSet eq true}
						<li {if $Data.moduleName eq "interaction"}class="active"{/if}>
							<a id="wizard-menu-smInteraction" href="/interaction"><i class="fa fa-users fa-lg"></i> Interactions <span class="badge">0</span></a>
						</li>
					{/if}
				{/if}
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li id="wizard-menu-settings" class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="greeting">{if strlen($Data.user.account.firstname)}Hi, {$Data.user.account.firstname}{else}Welcome{/if}!</span> <i class="fa fa-cog fa-lg"></i><b class="caret"></b></a>
					<ul class="dropdown-menu">
						{if isset($smaOk) && $Data.user.account.allSet eq true}
							<li{if $Data.moduleName eq "dashboard"} class="active"{/if}>
								<a href="/dashboard"><i class="fa fa-dashboard fa-lg"></i> Dashboard</a>
							</li>
						{/if}
						<li{if $Data.moduleName eq "account"} class="active"{/if}>
							<a href="/account"><i class="fa fa-user fa-lg"></i> Account</a>
						</li>
						{if $Data.user.representative eq true && $Data.company.modules.reseller eq true}
							<li{if $Data.moduleName eq "reseller"} class="active"{/if}>
								<a href="/reseller"><i class="fa fa-users fa-lg"></i> Users</a>
							</li>
						{/if}
						{if $Data.user.representative eq true && $Data.company.modules.admin eq true}
							<li{if $Data.moduleName eq "admin"} class="active"{/if}>
								<a href="/admin"><i class="fa fa-cogs fa-lg"></i> Admin</a>
							</li>
						{/if}
						{if in_array($Data.moduleName, array("dashboard", "account"))}
							<li class="divider"></li>
							<li>
								<a href="#" onclick="window['tour'].restart();window['tour'].start(true);"><i class="fa fa-road fa-lg"></i> Tour Guide</a>
							</li>
						{/if}
						<li class="divider"></li>
						{if !empty($Data.company.support)}
							<li>
								{*if is_string(filter_var($Data.company.support, $smarty.const.FILTER_VALIDATE_EMAIL))}
								<a href="mailto:{$Data.company.support}?subject=I%20need%20help%20with%20{$Data.company.brand}">
								{else*}
								<a href="{$Data.company.support}" target="_blank">
								{*/if*}
									<i class="fa fa-question-circle fa-lg"></i> Support
								</a>
							</li>
						{/if}
						<li>
							<a href="/logout"><i class="fa fa-sign-out fa-lg"></i> Log out</a>
						</li>
					</ul>
				</li>
			</ul>
		</div><!--/.nav-collapse -->
	</div>
</div>
{include file="account_notices.tpl"}