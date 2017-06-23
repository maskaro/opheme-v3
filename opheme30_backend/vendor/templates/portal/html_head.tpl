{if $Data.loggedIn eq true}
	{foreach $Data.user.authorisation as $authModule}
		{if count($Data.user.authorisation[$authModule@key]) gt 0}
			{assign "smaOk" true scope="global"}
			{break}
		{/if}
	{/foreach}
	{if $Data.moduleName eq "discover" || $Data.moduleName eq "campaign"}
		{if isset($Data.user.allowance.discoversLeft) && $Data.user.allowance.discoversLeft lt 1}{assign "noDiscs" true scope="global"}{/if}
		{if isset($Data.user.allowance.campaignsLeft) && $Data.user.allowance.campaignsLeft lt 1}{assign "noCamps" true scope="global"}{/if}
	{/if}
	{if (isset($Data.user.allowance.accountTimeLeftSeconds) && $Data.user.allowance.accountTimeLeftSeconds eq 0)}{assign "trialEnded" true scope="global"}{/if}
{/if}
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{$Data.company.brand} | {$Data.title}</title>
		<link href="data:image/x-icon;base64,{$Data.logos.favicon}" rel="icon" type="image/x-icon">
		<!-- CSS -->
		{foreach $Data.cssFiles as $file}
			<link href="{$file}" rel="stylesheet" media="screen">
		{/foreach}
		{foreach $Data.cssExtra as $code}
			{if strlen($code) > 0}
				<style rel="stylesheet" media="screen">{$code}</style>
			{/if}
		{/foreach}
		{if strlen({$Data.cssCompanyChanges}) > 0}
			<style rel="stylesheet" media="screen">{$Data.cssCompanyChanges}</style>
		{/if}
		{if isset($Data.cssModuleFile)}
			<link href="{$Data.cssModuleFile}" rel="stylesheet" media="screen">
		{/if}
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script>
			moduleName = "{$Data.title}";
			moduleId = "{$Data.moduleName}";
			brand = "{$Data.company.brand}";
			{if isset($smaOk) && $Data.user.account.allSet eq true}
				accountOK = true;
				_oUserId = "{$Data.user.account.id}";
				localTimeOffSet = (new Date()).getTimezoneOffset() * 60;
				lastActionTime = {$smarty.session.user.previous_interaction_check};// + localTimeOffSet;
			{else}
				accountOK = false;
			{/if}
		</script>
		{foreach $Data.jsFiles as $file}
			<script src="{$file}"></script>
		{/foreach}
		{foreach $Data.jsFilesExtraTop as $file}
			<script src="{$file}"></script>
		{/foreach}
		{if isset($smaOk) && $Data.user.account.allSet eq true && $Data.moduleName neq "interaction"}
			<script>
				$(function() {
					getInteractionUpdateCount();
					setInterval(function() { getInteractionUpdateCount(); }, 30000);
				});
			</script>
		{/if}
		<script>
			{literal}(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-38710014-2', 'opheme.com');ga('send', 'pageview');{/literal}
		</script>
	</head>
	<body>