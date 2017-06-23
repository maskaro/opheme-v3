{if $Data.moduleName eq "login"}
	<div id="terms-content" style="display: none;">
{else}
	<div id="dashboard" class="container account"><div class="row"><div class="col-md-12">
{/if}
		{if strlen($Data.company.terms)}{$Data.company.terms}{else}{$CompanyTerms}{/if}
		<div id="back-tc" class="panel panel-default">
			<div class="panel-body">
				<div class="back-up button-footer text-center">
					<i class="fa fa-angle-up fa-2x"></i><br>
					Back to Top
				</div>
			</div>
		</div>
{if $Data.moduleName eq "login"}
	</div>
{else}
	</div></div></div>
{/if}