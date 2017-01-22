<?php

$mapColumn = [
    'tx_formmap_address' => [
        'exclude' => 0,
        'label'   => 'Map',
        'config'  => [
            'type'       => 'text',
            'renderType' => 'cz_map',
            'cols'       => 40,
            'rows'       => 15,
            'eval'       => 'trim',
            'size'       => 50,
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $mapColumn);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'tx_formmap_address');
