<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

// insert hook processDatamapClass, there are different hooks based in this class
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['etcachetsobjects'] = \ElementareTeilchen\EtCachetsobjects\Hooks\DataHandler::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['etcachetsobjects'] = \ElementareTeilchen\EtCachetsobjects\Hooks\DataHandler::class;

// Add a new cache configuration
$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_etcachetsobjects'] = array();
$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_etcachetsobjects']['frontend'] = 'TYPO3\CMS\Core\Cache\Frontend\StringFrontend';
