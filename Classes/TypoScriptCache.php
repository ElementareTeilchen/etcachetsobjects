<?php

namespace ElementareTeilchen\Etcachetsobjects;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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


class TypoScriptCache
{
    public string $extKey = 'etcachetsobjects';

    protected ContentObjectRenderer $cObj;


    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }

    /**
     * before 3.0 this method was called by TS,
     * we keep it for backwards compatibility
     *
     * @param    string $content : The PlugIn content
     * @param    array $conf : The PlugIn configuration
     * @param    ServerRequestInterface $request : The current request object
     * @return   string : The content that is displayed on the website
     * @deprecated
     */
    public function handleElement(string $content, array $conf, ServerRequestInterface $request): string
    {
        return $this->databaseBackend($content, $conf, $request);
    }


    /**
     * called from TS as caching layer for TS-Objects
     * used the Database Backend
     *
     * @param    string $content : The PlugIn content
     * @param    array $conf : The PlugIn configuration
     * @param    ServerRequestInterface $request : The current request object
     * @return   string : The content that is displayed on the website
     */
    public function databaseBackend(string $content, array $conf, ServerRequestInterface $request): string
    {
        $tsCache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('etcachetsobjects_db');
        $uniqueCacheIdentifiers = [];

        // each BE user gets own cache because of access restricted pages
        // on big sites this makes sense because if editor works on content
        // she has to wait several seconds each time she checks the frontend mainly because of the menu
        $context = GeneralUtility::makeInstance(Context::class);
        if ($context->getPropertyFromAspect('backend.user', 'isLoggedIn') && !empty($GLOBALS['BE_USER']->user['uid'])) {
            $uniqueCacheIdentifiers['beUser'] = $GLOBALS['BE_USER']->user['uid'];
        }

        $cacheTags = [];

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId($GLOBALS['TSFE']->id);
        if ($site instanceof Site) {
            // MIND: if you change identifier here, do also in hook for cache clearing
            $uniqueCacheIdentifiers['siteIdentifier'] = $site->getIdentifier();
            $cacheTags[] = $site->getIdentifier();
        }

        $cacheIdentifier = $this->createCacheIdentifier($conf, $uniqueCacheIdentifiers);

        return $this->checkCache($tsCache, $conf, $cacheIdentifier, $cacheTags);
    }


    /**
     * called from TS as caching layer for TS-Objects
     * used the Transient Backend, which means cache is only used within current page call
     * the TS-object must be called at least two times for this to make sense
     *
     * @param    string $content : The PlugIn content
     * @param    array $conf : The PlugIn configuration
     * @param    ServerRequestInterface $request : The current request object
     * @return   string : The content that is displayed on the website
     */
    public function transientBackend(string $content, array $conf, ServerRequestInterface $request): string
    {
        $tsCache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('etcachetsobjects_transient');
        $cacheIdentifier = $this->createCacheIdentifier($conf);
        return $this->checkCache($tsCache, $conf, $cacheIdentifier);
    }


    /**
     * @param array $conf
     * @param array $uniqueCacheIdentifiers
     * @return string
     */
    protected function createCacheIdentifier(array $conf, array $uniqueCacheIdentifiers = []): string
    {
        // additionalUniqueCacheParameters via TypoScript
        if (isset($conf['additionalUniqueCacheParameters']) && is_array($conf['additionalUniqueCacheParameters.'])) {
            $uniqueCacheIdentifiers['typoScript'] = $this->cObj->getContentObject($conf['additionalUniqueCacheParameters'])->render($conf['additionalUniqueCacheParameters.']);
        }

        $cacheIdentifier = sha1(serialize($uniqueCacheIdentifiers) . serialize($conf));

        return $cacheIdentifier;
    }


    /**
     * check if cache already exists
     * fetch content if there or
     * create, store in cache and return content
     *
     * @param FrontendInterface $currentCache
     * @param array $conf
     * @param string $cacheIdentifier
     * @param array $cacheTags
     * @return string
     */
    protected function checkCache(
        FrontendInterface $currentCache,
        array $conf,
        string $cacheIdentifier,
        array $cacheTags = []
    ): string
    {
        if (false === ($content = $currentCache->get($cacheIdentifier))) {
            $content = $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);

            // make sure we do not cache elements when in preview mode
            // i.e. hidden pages are shown in menu
            $context = GeneralUtility::makeInstance(Context::class);
            if ($context->getPropertyFromAspect('frontend.preview', 'isPreview')
                || $context->getPropertyFromAspect('visibility', 'includeHiddenPages')
            ) {
                return $content;
            }

            // additionalTags via TypoScript
            if (isset($conf['additionalTags.']) && is_array($conf['additionalTags.'])) {
                foreach ($conf['additionalTags.'] as $tag) {
                    $cacheTags[] = $tag;
                }
            }

            $currentCache->set(
                $cacheIdentifier,
                $content,
                $cacheTags,
                (int)$conf['cacheTime']
            );
            return $content;
        }
        return $content;
    }
}
