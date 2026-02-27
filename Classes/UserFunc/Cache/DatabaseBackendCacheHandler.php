<?php

namespace ElementareTeilchen\Etcachetsobjects\UserFunc\Cache;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 *  called from TS as caching layer for TS-Objects
 *  used the Database Backend
 */
class DatabaseBackendCacheHandler extends AbstractCacheHandler
{
    public function __construct(
        #[Autowire(service: 'cache.etcachetsobjects_db')]
        FrontendInterface $cache,
        Context $context,
        protected readonly SiteFinder $siteFinder,
    ) {
        parent::__construct($cache, $context);
    }

    public function handle(string $content, array $conf, ServerRequestInterface $request): string
    {
        $uniqueCacheIdentifiers = [];

        // each BE user gets own cache because of access restricted pages
        // on big sites this makes sense because if editor works on content
        // she has to wait several seconds each time she checks the frontend mainly because of the menu
        if ($this->context->getPropertyFromAspect('backend.user', 'isLoggedIn')
            && $this->context->getPropertyFromAspect('backend.user', 'id', 0) !== 0
        ) {
            $uniqueCacheIdentifiers['beUser'] = $this->context->getPropertyFromAspect('backend.user', 'id');
        }

        $cacheTags = [];

        $site = $this->siteFinder->getSiteByPageId(
            $request->getAttribute('frontend.page.information')->getId()
        );
        if ($site instanceof Site) {
            // MIND: if you change identifier here, do also in hook for cache clearing
            $uniqueCacheIdentifiers['siteIdentifier'] = $site->getIdentifier();
            $cacheTags[] = $site->getIdentifier();
        }

        $cacheIdentifier = $this->createCacheIdentifier($conf, $uniqueCacheIdentifiers);

        return $this->checkCache($conf, $cacheIdentifier, $cacheTags);
    }
}
