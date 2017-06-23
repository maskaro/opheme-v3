<?php

	/**
	 * Smarty options
	 */

	// smarty template dir
	$site->smarty->setTemplateDir(__oTPL__ . '/portal');
	// set debugging according to global operations constant
	$site->smarty->debugging = __oDEBUG_OPS__;
	
	// portal template location
	$headerTemplate = 'html_head.tpl';
	$menuTemplate = 'html_menu.tpl';
	$footerTemplate = 'html_foot.tpl';
	$moduleTemplate = $module . '.tpl';
	$messagesTemplate = 'notices.tpl';
	
	/**
	 * Order of Templates:
	 *					->	[     Errors	   ]	->
	 *	Header -> Menu				OR					Footer
	 *					->	[ Messages -> Module ]	->
	 */
	
	// load template general settings
	require $site->getModuleToLoad('template_settings');
	
	// list of available company modules
	$data['company']['modules'] = $site->company->getModules();
	
	// assign any available module data
	if (!empty($moduleData)) { $data['moduleData'] = $moduleData; unset($moduleData); }
	
	// other template variables
	$data['moduleName'] = $module;
	$data['title'] = ucfirst($module);
	if ($loggedIn) { $data['user'] = $_SESSION[__oSESSION_USER_PARAM__]; }
	if (!empty($_SESSION['formData'])) { $data['formData'] = $_SESSION['formData']; }
	$data['loggedIn'] = $loggedIn;
	
	/**
	 * @var array Extra JS files at the beginning of the template.
	 */
	$jsFilesExtra = array();
	if (isset($assignJs[$module])) {
		foreach ($assignJs[$module] as $js) {
			$jsFilesExtra[] = $extraJs[$js];
		}
	}
	$data['jsFilesExtraTop'] = $jsFilesExtra;
	
	/**
	 * @var array Extra JS files at the end of the template.
	 */
	$jsFilesEndExtra = array();
	if (isset($assignEndJs[$module])) {
		foreach ($assignEndJs[$module] as $js) {
			$jsFilesEndExtra[] = $extraJs[$js];
		}
	}
	$data['jsFilesEndExtraBottom'] = $jsFilesEndExtra;
	
	/**
	 * @var string Extra JS file to go at the end of the template.
	 */
	$extraJsFile = '/jscripts/internal/module/' . $module . $minified . '.js';
	$jsBodyFile = (
		is_file(__oPUBLIC__ . $extraJsFile)
		?
		$extraJsFile
		:
		null
	);
	$data['jsBodyFile'] = $jsBodyFile;
	
	/**
	 * @var string Extra JS file to go at the end of the template.
	 */
	$moduleCSSFile = '/stylesheets/internal/module/' . $module . $minified . '.css';
	$ModuleCSS = (
		is_file(__oPUBLIC__ . $moduleCSSFile)
		?
		$moduleCSSFile
		:
		null
	);
	$data['cssModuleFile'] = $ModuleCSS;
	
	$data['minified'] = $minified;
	
	// assign all collected data to template
	$site->smarty->assign('Data', $data);
	
	// display header
	$site->smarty->display($headerTemplate);
	
	// display critical notices
	if (!empty($notices['critical'])) {
		
		$site->smarty->assign('NoticeType', 'critical');
		$site->smarty->assign('Notices', $notices);
		$site->smarty->display($messagesTemplate);
		
	} else {
		
		//if logged in, display menu
		if ($loggedIn === true) { $site->smarty->display($menuTemplate); }
		
		//handle casual notices
		$notices = array();
		//get all app manageable errors
		$notices['danger'] = $site->message->getAll(null, 'ERR');
		//get all app warning messages
		$notices['warning'] = $site->message->getAll(null, 'WAR');
		//get all app informational messages
		$notices['info'] = $site->message->getAll(null, 'INFO');
		//get all app success messages
		$notices['success'] = $site->message->getAll(null, 'OK');
		
		// if any non critical messages exist
		if (!empty($notices['danger']) || !empty($notices['warning']) || !empty($notices['info']) || !empty($notices['success'])) {
			
			// assign and display them
			$site->smarty->assign('NoticeType', 'casual');
			$site->smarty->assign('Notices', $notices);
			$site->smarty->display($messagesTemplate);

		}
		
		// display module
		$site->smarty->display($moduleTemplate);
		
	}
	
	// display globals debug info, if needed
	if (__oDEBUG_GLOBALS__) { include $site->getModuleToLoad('debug'); }
	
	// record template execution end time
	$display = calcMicroTimeDiff($startTimeMilis, microtime(true));
	
	// set rendering start time
	$site->smarty->assign('ExecutionTime', $display);
	
	// display footer
	$site->smarty->display($footerTemplate);