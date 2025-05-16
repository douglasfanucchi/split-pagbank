<?php

namespace PagBank_Split_Payment\Fields;

class ProductRecipient extends Fields
{
    protected function fields() {
        return apply_filters('fn_product_recipient_fields', [
            'user' => [
                'label' => 'Recebedor',
                'type' => 'association',
                'types' => [
                    ['type' => 'user']
                ],
                'min' => 1,
                'max' => 1,
                'width' => 50,
                'required' => true,
            ],
            'amount' => [
                'type' => 'text',
                'label' => 'Quantidade a Receber',
                'attributes' => [
                    'type' => 'number',
                    'step' => 0.01
                ],
                'width' => 50,
                'required' => true,
            ],
        ]);
    }
}
