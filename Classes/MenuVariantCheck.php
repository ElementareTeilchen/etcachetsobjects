<?php

namespace ElementareTeilchen\Etcachetsobjects;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
    public $extKey = 'etcachetsobjects';

    /**
     * if user is on deeper levels we cannot use same menu anymore because then we show subpages depending on current page
     * with setting expAll = 0
     * $conf['individualMenusComingAtLevel'] is the level where you first set expAll = 0
     *
     * root page of menu is defined by $conf['sectorstartId']
     * level from where on additional subpages might pop up: $conf['individualMenusComingAtLevel']
     *
     * @deprecated Replaced by `\ElementareTeilchen\Etcachetsobjects\UserFunc\MenuVariantCheck->levelGroupIdentifier`
     */
    public function levelGroupIdentifier(string $content, array $conf, ServerRequestInterface $request): string
    {
        return GeneralUtility::makeInstance(\ElementareTeilchen\Etcachetsobjects\UserFunc\MenuVariantCheck::class)
            ->levelGroupIdentifier($content, $conf, $request);
    }
}
