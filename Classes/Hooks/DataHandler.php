<?php

namespace ElementareTeilchen\Etcachetsobjects\Hooks;

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

use ElementareTeilchen\Etcachetsobjects\Event\CollectCacheTagsToBeClearedEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tcemain hooks. See ext_localconf.php for activating/deactivating them.
 *
 * @author    Franz Kugelmann <franz.kugelmann@elementare-teilchen.de>
 */
class DataHandler
{
    public function __construct(
        #[Autowire(service: 'cache.etcachetsobjects_db')]
        protected readonly FrontendInterface $cache,
        #[Autowire(expression: 'service("extension-configuration").get("etcachetsobjects")')]
        protected readonly array $extensionConfiguration,
        protected readonly EventDispatcher $eventDispatcher,
        protected readonly SiteFinder $siteFinder
    ) {}

    /**
     * we need to clear our TS object caches if menu relevant data is saved in pages (title, nav_title, ...)
     */
    public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$parentObj): void
    {
        if ($table == 'pages') {
            //$incomingFieldArray contains all fields: todo: how can we easily check only on some fields?
            //Performance-Relevant? how often are other fields edited?
            //if (count(array_intersect_key($incomingFieldArray, array('title','nav_title')))) {
            //    $this->handleFlushing($id);
            //}
            $this->handleFlushing($id);
        }
    }

    /**
     * This hook is called for example by list module if deleting a record.
     * At this point the action is not yet done, so for example deleted is still 0.
     */
    public function processCmdmap_preProcess($command, $table, $id, $value, &$pObj): void
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
     */
    private function handleFlushing($pageId): void
    {
        switch ($this->extensionConfiguration['clearCacheVariant'] ?? '') {
            case 'PageTS Setting':
                // if pageId is no int (eg "NEW_1"), we just finish cache flushing
                if (is_int($pageId) === false) {
                    return;
                }
                $pageTSconfig = BackendUtility::getPagesTSconfig($pageId)['tx_etcachetsobjects.']['clearByTags'] ?? '';
                $tagsToBeFlushed = GeneralUtility::trimExplode(',', $pageTSconfig, true);

                $tagsToBeFlushed = $this->eventDispatcher->dispatch(
                    new CollectCacheTagsToBeClearedEvent($pageId, $tagsToBeFlushed)
                )->getCacheTags();

                $tagsToBeFlushed = array_unique($tagsToBeFlushed);
                foreach ($tagsToBeFlushed as $cacheTag) {
                    if ($this->cache->isValidTag($cacheTag)) {
                        $this->cache->flushByTag($cacheTag);
                    }
                }

                break;

            case 'Site based':
                // if pageId is no int (eg "NEW_1"), we just finish cache flushing
                if (is_int($pageId) === false) {
                    return;
                }

                try {
                    $site = $this->siteFinder->getSiteByPageId($pageId);
                    $this->cache->flushByTag($site->getIdentifier());
                } catch (SiteNotFoundException $e) {
                }

                break;

            case 'From all pages':
            default:
                // default is also just flush all
                $this->cache->flush();
                break;
        }
    }
}
