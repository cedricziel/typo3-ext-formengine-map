<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'FormEngine map node type',
    'description'      => '',
    'category'         => 'misc',
    'version'          => '0.0.1',
    'state'            => 'dev',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearcacheonload' => true,
    'author'           => 'Cedric Ziel',
    'author_email'     => 'cedric@cedric-ziel.com',
    'author_company'   => '',
    'constraints'      => [
        'depends'   => [
            'typo3' => '7.6.0-7.6.99',
        ],
        'conflicts' => [
        ],
        'suggests'  => [
        ],
    ],
    'autoload'         => [
        "psr-4" => [
            "CedricZiel\\FormEngine\\Map\\" => "Classes",
        ],
    ],
];
