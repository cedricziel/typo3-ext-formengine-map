<?php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['cz_map'] = [
    'nodeName' => 'cz_map',
    'priority' => 40,
    'class'    => \CedricZiel\FormEngine\Map\Form\Element\MapElement::class,
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'cz_maps_geocode_handler',
    \CedricZiel\FormEngine\Map\Controller\GeocodingController::class . '->geocode',
    true
);
