<?php

use ElementareTeilchen\Etcachetsobjects\Hooks\DataHandler;

defined('TYPO3') or die('Access denied.');

(function () {
    // insert hook processDatamapClass, there are different hooks based in this class
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['etcachetsobjects'] = DataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['etcachetsobjects'] = DataHandler::class;

    // Add new cache configurations
    // The is_array() check is done to enable administrators to overwrite configuration of caches in LocalConfiguration.php.
    // https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/CachingFramework/Developer/Index.html
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['etcachetsobjects_db']
        ??= [];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['etcachetsobjects_db']['groups']
        ??= ['pages', 'all'];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['etcachetsobjects_transient']
        ??= [];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['etcachetsobjects_transient']['backend']
        ??= \TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend::class;
})();
