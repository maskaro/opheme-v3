		<footer class="footer">
			<div class="panel panel-default">
				<div class="panel-footer">
					<div class="text-center container">
						<p>Copyright {$Data.company.name} {$smarty.now|date_format:"%Y"}. All rights reserved. <a href="/terms" target="_blank">Terms of Service</a>. <a href="/cookie-policy" target="_blank">Cookie Policy</a>.</p>
						<p>Created by Crazy Polar Bears in {$ExecutionTime} seconds.</p>
						{*if isset($Data.company.modules.klout)}
							<p><a href="http://www.klout.com" target="_blank"><img src="/images/powered-by-klout.png" alt="Powered by Klout" style="height: 30px"></a></p>
						{/if*}
					</div>
				</div>
			</div>
		</footer>
		<div id="loader-system" class="free-for-all" style="display:none;"><div class="loader-content"><i class="fa fa-cog fa-spin fa-lg"></i>loading</div></div>
		<div id="loader-element" class="free-for-all" style="display:none;"><div class="loader-content"><i class="fa fa-cog fa-spin fa-lg"></i>loading</div></div>
		<!-- JS -->
		{foreach $Data.jsFilesBottom as $file}
			<script src="{$file}"></script>
		{/foreach}
		{if isset($Data.jsBodyFile)}
			<script src="{$Data.jsBodyFile}"></script>
		{/if}
		{foreach $Data.jsFilesEndExtraBottom as $file}
			<script src="{$file}"></script>
		{/foreach}
	</body>
</html>