<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// insert hook processDatamapClass, there are different hooks based in this class
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['etcachetsobjects'] = 'EXT:et_cachetsobjects/hooks/class.tx_etcachetsobjects_tcemain.php:tx_etcachetsobjects_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['etcachetsobjects'] = 'EXT:et_cachetsobjects/hooks/class.tx_etcachetsobjects_tcemain.php:tx_etcachetsobjects_tcemain';

// Add a new cache configuration
$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_etcachetsobjects_cache'] = array();
$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_etcachetsobjects_cache']['frontend'] = 't3lib_cache_frontend_StringFrontend';

?>