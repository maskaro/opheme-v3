<?php
	
	if (!__oREGISTRATION_ACTIVE__) { $site->smarty->assign('CompanyTerms', file_get_contents(__oCompanyFiles__ . '/terms/tac.inc')); }