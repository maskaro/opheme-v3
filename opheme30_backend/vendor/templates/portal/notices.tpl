<div class="container">
	{if ($NoticeType eq "critical" || $Data.moduleName eq "login") && isset($Data.logos.large)}
		<div class="row">
			<div class="row">
				<div class="col-md-12">
					<img id="large-logo" class="img-responsive" src="data:image/png;base64,{$Data.logos.large}" alt="Logo">		
				</div>
			</div>
		</div>
	{/if}
	<div class="row msgs-container">
		<div class="col-md-12">
			<div id="message-container"{if $NoticeType eq "critical" && isset($Data.logos.large)} class="error-fatal"{/if}>
				{foreach $Notices as $Type => $Messages}
					{if is_array($Messages) && count($Messages)}
						<div class="yestouch alert alert-{if $Type eq "critical"}danger{else}{$Type}{/if}">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							{foreach $Messages as $Module => $Message}
								{foreach $Message as $Msg}
									<span style="display: block; margin-bottom: 10px">{$Msg}</span>
								{/foreach}
							{/foreach}
						</div>
					{/if}
				{/foreach}
			</div>
		</div>
	</div>
</div>