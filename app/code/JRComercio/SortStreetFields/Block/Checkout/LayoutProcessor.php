<?php

namespace JRComercio\SortStreetFields\Block\Checkout;

class LayoutProcessor {

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ){
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street'] = [
            'component' => 'Magento_Ui/js/form/components/group',
            //'label' => __('Street Address'), Remove label principal
            'required' => false,
            'dataScope' => 'shippingAddress.street',
            'provider' => 'checkoutProvider',
            'sortOrder' => 50,
            'type' => 'group',
            'additionalClasses' => 'street',
            'children' => [
                [
                    'label' => __('Street Address'), /* Endereço */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 1,
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '0',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255],
                ],
                [
                    'label' => __('Street Address 1'), /* Número */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 2,
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '1',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255, "validate-number" => true],
                ],
                [
                    'label' => __('Street Address 2'), /* Complemento */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 3,
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '3',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 0, "max_text_length" => 255],
                ],
                [
                    'label' => __('Street Address 3'), /* Bairro */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 4,
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '2',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => false, "min_text_len‌​gth" => 1, "max_text_length" => 10],
                ],
            ]
        ];

        // Ordena campos do boleto bradesco.
        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]["billing-step"]["children"]
        ["payment"]["children"]["payments-list"]["children"]["bradesco_boleto-form"]["children"]
        ["form-fields"]["children"]["street"] = [
            'component' => 'Magento_Ui/js/form/components/group',
            //'label' => __('Street Address'), Remove label principal
            'required' => false,
            'dataScope' => 'billingAddressbradesco_boleto.street',
            'provider' => 'checkoutProvider',
            'sortOrder' => 40,
            'type' => 'group',
            'additionalClasses' => 'street',
            'children' => [
                [
                    'label' => __('Street Address'), /* Endereço */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 1,
                    'config' => [
                        'customScope' => 'billingAddressbradesco_boleto',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '0',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255],
                ],
                [
                    'label' => __('Street Address 1'), /* Número */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 2,
                    'config' => [
                        'customScope' => 'billingAddressbradesco_boleto',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '1',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255, "validate-number" => true],
                ],
                [
                    'label' => __('Street Address 2'), /* Bairro */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 3,
                    'config' => [
                        'customScope' => 'billingAddressbradesco_boleto',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '3',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 0, "max_text_length" => 255],
                ],
                [
                    'label' => __('Street Address 3'), /* Complemento */
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'sortOrder' => 4,
                    'config' => [
                        'customScope' => 'billingAddressbradesco_boleto',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '2',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => false, "min_text_len‌​gth" => 1, "max_text_length" => 10],
                ],
            ]
        ];

        return $jsLayout;
    }
}
