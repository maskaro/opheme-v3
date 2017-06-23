<div id="map_cc">
	<a onclick="createShareForm();"><i title="Share {$Data.title} on Social Media" class="fa fa-globe icon-large map-more-info-tooltip"></i></a>
	<a onclick="shareJob();"><i title="Share {$Data.title} by URL" class="fa fa-share icon-large map-more-info-tooltip sharenow"></i></a>
	<a onclick="unShareJob();"><i title="Disable {$Data.title} Sharing" class="fa fa-share icon-large map-more-info-tooltip text-danger unshare"></i></a>
	<a onclick="map_cc_grow();"><i title="Maximise" class="fa fa-expand icon-large map-more-info-tooltip"></i></a>
	<a onclick="map_cc_shrink();"><i title="Minimise" class="fa fa-compress icon-large map-more-info-tooltip"></i></a>
	<a onclick="map_cc_zoomIn();"><i title="Zoom In" class="fa fa-search-plus icon-large map-more-info-tooltip"></i></a>
	<a onclick="map_cc_zoomOut();"><i title="Zoom Out" class="fa fa-search-minus icon-large map-more-info-tooltip"></i></a>
	<span style="display: none" class="current_map_id">-1</span>
	{*<a onclick="map_cc_close();"><i title="Close" class="fa fa-times icon-large map-more-info-tooltip"></i></a>*}
</div>