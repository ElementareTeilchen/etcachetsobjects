<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

// insert hook processDatamapClass, there are different hooks based in this class
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['etcachetsobjects'] = \ElementareTeilchen\EtCachetsobjects\Hooks\DataHandler::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['etcachetsobjects'] = \ElementareTeilchen\EtCachetsobjects\Hooks\DataHandler::class;

// Add new cache configurations
// The is_array() check is done to enable administrators to overwrite configuration of caches in LocalConfiguration.php. During bootstrap, any ext_localconf.php is loaded after DefaultConfiguration.php and AdditionalConfiguration.php are loaded, so it is important to make sure that the administrator did not already set any configuration of the extensions cache.
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['etcachetsobjects_db'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['etcachetsobjects_db'] = [
        // we use default backend (database at time of writing)
        'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
	'groups' => ['pages','all'],
    ];
}

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['etcachetsobjects_transient'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['etcachetsobjects_transient'] = [
        'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\TransientMemoryBackend',
        'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
    ];
}
