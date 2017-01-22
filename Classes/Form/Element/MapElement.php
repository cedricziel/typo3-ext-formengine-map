<?php

namespace CedricZiel\FormEngine\Map\Form\Element;

use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class MapElement extends InputTextElement
{
    const GOOGLE_STATICMAP_URL = 'https://maps.googleapis.com/maps/api/staticmap?';

    /**
     * Handler for single nodes
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $table = $this->data['tableName'];
        $fieldName = $this->data['fieldName'];
        $row = $this->data['databaseRow'];
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();
        $isDateField = false;

        $config = $parameterArray['fieldConf']['config'];
        $specConf = BackendUtility::getSpecConfParts($parameterArray['fieldConf']['defaultExtras']);
        $size = MathUtility::forceIntegerInRange(
            $config['size'] ?: $this->defaultInputWidth,
            $this->minimumInputWidth,
            $this->maxInputWidth
        );
        $evalList = GeneralUtility::trimExplode(',', $config['eval'], true);
        $attributes = [];
        $attributes['class'] = 'form-control';

        $currentValue = json_decode($parameterArray['itemFormElValue']);
        if ($currentValue === null) {
            $currentValue = [];
            $attributes['placeholder'] = 'Please enter an address or place.';
        } else {
            $attributes['placeholder'] = $currentValue->formatted_address;
        }

        // Build the attribute string
        $attributeString = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeString .= ' '.$attributeName.'="'.htmlspecialchars($attributeValue).'"';
        }

        $escapedValue = htmlspecialchars($parameterArray['itemFormElValue']);
        $hiddenField = '<input type="hidden" name="'.$parameterArray['itemFormElName'].'" value="'.$escapedValue.'" />';
        $visibleField = '<input type="text"'.$attributeString.'/>';

        $view = $this->getFluidStandaloneView();
        $view->assignMultiple(
            [
                'apiKey'       => $this->getApiKey(),
                'currentValue' => $currentValue,
                'currentValueJson' => json_encode($currentValue),
                'hidden'       => $hiddenField,
                'input'        => $visibleField,
                'mode'         => $this->getMode(),
                'staticMapUrl' => $this->getStaticMapsUrl($currentValue),
            ]
        );
        $html = $view->render();

        // Wrap wizards.
        $html = $this->renderWizards(
            [$html],
            $config['wizards'],
            $table,
            $row,
            $fieldName,
            $parameterArray,
            $parameterArray['itemFormElName'],
            $specConf
        );

        $resultArray['requireJsModules'] = ['TYPO3/CMS/FormengineMap/MapHandler'];

        // Add a wrapper to remain maximum width
        $width = (int)$this->formMaxWidth($size);
        $html = '<div class="form-control-wrap"'.($width ? ' style="max-width: '.$width.'px"' : '').'>'.$html.'</div>';
        $resultArray['html'] = $html;

        return $resultArray;
    }

    /**
     * @return StandaloneView
     */
    private function getFluidStandaloneView()
    {
        $view = new StandaloneView();
        $view->setTemplateRootPaths(
            [10 => GeneralUtility::getFileAbsFileName('EXT:formengine_map/Resources/Private/Templates/')]
        );
        $view->setTemplate('MapElement.html');

        return $view;
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
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return string
     */
    protected function getMode()
    {
        /** @var ConfigurationUtility $configurationUtility */
        $configurationUtility = $this->getObjectManager()->get(ConfigurationUtility::class);
        $extensionConfiguration = $configurationUtility->getCurrentConfiguration('formengine_map');

        return $extensionConfiguration['mode']['value'];
    }

    /**
     * Computes a static maps url.
     *
     * @param array $currentValue
     *
     * @return string|null
     */
    protected function getStaticMapsUrl($currentValue = null)
    {
        $formattedAddress = ObjectAccess::getPropertyPath($currentValue, 'formatted_address');

        if ($formattedAddress === null) {
            return '';
        }

        $parameters = http_build_query(
            [
                'key'     => $this->getApiKey(),
                'size'    => '1000x200',
                'zoom'    => 14,
                'center'  => $formattedAddress,
                'markers' => $formattedAddress,
            ]
        );

        return static::GOOGLE_STATICMAP_URL.$parameters;
    }
}
