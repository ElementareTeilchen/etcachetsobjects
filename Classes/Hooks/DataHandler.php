<?php

namespace ElementareTeilchen\EtCachetsobjects\Hooks;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2012 Franz Kugelmann (franz.kugelmann@elementare-teilchen.de)
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
 * tcemain hooks. See ext_localconf.php for activating/deactivating them.
 *
 * @author    Franz Kugelmann <franz.kugelmann@elementare-teilchen.de>
 */

use \TYPO3\CMS\Backend\Utility\BackendUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandler
{

    /*
    * we need to clear our TS object caches if menu relevant data is saved in pages (title, nav_title, ...)
     * @param array Changed fields
     * @param string The table we are working on
     * @param integer Uid of record
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler reference to parent object
     * @return void
     */
    function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$parentObj)
    {
        if ($table == 'pages') {
            //$incomingFieldArray contains all fields: todo: how can we easily check only on some fields?
            //Performance-Relevant? how often are other fields edited?
            #if (count(array_intersect_key($incomingFieldArray, array('title','nav_title')))) {
            $this->handleFlushing($id);

            #}
        }
    }

    /**
     * This hook is called for example by list module if deleting a record.
     * At this point the action is not yet done, so for example deleted is still 0.
     *
     * @param string new, delete, ...
     * @param string Table we are working on
     * @param int Record uid
     * @param mixed    Unused
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler Unused reference to parent object
     * @return    void
     */
    public function processCmdmap_preProcess($command, $table, $id, $value, &$pObj)
    {
        if ($table == 'pages') {
            $this->handleFlushing($id);
        }
    }


    /**
     * we have different variants to flush caches
     * - based on PageTSConfig
     * - based on domain related pagetree part
     * - whole tree is relevant (simple, but cache often cleared if many editor changes)
     * @param $pageId
     */
    private function handleFlushing($pageId)
    {
        $tsCache = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('cache_etcachetsobjects');

        switch ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['et_cachetsobjects']['setup']['clearCacheVariant']) {

            case 'PageTS Setting':
                $pageTSconfig = BackendUtility::getPagesTSconfig($pageId);
                $tagsToBeFlushed = explode(',',@$pageTSconfig['tx_etcachetsobjects.']['clearByTags']);
                foreach($tagsToBeFlushed as $cacheTag) {
                    $tsCache->flushByTag($cacheTag);
                }
                break;

            case 'Domain based':
                $rootLine = BackendUtility::BEgetRootLine($pageId);
                //page with id 0 wont contain domain record
                array_pop($rootLine);
                foreach ($rootLine as $pageDataArray) {
                    foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'] as $configuredDomainName => $configuredDomainConfiguration) {
                        if ($configuredDomainConfiguration['pagePath']['rootpage_id'] == $pageDataArray['uid']) {
                            $foundDomain = str_replace('.', '', $configuredDomainName);
                            #debug($foundDomain);
                            $tsCache->flushByTag($foundDomain);
                        }
                    }
                }
                break;

            // default is also just flush all
            case 'From all pages':
            default:
            $tsCache->flush();
                break;
        }

    }

}

?>