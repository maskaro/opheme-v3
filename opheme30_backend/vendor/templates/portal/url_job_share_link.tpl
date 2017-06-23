<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script src="/jscripts/external/other/markerclustererplus.min.js"></script>
<script src="/jscripts/external/other/autolinker.min.js"></script>
<script src="/jscripts/internal/tooltip{$Data.minified}.js"></script>
<style>
	/*#map-share, #myMap { width: 100%; height: 600px; }*/
	.dummy { padding-top: 50%; }
	#myMap { position: absolute; top: 0; bottom: 0; left: 0; right: 0; }
	._opheme_bubbleContainer .bubble_individual { height: 80px; }
	#map_legend { position: absolute; top: 0px; right: 0px; z-index: 1000; margin: 0px; padding: 3px; }
</style>
<div id="sharing" class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Hey there, this {$Data.moduleData.jobType} has been shared with the World by {$Data.moduleData.userName} ({$Data.moduleData.smType} @{$Data.moduleData.screen_name}) using <a href="{$Data.company.brand_url}/login?register" target="_blank">{$Data.company.brand}</a>!</h4>
				</div>
				<div class="panel-body">
					<div id="job-preview">
						<div id="loader-element" class="map-loader">
							<div class="loader-content"><i class="fa fa-cog fa-spin fa-lg"></i>loading</div>
						</div>
						<div class="dummy"></div>
						<div id="myMap"></div>
						{include "camp-disc-mapLegend.tpl"}
					</div>
				</div>
			</div>
			<script>var messages = {$Data.moduleData.mapMessages};</script>
			<script src="/jscripts/internal/sharing_parse_scripts{$Data.minified}.js"></script>
		</div>
	</div>
</div>