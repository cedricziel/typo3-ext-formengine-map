<?php

namespace CedricZiel\FormEngine\Map\Controller;

use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

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
        $address = $request->getQueryParams()['query'];
        $queryData = http_build_query(
            [
                'key'     => $this->getApiKey(),
                'address' => $address,
            ]
        );

        $report = [];
        $url = static::API_URL.$queryData;

        $result = GeneralUtility::getUrl($url, 0, false, $report);

        $ajaxRequestHandler->setContentFormat('application/json');
        $ajaxRequestHandler->setContent(['data' => $result]);
    }

    /**
     * Retreives the API key from the extension configuration.
     *
     * @return string
     */
    protected function getApiKey()
    {
        /** @var ConfigurationUtility $configurationUtility */
        $configurationUtility = $this->getObjectManager()->get(ConfigurationUtility::class);
        $extensionConfiguration = $configurationUtility->getCurrentConfiguration('formengine_map');
        return $extensionConfiguration['googleMapsGeocodingApiKey']['value'];
    }

    /**
     * @return ObjectManagerInterface
     */
    protected function getObjectManager() {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
