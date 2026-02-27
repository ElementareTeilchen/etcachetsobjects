<?php

namespace ElementareTeilchen\Etcachetsobjects\UserFunc\Cache;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

abstract class AbstractCacheHandler
{
    protected ContentObjectRenderer $cObj;

    public function __construct(
        protected readonly FrontendInterface $cache,
        protected readonly Context $context
    ) {}

    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }

    abstract public function handle(string $content, array $conf, ServerRequestInterface $request): string;

    protected function createCacheIdentifier(array $conf, array $uniqueCacheIdentifiers = []): string
    {
        // additionalUniqueCacheParameters via TypoScript
        if (isset($conf['additionalUniqueCacheParameters']) && is_array($conf['additionalUniqueCacheParameters.'])) {
            $uniqueCacheIdentifiers['typoScript'] = $this->cObj->getContentObject($conf['additionalUniqueCacheParameters'])->render($conf['additionalUniqueCacheParameters.']);
        }

        return sha1(serialize($uniqueCacheIdentifiers) . serialize($conf));
    }

    /**
     * check if cache already exists
     * fetch content if there or
     * create, store in cache and return content
     */
    protected function checkCache(
        array $conf,
        string $cacheIdentifier,
        array $cacheTags = []
    ): string
    {
        if (false === ($content = $this->cache->get($cacheIdentifier))) {
            $content = $this->cObj->getContentObject($conf['conf'])->render($conf['conf.']);

            // make sure we do not cache elements when in preview mode
            // i.e. hidden pages are shown in menu
            if ($this->context->getPropertyFromAspect('frontend.preview', 'isPreview')
                || $this->context->getPropertyFromAspect('visibility', 'includeHiddenPages')
            ) {
                return $content;
            }

            // additionalTags via TypoScript
            if (isset($conf['additionalTags.']) && is_array($conf['additionalTags.'])) {
                foreach ($conf['additionalTags.'] as $tag) {
                    $cacheTags[] = $tag;
                }
            }

            $this->cache->set(
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
