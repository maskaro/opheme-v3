<p style="color: #606060;font-family: Helvetica;font-size: 15px;line-height: 150%;text-align: left;">
	Thank you for signing up! We're absolutely sure you'll enjoy using {$Data.company.brand}.<br>
	&nbsp;
</p>
<p style="color: #606060;font-family: Helvetica;font-size: 15px;line-height: 150%;text-align: left;">
	Please access the following link (or copy it in your browser if for whatever reason you cannot click it) to use your new account:
	<br>
	<br>
	<a href="{$Data.email.loginUrl}" target="_blank">{$Data.email.loginUrl}</a>
	<br>
	<br>
	Username: {$Data.email.email}
	<br>
	Password: <strong>{if isset($Data.email.regToken)}{$Data.email.regToken}{else}(use the one You supplied us on the Registration page){/if}</strong>
	<br>
	<br>
	<br>
	Thanks
	<br>
	<br>
	The {$Data.company.brand} Team
</p>