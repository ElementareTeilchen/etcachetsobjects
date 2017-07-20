<?php
namespace ElementareTeilchen\Etcachetsobjects;

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
 * class to find identifiers for menu variants
 *
 * @author    Franz Kugelmann <franz.kugelmann@elementare-teilchen.de>
 * @package    TYPO3
 * @subpackage    tx_etcachetsobjects
 */

class MenuVariantCheck
{
    var $extKey = 'etcachetsobjects';

    /**
     * if user is on deeper levels we cannot use same menu anymore because then we show subpages depending on current page
     * with setting expAll = 0
     * $conf['individualMenusComingAtLevel'] is the level where you first set expAll = 0
     *
     * root page of menu is defined by $conf['sectorstartId']
     * level from where on additional subpages might pop up: $conf['individualMenusComingAtLevel']
     *
     * @param    string $content : The PlugIn content
     * @param    array $conf : The PlugIn configuration
     * @return    The content that is displayed on the website
     */
    function levelGroupIdentifier($content, $conf)
    {
        // to we have something already there to check that?
        $currentPageSubpagesCount = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*','pages','pid=' . $GLOBALS['TSFE']->id);
        #echo '<pre>';print_r($currentPageSubpagesCount);echo '</pre>';
        #echo '<pre>';print_r($GLOBALS['TSFE']->rootLine);echo '</pre>';

        // go from current page up the rootline
        $sectorMenuLevelCount = 0;
        foreach ($GLOBALS['TSFE']->rootLine as $page) {
            #echo $page['uid']. '<br>';
            if ($page['uid'] == $conf['sectorstartId']) {
                break;
            }
            $sectorMenuLevelCount++;
        }

        // if on deeper level than $conf['individualMenuLevel'] and page has _NO_ subpages: use pageUid of parent page as additional identifier
        if ($sectorMenuLevelCount > $conf['individualMenusComingAtLevel'] && $currentPageSubpagesCount === 0) {
            return $GLOBALS['TSFE']->page['pid'];
        }

        // if on level $conf['individualMenuLevel'] or deeper and page has subpages: use pageUid as additional identifier
        if ($sectorMenuLevelCount >= $conf['individualMenusComingAtLevel'] && $currentPageSubpagesCount > 0) {
            return $GLOBALS['TSFE']->id;
        }

        // all else: return nothing (no extra cacheIdentifier), all fine
        return '';
    }
}

?>
