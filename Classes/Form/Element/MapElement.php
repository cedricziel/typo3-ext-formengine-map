<?php

namespace CedricZiel\FormEngine\Map\Form\Element;

use CedricZiel\FormEngine\Map\Utility\StaticMaps;
use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class MapElement extends InputTextElement
{
    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @param NodeFactory $nodeFactory
     * @param array       $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);

        $this->view = $this->prepareView();
    }

    /**
     * Handler for single nodes
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();

        $config = $parameterArray['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange(
            $config['size'] ?: $this->defaultInputWidth,
            $this->minimumInputWidth,
            $this->maxInputWidth
        );

        $currentValue = json_decode($parameterArray['itemFormElValue']) ?: [];
        $attributes = [
            'class'       => 'form-control',
            'placeholder' => $this->preparePlaceholderAttribute($currentValue),
        ];

        $html = $this->view->renderSection(
            'FormElement',
            [
                'apiKey'           => StaticMaps::getApiKey(),
                'currentValue'     => $currentValue ? $currentValue : [],
                'currentValueJson' => json_encode($currentValue),
                'inputAttributes'  => $this->buildInputAttributes($attributes),
                'parameterArray'   => $parameterArray,
                'mode'             => $this->getMode(),
                'staticMapUrl'     => StaticMaps::getStaticMapsUrl($currentValue),
            ]
        );

        // Add a wrapper to remain maximum width
        $width = (int)$this->formMaxWidth($size);
        $resultArray['html'] = $this->view->renderSection('Wrapper', ['content' => $html, 'width' => $width]);
        $resultArray['requireJsModules'] = ['TYPO3/CMS/FormengineMap/MapHandler'];

        return $resultArray;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    protected function buildInputAttributes($attributes)
    {
        $attributeString = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeString .= ' '.$attributeName.'="'.htmlspecialchars($attributeValue).'"';
        }

        return $attributeString;
    }

    /**
     * @return string
     */
    protected function getMode()
    {
        /** @var ConfigurationUtility $configurationUtility */
        $configurationUtility = static::getObjectManager()->get(ConfigurationUtility::class);
        $extensionConfiguration = $configurationUtility->getCurrentConfiguration('formengine_map');

        return $extensionConfiguration['mode']['value'];
    }

    /**
     * @return ObjectManagerInterface
     */
    protected static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @param string $currentValue
     *
     * @return string
     */
    protected function preparePlaceholderAttribute($currentValue)
    {
        if ($currentValue === null || empty($currentValue)) {
            return 'Please enter an address or place.';
        } else {
            return $currentValue->formatted_address;
        }
    }


    /**
     * @return StandaloneView
     */
    protected function prepareView()
    {
        $view = new StandaloneView();
        $view->setTemplateRootPaths(
            [10 => GeneralUtility::getFileAbsFileName('EXT:formengine_map/Resources/Private/Templates/')]
        );
        $view->setTemplate('MapElement.html');

        return $view;
    }
}
