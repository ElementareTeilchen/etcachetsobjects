etcachetsobjects: Caching possibility for expensive TypoScript Objects like menus
==================================================================================

## Idea behind

On bigger pages you have quite often big menus with lots of pages and levels (like responsive menus or flyout menus)
The rendering of these menus is quite heavy and is normally done on every rendered page
If you do not need to mark active or current pages or do this on browser side via Javascript, then the menu might be idential on all / several pages.
Perfect to be cached!

Currently we use it only for menus, other TypoScript objects should work, but probably the cache invalidation needs refinement then

HINT FOR DEBUGGING: we activate caching only if no FE user is logged in. Every BE user gets her own individual cache.

## Usage example 1: cache (part of) menu, which is identical on different pages

    lib.menu.sector = HMENU
    lib.menu.sector {
        [...]
    }
    // only use caching if no fe_user is logged in, else just stay with original TS
    lib.menu.sector_cached < lib.menu.sector
    [loginUser('*') == false]
        lib.menu.sector_cached >
        lib.menu.sector_cached = USER
        lib.menu.sector_cached {
            userFunc = ElementareTeilchen\Etcachetsobjects\TypoScriptCache->databaseBackend
            conf < lib.menu.sector
            cacheTime = 0 // "0" means unlimited liftime, cleared via backend saving hook on page changes
            //here we can set parameter needed for creating different cache entries
            additionalUniqueCacheParameters = COA
            additionalUniqueCacheParameters {
                10 = TEXT
                10.value = 0
                10.override.data = GP:L
    
                15 = TEXT
                15.value = 0
                15.override.data = GP:contrast
     
                20 = TEXT
                20.value = {$theme.pages.sectorstart_id}
            }
        }
    [global]

## Usage example 2: cache (part of) menu, which is identical on different pages, BUT should be different again on deeper levels
That one is used when you have a very big and deep page tree. On a certain level you don't want to flyout anymore, but show subpages only for the current page

    lib.menu.sector = HMENU
    lib.menu.sector {
        [...]
    }
    [loginUser('*') == false]
        lib.menu.sector_cached >
        lib.menu.sector_cached = USER
        lib.menu.sector_cached {
            userFunc = ElementareTeilchen\Etcachetsobjects\TypoScriptCache->databaseBackend
            conf < lib.menu.sector
            cacheTime = 0 // "0" means unlimited liftime, cleared via backend saving hook on page changes
            //here we can set parameter needed for creating different cache entries
            additionalUniqueCacheParameters = COA
            additionalUniqueCacheParameters {
                10 = TEXT
                10.value = 0
                10.override.data = GP:L
    
                15 = TEXT
                15.value = 0
                15.override.data = GP:contrast
    
                20 = TEXT
                20.value = {$theme.pages.sectorstart_id}
    
                // special handling because of level 5/6
                // if on level 4 and page has subpages or if on level 5 and deeper we have individual menus
                30 = USER
                30.userFunc = ElementareTeilchen\Etcachetsobjects\MenuVariantCheck->levelGroupIdentifier
                30.sectorstartId = {$theme.pages.sectorstart_id}
                30.individualMenusComingAtLevel = 4
            }
    
            // no ContentObject like COA, TEXT needed, we just want the configuration value
            additionalTags {
                10 = sector_{$theme.pages.sectorstart_id}
                #20 =
            }
        }
    [global]

## Usage example 3: cache expensive lib. object which is used several times on same page

    lib.pageRootlineCategoryId = CONTENT
    lib.pageRootlineCategoryId {
        [...]
    }

    lib.pageRootlineCategoryId_cached = USER
    lib.pageRootlineCategoryId_cached {
        userFunc = ElementareTeilchen\Etcachetsobjects\TypoScriptCache->transientBackend
        conf < lib.pageRootlineCategoryId
    }
    
    // then just replace _lib.pageRootlineCategoryId_ with _lib.pageRootlineCategoryId_cached_ whereever you use it.

## Cache Invalidation

In the Extension Manager you can define which variant of invalidation you need.


