<?php

namespace ElementareTeilchen\Etcachetsobjects\UserFunc;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

/**
 * class to find identifiers for menu variants
 *
 * @author    Franz Kugelmann <franz.kugelmann@elementare-teilchen.de>
 * @package    TYPO3
 * @subpackage    tx_etcachetsobjects
 */
class MenuVariantCheck
{
    public function __construct(
        protected PageRepository $pageRepository
    ) {}

    /**
     * if user is on deeper levels we cannot use same menu anymore because then we show subpages depending on current page
     * with setting expAll = 0
     * $conf['individualMenusComingAtLevel'] is the level where you first set expAll = 0
     *
     * root page of menu is defined by $conf['sectorstartId']
     * level from where on additional subpages might pop up: $conf['individualMenusComingAtLevel']
     */
    public function levelGroupIdentifier(string $content, array $conf, ServerRequestInterface $request): string
    {
        // beware: don't use second parameter in getMenu to filter fields, you need quite some for correct mount point behaviour
        $currentPageSubpagesCount = count(
            $this->pageRepository->getMenu(
                $request->getAttribute('frontend.page.information')->getId()
            )
        );

        // go from current page up the rootline
        $sectorMenuLevelCount = 0;
        foreach ($request->getAttribute('frontend.page.information')->getRootLine() as $page) {
            if ($page['uid'] == $conf['sectorstartId']) {
                break;
            }
            $sectorMenuLevelCount++;
        }

        // if on deeper level than $conf['individualMenusComingAtLevel'] and page has zero subpages: use pageUid of parent page as additional identifier
        if ($sectorMenuLevelCount > $conf['individualMenusComingAtLevel'] && $currentPageSubpagesCount === 0) {
            return (string)$request->getAttribute('frontend.page.information')->getPageRecord()['pid'];
        }

        // if on level $conf['individualMenusComingAtLevel'] or deeper and page has subpages: use pageUid as additional identifier
        if ($sectorMenuLevelCount >= $conf['individualMenusComingAtLevel'] && $currentPageSubpagesCount > 0) {
            return (string)$request->getAttribute('frontend.page.information')->getId();
        }

        // all else: return nothing (no extra cacheIdentifier), all fine
        return '';
    }
}
