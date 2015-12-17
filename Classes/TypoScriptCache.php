<?php
namespace ElementareTeilchen\EtCachetsobjects;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Franz Kugelmann <franz.kugelmann@elementare-teilchen.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * wrapper for TS objects which should be cached
 *
 * @author    Franz Kugelmann <franz.kugelmann@elementare-teilchen.de>
 * @package    TYPO3
 * @subpackage    tx_etcachetsobjects
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class TypoScriptCache extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
{
    var $extKey = 'et_cachetsobjects';

    /**
     * The main method of the PlugIn
     *
     * @param    string $content : The PlugIn content
     * @param    array $conf : The PlugIn configuration
     * @return    The content that is displayed on the website
     */
    function handleElement($content, $conf)
    {
        // only use caching, if not in Backend (preview mode contains pages which are hidden)
        if ($GLOBALS['TSFE']->beUserLogin) {
            return $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);
        }

        // only use caching if domain is lowercase, we suspect problems with
#        if ($currentDomainIdentifier != strtolower($currentDomainIdentifier)) {
#            file_put_contents(PATH_site . 'et_log/log_domainproblems.txt', strftime('%Y-%m-%d') . ' - ' . $currentDomainIdentifier . "\n", FILE_APPEND);
#            return $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);
#        }

        $uniqueCacheIdentifiers = array();
        $cacheTags = array();
        // the current domain is used
        // - maybe as tag to clear cache for all TS-caches for this domain, depending on extConf settings
        // - as additional info to create unique identifier, because TS might be the same for different domains
        // MIND: if you change identifier here, do also in hook for cache clearing
        $uniqueCacheIdentifiers['currentDomain'] = trim(str_replace('.', '', \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY')));
        $cacheTags[] = $uniqueCacheIdentifiers['currentDomain'];

        // the PageTS setting
        if ($weHavePageTS) {
            $uniqueCacheIdentifiers['pageTS'] = 'xxx';
            $cacheTags[] = 'xxx';
        }
        // additionalUniqueCacheParameters via TypoScript
        if (isset($conf['additionalUniqueCacheParameters']) && is_array($conf['additionalUniqueCacheParameters.'])) {
            $uniqueCacheIdentifiers['typoScript'] = $this->cObj->getContentObject($conf['additionalUniqueCacheParameters'])->render($conf['additionalUniqueCacheParameters.']);
#            echo '<pre>';print_r($uniqueCacheIdentifiers);echo '</pre>';die();
        }

        $cacheIdentifier = sha1(serialize($uniqueCacheIdentifiers) . serialize($conf));


        $tsCache = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('cache_etcachetsobjects');

        if (FALSE === ($content = $tsCache->get($cacheIdentifier))) {
            $content = $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);
            $tsCache->set(
                $cacheIdentifier,
                $content,
                $cacheTags,
                (int)$conf['cacheTime']
            );
        }

        return $content;
    }
}

?>
