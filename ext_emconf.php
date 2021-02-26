<?php

########################################################################
# Extension Manager/Repository config file for ext: "etcachetsobjects"
#
# Auto generated 19-06-2008 13:13
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Cache results of expensive TypoScript Objects',
    'description' => 'some TypoScript Objects (especially menues) are quite expensive to render, but might be the same on different pages, thus caching that output is useful. Several variants of cache invalidation available.',
    'category' => 'plugin',
    'shy' => 0,
    'version' => '6.0.4',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearcacheonload' => 0,
    'lockType' => '',
    'author' => 'Franz Kugelmann',
    'author_email' => 'franz.kugelmann@elementare-teilchen.de',
    'author_company' => 'http://www.elementare-teilchen.de',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'autoload' =>
        array (
            'psr-4' =>
                array (
                    'ElementareTeilchen\\Etcachetsobjects\\' => 'Classes/',
                ),
        ),
    'constraints' => array(
        'depends' => array(
            'typo3' => '9.5.24-10.4.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
