<?php

	/**
	 * Set this to '.min' once all JS and CSS files have been minified and reside next to the un-minified versions.
	 * Development: '' / Production: '.min'
	 */
	if (__oLIVE__) { $minified = '.min'; }
	else { $minified = ''; }

	/**
	 * @var array Company info. ['name', 'address', 'brand', 'support', 'domain']
	 */
	$company = array(
		'id' => trim(__oCompanyID__),
		'name' => trim(__oCompanyName__),
		'address' => trim(__oCompanyAddress__),
		'brand' => trim(__oCompanyBrand__),
		'brand_url' => trim(__oCompanyBrandURL__),
		'support' => trim(__oCompanySupport__),
		'domain' => trim(__oCompanyDomain__),
		'terms' => trim(__oCompanyTerms__)
	);
	
	/**
	 * @var array Company logos. ['small', 'medium', 'large']
	 */
	$logos = array(
		'small' => (
			is_file(__oCompanyFiles__ . '/logos/small-logo.png')
			?
			base64_encode(file_get_contents(__oCompanyFiles__ . '/logos/small-logo.png'))
			:
			null
		),
		'medium' => (
			is_file(__oCompanyFiles__ . '/logos/medium-logo.png')
			?
			base64_encode(file_get_contents(__oCompanyFiles__ . '/logos/medium-logo.png'))
			:
			null
		),
		'large' => (
			is_file(__oCompanyFiles__ . '/logos/large-logo.png')
			?
			base64_encode(file_get_contents(__oCompanyFiles__ . '/logos/large-logo.png'))
			:
			null
		),
		'favicon' => (
			is_file(__oCompanyFiles__ . '/favicon/favicon.png')
			?
			base64_encode(file_get_contents(__oCompanyFiles__ . '/favicon/favicon.png'))
			:
			null
		)
	);
	
	/**
	 * @var array Default CSS files.
	 */
	$cssFiles = array(
		'/stylesheets/internal/bootstrap.min.css',
		//'//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css',
		'/stylesheets/external/bootstrap/font-awesome.min.css',
		'/stylesheets/external/bootstrap/bootstrap-datepicker3.min.css',
		'/stylesheets/external/bootstrap/bootstrap-tour.min.css',
		'/stylesheets/external/jquery-ui/jquery-ui.custom.min.css',
		'/stylesheets/external/jquery-ui/jquery-ui.structure.min.css',
		'/stylesheets/external/jquery-ui/jquery-ui.theme.min.css',
		'/stylesheets/external/jquery/jquery.dataTables.min.css',
		'/stylesheets/external/jquery/jquery.dataTables_themeroller.min.css',
		'/stylesheets/external/jquery/jquery.dataTables.tableTools.min.css',
		'/stylesheets/external/jquery/jquery.timepicker.min.css',
		'/stylesheets/external/jquery/jquery.tooltipster.min.css',
		'/stylesheets/external/jquery/jquery.tooltipster-light.min.css',
		'/stylesheets/external/jquery/jquery.tooltipster-noir.min.css',
		'/stylesheets/external/jquery/jquery.tooltipster-punk.min.css',
		'/stylesheets/external/jquery/jquery.tooltipster-shadow.min.css',
		//'/stylesheets/external/jquery/jquery.flipclock.min.css',
		//'/stylesheets/external/pnotify/pnotify.custom.min.css',
		'/stylesheets/internal/main' . $minified . '.css',
		'/stylesheets/internal/map_cc' . $minified . '.css',
		'/stylesheets/internal/custom_2.0' . $minified . '.css'
	);
	
	/**
	 * @var string Company CSS changes as string.
	 */
	$cssCompanyChanges = file_get_contents(__oCompanyFiles__ . '/css/changes' . $minified . '.css');
	
	/**
	 * @var array Extra CSS *CODE* to load. Must use file_get_contents().
	 */
	$cssExtra = array();
	
	/**
	 * @var array Default JS files. Goes inside head.
	 */
	$jsFiles = array(
		//'//code.jquery.com/jquery.js',
		//'/jscripts/external/jquery/jquery-2.1.1.min.js', //does not support IE < v9
		'/jscripts/external/jquery/jquery-1.11.1.min.js',
		'/jscripts/external/jquery/jquery-ui.custom.min.js',
		'/jscripts/external/jquery/jquery.ui.touch-punch.min.js',
		//'//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js',
		'/jscripts/external/bootstrap/bootstrap.min.js',
		'/jscripts/external/jquery/jquery.scrollTo.min.js',
		'/jscripts/external/jquery/jquery.validate.min.js',
		'/jscripts/external/jquery/jquery.validate.additional-methods.min.js',
		'/jscripts/external/jquery/jquery.timeago.min.js',
		'/jscripts/external/jquery/jquery.fileDownload.min.js',
		'/jscripts/external/jquery/jquery.tooltipster.min.js',
		//'/jscripts/external/jquery/jquery.flipclock.min.js',
		//'/jscripts/external/other/countdown.min.js',
		//'/jscripts/external/other/pnotify.custom.min.js',
		//'/jscripts/external/jquery/jquery.shuffle.min.js',
		'/jscripts/internal/production-top' . $minified . '.js'
	);
	
	/**
	 * @var array Default end JS files. Goes inside body before any other JS files.
	 */
	$jsFilesBottom = array(
		'/jscripts/internal/production-bottom' . $minified . '.js'
	);
	
	/**
	 * @var array Other available JS files.
	 */
	$extraJs = array(
		'google' => 'https://maps.googleapis.com/maps/api/js?key=' . __oGMAPS_API_KEY__ . '&sensor=false',
		'bootstrap.datepicker' => '/jscripts/external/bootstrap/bootstrap-datepicker.min.js',
		'bootstrap.tour' => '/jscripts/external/bootstrap/bootstrap-tour.min.js',
		'jquery.timepicker' => '/jscripts/external/jquery/jquery.timepicker.min.js',
		'google.marker-clusterer' => '/jscripts/external/other/markerclustererplus.min.js',
		//'google.marker-clusterer' => '/jscripts/external/other/markerclusterer.min.js',
		'json2' => '/jscripts/external/other/json2.min.js',
		'autolinker' => '/jscripts/external/other/autolinker.min.js',
		'wcloud2' => '/jscripts/external/wordcloud2/wordcloud2.min.js',
		'jdt' => '/jscripts/external/jquery/jquery.dataTables.min.js',
		'tooltip' => '/jscripts/internal/tooltip' . $minified . '.js',
		'opheme' => '/jscripts/internal/oPheme_ui' . $minified . '.js',
		'camp-disc-utils' => '/jscripts/internal/camp-disc-utils' . $minified . '.js',
		'camp-disc-utils-top' => '/jscripts/internal/camp-disc-utils-top' . $minified . '.js',
		'camp-disc-editor' => '/jscripts/internal/camp-disc-editor' . $minified . '.js',
		'reseller-admin' => '/jscripts/internal/reseller-admin' . $minified . '.js',
		'reseller-admin-top' => '/jscripts/internal/reseller-admin-top' . $minified . '.js',
	);
	
	/**
	 * @var array JS files per module assignment. Goes inside head after default JS files.
	 */
	$assignJs = array(
		'dashboard' => array(
			'bootstrap.tour'
		),
		'account' => array(
			'bootstrap.tour'
		),
		'discover' => array(
			'json2',
			'google',
			'bootstrap.datepicker',
			'jquery.timepicker',
			'google.marker-clusterer',
			'wcloud2',
			'tooltip',
			'opheme',
			'camp-disc-utils-top',
			'autolinker'
		),
		'campaign' => array(
			'json2',
			'google',
			'bootstrap.datepicker',
			'jquery.timepicker',
			'google.marker-clusterer',
			'wcloud2',
			'tooltip',
			'opheme',
			'camp-disc-utils-top',
			'autolinker'
		),
		'interaction' => array(
			'json2',
			'google',
			'tooltip',
			'opheme',
			'autolinker'
		),
		'admin' => array(
			'google',
			'jdt',
			'reseller-admin-top'
		),
		'reseller' => array(
			'google',
			'jdt',
			'reseller-admin-top'
		)
	);
	
	/**
	 * @var array JS files per module assignment. Goes just before body ends, but before any module JS file.
	 */
	$assignEndJs = array(
		'discover' => array(
			'camp-disc-editor',
			'camp-disc-utils'
		),
		'campaign' => array(
			'camp-disc-editor',
			'camp-disc-utils'
		),
		'admin' => array(
			'reseller-admin'
		),
		'reseller' => array(
			'reseller-admin'
		)
	);
	
	/**
	 * @var array Template default data.
	 */
	$data = array(
		'company' => $company,
		'logos' => $logos,
		'cssFiles' => $cssFiles,
		'cssExtra' => $cssExtra,
		'cssCompanyChanges' => $cssCompanyChanges,
		'jsFiles' => $jsFiles,
		'jsFilesBottom' => $jsFilesBottom
	);