{if $Data.moduleData.loggedIn eq false}
	<br><br>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<img id="large-logo" class="img-responsive" src="data:image/png;base64,{$Data.logos.large}" alt="Logo">		
			</div>
		</div>
	</div>
{/if}
{assign "loadTpl" $Data.moduleData.loadTpl}
{include file="url_$loadTpl.tpl"}