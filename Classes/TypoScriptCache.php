<?php

namespace ElementareTeilchen\Etcachetsobjects;

use ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\DatabaseBackendCacheHandler;
use ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\TransientBackendCacheHandler;
use Psr\Http\Message\ServerRequestInterface;
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
     *
     * @deprecated Replaced by `\ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\DatabaseBackendCacheHandler->handle`
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
     *
     * @deprecated Replaced by `\ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\DatabaseBackendCacheHandler->handle`
     */
    public function databaseBackend(string $content, array $conf, ServerRequestInterface $request): string
    {
        $cacheHandler = GeneralUtility::makeInstance(DatabaseBackendCacheHandler::class);
        $cacheHandler->setContentObjectRenderer($this->cObj);
        return $cacheHandler->handle($content, $conf, $request);
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
     *
     * @deprecated Replaced by `\ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\TransientBackendCacheHandler->handle`
     */
    public function transientBackend(string $content, array $conf, ServerRequestInterface $request): string
    {
        $cacheHandler = GeneralUtility::makeInstance(TransientBackendCacheHandler::class);
        $cacheHandler->setContentObjectRenderer($this->cObj);
        return $cacheHandler->handle($content, $conf, $request);
    }
}
