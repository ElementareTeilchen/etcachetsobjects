<?php

namespace ElementareTeilchen\Etcachetsobjects\UserFunc\Cache;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Context\Context;

/**
 * called from TS as caching layer for TS-Objects
 * used the Transient Backend, which means cache is only used within current page call
 * the TS-object must be called at least two times for this to make sense
 */
class TransientBackendCacheHandler extends AbstractCacheHandler
{
    public function __construct(
        #[Autowire(service: 'cache.etcachetsobjects_transient')]
        FrontendInterface $cache,
        Context $context
    ) {
        parent::__construct($cache, $context);
    }

    public function handle(string $content, array $conf, ServerRequestInterface $request): string
    {
        $cacheIdentifier = $this->createCacheIdentifier($conf);
        return $this->checkCache($conf, $cacheIdentifier);
    }
}
