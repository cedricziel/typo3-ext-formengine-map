<?php

$mapColumn = [
    'tx_formenginemap_address' => [
        'exclude' => 0,
        'label'   => 'Map',
        'config'  => [
            'type'       => 'text',
            'renderType' => 'cz_map',
            'cols'       => 40,
            'rows'       => 15,
            'eval'       => 'trim',
            'size'       => 50,
            'default'    => '',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $mapColumn);

$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['formengine_map']);
$attachToAllTCATypes = $extensionConfiguration['enableTtContentField'];

if (true === (bool) $attachToAllTCATypes) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;Map,tx_formenginemap_address'
    );
}

unset($attachToAllTCATypes);
unset($extensionConfiguration);
