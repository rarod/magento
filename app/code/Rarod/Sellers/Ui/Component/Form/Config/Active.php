<?php


namespace Rarod\Sellers\Ui\Component\Form\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Active implements OptionSourceInterface
{
    protected $options;

    public function toOptionArray()
    {

        $this->options = [
            [
                'label' => 'Desabilitado',
                'value' => '0'
            ],
            [
                'label' => 'Habilitado',
                'value' => '1'
            ]
        ];

        return $this->options;
    }
}