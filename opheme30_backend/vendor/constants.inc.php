<?php

	/**
	 * Controls user registration access.
	 */
	define('__oREGISTRATION_ACTIVE__', true);

	/**
	 * CI flag.
	 */
	define('__oCIo__', false);
	
	/**
	 * Controls SSL status.
	 */
	define('__oSSL_ENABLED__', true);
	
	/**
	 * Config location
	 */
	define('__oCONF__', __oDIR__ . '/conf');
	
	/**
	 * Vendor location
	 */
	define('__oVENDOR__', __oDIR__ . '/vendor');
	
	/**
	 * Vendor lib location
	 */
	define('__oLIB__', __oVENDOR__ . '/lib');
	
	/**
	 * Vendor module location
	 */
	define('__oMOD__', __oVENDOR__ . '/modules');
	
	/**
	 * Vendor template location
	 */
	define('__oTPL__', __oVENDOR__ . '/templates');
	
	/**
	 * System logs - folder next to oPheme
	 */
	define('__oLOGS__', __oDIR__ . '/logs');
	
	/**
	 * Server name, all lower case
	 */
	$sn = strtolower(filter_input(INPUT_SERVER, 'SERVER_NAME'));
	if (empty($sn)) { //backend execution
		/**
		 * Company Identifier
		 */
		if (isset($companyId)) { define('__oCompanyID__', $companyId); } else { define('__oCompanyID__', 'opheme'); }
		/**
		 * Continuous Integration flag
		 */
		if (__oCIo__ === true) {
			define('__oCI__', true);
		} else {
			define('__oCI__', false);
		}
		/**
		 * System Demo flag
		 */
		define('__oDEMO__', false);
	}
	
	/**
	 * Company Files location
	 */
	define('__oCompanyFiles__', __oDIR__ . '/rebrands/' . __oCompanyID__);

	//company domain name
	if (__oDEMO__ === true) {
		/**
		 * Company Domain name
		 */
		define('__oCompanyDomain__', 'opheme.com');
	} else {
		/**
		 * Company Domain name
		 */
		define('__oCompanyDomain__', file_get_contents(__oCompanyFiles__ . '/words/domain_name.inc'));
	}

	/**
	 * Company Brand name
	 */
	define('__oCompanyBrand__', file_get_contents(__oCompanyFiles__ . '/words/brand_name.inc'));

	/**
	 * Company Name
	 */
	define('__oCompanyName__', file_get_contents(__oCompanyFiles__ . '/words/company_name.inc'));

	/**
	 * Company Address
	 */
	define('__oCompanyAddress__', file_get_contents(__oCompanyFiles__ . '/words/company_address.inc'));

	/**
	 * Company Support url
	 */
	define('__oCompanySupport__', file_get_contents(__oCompanyFiles__ . '/words/company_support.inc'));

	/**
	 * Company Terms and Conditions
	 */
	define('__oCompanyTerms__', (__oREGISTRATION_ACTIVE__?file_get_contents(__oCompanyFiles__ . '/terms/tac.inc'):''));
	
	/**
	 * Company Brand URL
	 */
	define('__oCompanyBrandURL__', 'http' . (__oSSL_ENABLED__ === true?'s':'') . '://' . (__oCI__ === true?'ci_':'') . 'portal' . '.' . __oCompanyDomain__);
	
	/**
	 * Current Year
	 */
	define('__oYEAR__', date('Y'));