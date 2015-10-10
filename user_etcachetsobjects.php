<?php
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
 * @author	Franz Kugelmann <franz.kugelmann@elementare-teilchen.de>
 * @package	TYPO3
 * @subpackage	tx_etcachetsobjects
 */
class user_etcachetsobjects extends tslib_pibase {
	var $prefixId = 'tx_etcachetsobjects_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_etcachetsobjects_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'et_cachetsobjects';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		// only use caching, if not in Backend (preview mode contains pages which are hidden)
		if ($GLOBALS['TSFE']->beUserLogin) {
			return $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);
		}
			//the current domain is used
			// - as tag to clear cache for all TS-caches for this domain
			// - as additional info to create unique identifier, because TS might be the same for different domains
		//MIND: if you change identifier here, do also in hook for cache clearing
		$currentDomainIdentifier = trim(str_replace('.','',t3lib_div::getIndpEnv('TYPO3_HOST_ONLY')));
		//only use caching if domain is lowercase, we suspect problems with
		if ($currentDomainIdentifier != strtolower($currentDomainIdentifier)) {
			file_put_contents(PATH_site . 'et_log/log_domainproblems.txt', strftime('%Y-%m-%d') . ' - ' . $currentDomainIdentifier . "\n", FILE_APPEND);
			return $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);
		}
			//if additionalUniqueCacheParameters are given, use them to create identifer to be able to have different cache entries depending on this parameters
		if (isset($conf['additionalUniqueCacheParameters']) && is_array($conf['additionalUniqueCacheParameters.'])) {
			$cacheIdentifier = sha1($currentDomainIdentifier . serialize($conf) . $this->cObj->getContentObject($conf['additionalUniqueCacheParameters'])->render($conf['additionalUniqueCacheParameters.']));
		} else {
			$cacheIdentifier = sha1($currentDomainIdentifier . serialize($conf));
		}
		$cacheTags = array($currentDomainIdentifier);

		$myCacheInstance = $GLOBALS['typo3CacheManager']->getCache('cache_etcachetsobjects_cache');

		if (FALSE === ($content = $myCacheInstance->get($cacheIdentifier))) {
			$content = $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);
			$myCacheInstance->set(
				$cacheIdentifier,
				$content,
				$cacheTags,
				(int) $conf['cacheTime']
			);
		}

		#tx_abzdeveloper::debug('fr','neu: ' . $cacheIdentifier);
		return $content;
	}
}
?>
