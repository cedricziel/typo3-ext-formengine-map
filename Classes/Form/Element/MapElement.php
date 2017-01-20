<?php

namespace CedricZiel\FormEngine\Map\Form\Element;

use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class MapElement extends InputTextElement
{
    /**
     * Handler for single nodes
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $inputBox = parent::render();

        $inputBox['requireJsModules'][] = 'TYPO3/CMS/FormengineMap/MapHandler';

        $view = $this->getFluidStandaloneView();
        $view->assign('input', $inputBox['html']);

        $inputBox['html'] = $view->render();

        return $inputBox;
    }

    /**
     * @return StandaloneView
     */
    private function getFluidStandaloneView()
    {
        $view = new StandaloneView();
        $view->setTemplateRootPaths([10 => GeneralUtility::getFileAbsFileName('EXT:formengine_map/Resources/Private/Templates/')]);
        $view->setTemplate('MapElement.html');

        return $view;
    }
}
