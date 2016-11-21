<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

// insert hook processDatamapClass, there are different hooks based in this class
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['etcachetsobjects'] = \ElementareTeilchen\EtCachetsobjects\Hooks\DataHandler::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['etcachetsobjects'] = \ElementareTeilchen\EtCachetsobjects\Hooks\DataHandler::class;

// Add new cache configurations
$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_etcachetsobjects'] = array(
    // we use default backend (database at time of writing)
    'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
);

$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_etcachetsobjects_transient'] = array(
    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\TransientMemoryBackend',
    'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
);
