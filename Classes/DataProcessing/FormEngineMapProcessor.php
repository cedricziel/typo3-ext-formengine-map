<?php

namespace CedricZiel\FormEngine\Map\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FormEngineMapProcessor implements DataProcessorInterface
{
    /**
     * Process content object data
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array                 $contentObjectConfiguration The configuration of Content Object
     * @param array                 $processorConfiguration The configuration of this processor
     * @param array                 $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     *
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        $viewVariable = $this->extractViewVariable($processorConfiguration);
        $fieldName = $processorConfiguration['field'];

        if (array_key_exists($fieldName, $processedData['data'])) {
            $processedData[$viewVariable] = json_decode($processedData['data'][$fieldName]);
        }

        return $processedData;
    }

    /**
     * @param array $processorConfiguration
     *
     * @return string
     */
    protected function extractViewVariable(array $processorConfiguration)
    {
        if (true === array_key_exists('as', $processorConfiguration ) && false === empty($processorConfiguration['as'])) {
            return $processorConfiguration['as'];
        }

        return 'tx_formmap_processed';
    }
}
