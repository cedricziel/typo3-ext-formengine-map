<?php

namespace CedricZiel\FormEngine\Map\Controller;

use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class GeocodingController
{
    const API_URL = 'https://maps.googleapis.com/maps/api/geocode/json?';

    /**
     * @param array              $ajaxParameters
     * @param AjaxRequestHandler $ajaxRequestHandler
     */
    public function geocode(array $ajaxParameters, AjaxRequestHandler $ajaxRequestHandler)
    {
        /** @var ServerRequest $request */
        $request = $ajaxParameters['request'];
        $address = $request->getQueryParams()['address'];
        $queryData = http_build_query(
            [
                'address' => $address,
            ]
        );

        $report = [];
        $url = static::API_URL.$queryData;
        $result = GeneralUtility::getUrl($url, 0, false, $report);

        $ajaxRequestHandler->setContentFormat('application/json');
        $ajaxRequestHandler->setContent(['foo' => $result]);
    }
}
