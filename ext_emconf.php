<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Cache results of expensive TypoScript Objects',
    'description' => 'some TypoScript Objects (especially menues) are quite expensive to render, but might be the same on different pages, thus caching that output is useful. Several variants of cache invalidation available.',
    'category' => 'plugin',
    'version' => '7.0.2',
    'state' => 'stable',
    'createDirs' => '',
    'author' => 'Franz Kugelmann',
    'author_email' => 'franz.kugelmann@elementare-teilchen.de',
    'author_company' => 'http://www.elementare-teilchen.de',
    'autoload' =>
        array (
            'psr-4' =>
                array (
                    'ElementareTeilchen\\Etcachetsobjects\\' => 'Classes/',
                ),
        ),
    'constraints' => array(
        'depends' => array(
            'typo3' => '10.4.32-10.4.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
