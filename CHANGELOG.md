# Changelog

## 9.0.0

- Compatibility with TYPO3 v13 and drop support for TYPO3 v11 and v12
- [DEPRECATION] Rework class structure and deprecate some `userFunc` method calls:
  - `ElementareTeilchen\Etcachetsobjects\TypoScriptCache->handleElement`

    Should be replaced with `ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\DatabaseBackendCacheHandler->handle`
  - `ElementareTeilchen\Etcachetsobjects\TypoScriptCache->databaseBackend`

    Should be replaced with `ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\DatabaseBackendCacheHandler->handle`
  - `ElementareTeilchen\Etcachetsobjects\TypoScriptCache->transientBackend`

    Should be replaced with `ElementareTeilchen\Etcachetsobjects\UserFunc\Cache\TransientBackendCacheHandler->handle`
  - `ElementareTeilchen\Etcachetsobjects\MenuVariantCheck->levelGroupIdentifier`

    Should be replaced with `ElementareTeilchen\Etcachetsobjects\UserFunc\MenuVariantCheck->levelGroupIdentifier`

  The functionality of the replacement methods is identical to the old methods, so nothing else should need to be changed. For backwards compatibility, the old methods still work, but are scheduled to be removed at some point in the future.

## 8.1.1

- Improved TYPO3 v12 compatibility of `TypoScriptCache` class

## 8.1.0

- Compatibility with TYPO3 v12

## 8.0.0

- Compatibility with TYPO3 v11 and drop support for TYPO3 v10

## 7.0.3

- Do not cache when `includeHiddenPages` is set

## 7.0.2

- [SECURITY] Bumped required TYPO3 version to 10.4.32 to ensure security fixes
- Do not cache when in frontend preview mode

## 7.0.1

- Improve Readme

## 7.0.0

- [BREAKING] Removed domain based cache invalidation mode
- Added site based cache invalidation mode using the TYPO3 Site API
- Updated `TypoScriptCache` to use `SiteFinder` for generating site-specific cache identifiers and tags
- Added PSR-14 event `CollectCacheTagsToBeClearedEvent` allowing other extensions to inject additional cache tags for invalidation

## 6.0.5

- Added `extension-key` to `composer.json`

## 6.0.4

- Fix version number in `ext_emconf.php`

## 6.0.3

- Internal cleanup and dependency updates

## 6.0.2

- Improved handling of Mount Points when generating cache identifiers

## 6.0.1

- Raised requirements to avoid insecure TYPO3 versions

## 6.0.0

- Compatibility with TYPO3 9 and 10 and drop support for TYPO3 8

## 5.0.3

- Added validation for cache tags before flushing

## 5.0.2

- Corrected extension settings retrieval in `DataHandler`
- Fix dependencies in `ext_emconf.php`

## 5.0.1

- Documentation improvements in `README.md`

## 5.0.0

- Renamed extension from `et_cachetsobjects` to `etcachetsobjects`
- Updated namespace to `ElementareTeilchen\Etcachetsobjects`

## 4.0.0

- Compatibility with TYPO3 8 and drop support for TYPO3 6 and 7
- Added `composer.json` for composer support
- Implemented PSR-4 autoloading
- Add DB cache configuration to `pages` group, so flushing `pages` caches also flushes our caches

## 3.0.0

- Introduced "Transient Backend" cache handler for same-request caching
- Add possibility to modify the cache identifier via TypoScript configuration

## 2.0.0

- Compatibility with TYPO3 6.2 and 7 LTS and drop support for TYPO3 4
- Introduced `ElementareTeilchen\EtCachetsobjects` namespace

## 1.0.0

- Initial version
