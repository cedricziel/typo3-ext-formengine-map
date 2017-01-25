<?php

namespace CedricZiel\FormEngine\Map\Tests\Unit\Form\Element;

use CedricZiel\FormEngine\Map\Form\Element\MapElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Tests\BaseTestCase;
use TYPO3\CMS\Core\Tests\UnitTestCase;

class MapElementTest extends BaseTestCase
{
    /**
     * @test
     */
    public function renderReturnsCorrectlyFormattedTemplate()
    {
        $data = [
            'parameterArray' => [
                'tableName'       => 'table_foo',
                'fieldName'       => 'field_bar',
                'fieldConf'       => [
                    'config' => [
                        'type'    => 'input',
                        'dbType'  => 'datetime',
                        'eval'    => 'datetime',
                        'default' => '0000-00-00 00:00:00',
                    ],
                ],
                'itemFormElValue' => '',
            ],
        ];
        /** @var NodeFactory $nodeFactoryProphecy */
        $nodeFactoryProphecy = $this->prophesize(NodeFactory::class)->reveal();
        $subject = new MapElement($nodeFactoryProphecy, $data);
        $result = $subject->render();
        $this->assertContains('<input type="hidden" name="" value="" />', $result['html']);
    }
}
