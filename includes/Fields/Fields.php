<?php

namespace PagBank_Split_Payment\Fields;

use Carbon_Fields\Field;

defined( 'ABSPATH' ) || exit;

abstract class Fields {
    protected function fields() {
        return [];
    }

    public function carbon_fields() {
        $fields = $this->fields();

        return array_map(
            function ( $key ) use ( $fields ) {
                $field = $fields[ $key ];
                $fieldCarbon = Field::make(
                    $field[ 'type' ],
                    $key,
                    $field[ 'label' ],
                );

                if ( ! empty( $field[ 'attributes' ] ) ) {
                    $fieldCarbon->set_attributes( $field[ 'attributes' ]);
                }

                if ( isset( $field[ 'required' ] ) && $field[ 'required' ] ) {
                    $fieldCarbon->set_required();
                }

                if ( ! empty( $field[ 'options' ] ) ) {
                    $fieldCarbon->set_options( $field[ 'options' ] );
                }

                if ( ! empty( $field[ 'condition' ] ) ) {
                    $fieldCarbon->set_conditional_logic( $field[ 'condition' ] );
                }

                if ( ! empty( $field[ 'width' ] ) ) {
                    $fieldCarbon->set_width( $field[ 'width' ] );
                }

                if ( ! empty( $field[ 'min' ] ) && 'association' === $field[ 'type' ] ) {
                    $fieldCarbon->set_min( $field[ 'min' ] );
                }

                if ( ! empty( $field[ 'max' ] ) && 'association' === $field[ 'type' ] ) {
                    $fieldCarbon->set_max( $field[ 'max' ] );
                }

                if ( ! empty( $field[ 'types' ] ) && 'association' === $field[ 'type' ] ) {
                    $fieldCarbon->set_types( $field[ 'types' ] );
                }

                return $fieldCarbon;
            },
            array_keys( $fields )
        );
    }
}
