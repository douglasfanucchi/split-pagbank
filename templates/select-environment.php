<tr vlaign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $field_key ); ?>">
            <?php esc_html_e('Ambiente PagBank', 'pagbank-for-woocommerce'); ?>
        </label>
    </th>
    <td class="forminp">
        <select class="woocommerce_pagbank_credit_card_environment" id="<?php echo esc_attr( $field_key ); ?>" name="<?php echo esc_attr( $field_key ); ?>">
            <option <?php echo $value === "production" ? "selected" : "" ?> value="production">Produção</option>
            <option <?php echo $value === "sandbox" ? "selected" : "" ?> value="sandbox">Ambiente de testes</option>
        </select>
    </td>
</tr>
