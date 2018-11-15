<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_table_mobile_responsive">Mobile Responsive</label>
    <select id="wpt_table_mobile_responsive" class="wpt_fullwidth wpt_data_filed_atts" >
        <option value="1" selected="">Default (Yes Responsive)</option>
        <option value="0">No Responsive</option>
    </select>
    <p style="padding-top: 0;margin-top: 0;">Note: the [Mobile Responsive] feature is not available for dataTable Template</p>
</div>

<?php
$colums_disable_array = WOO_Product_Table::$colums_disable_array;
unset($colums_disable_array['thumbnails']);
unset($colums_disable_array['description']);
$columns_array = WOO_Product_Table::$columns_array;
unset($columns_array['product_title']);
unset($columns_array['price']);
unset($columns_array['action']);
unset($columns_array['check']);
/*
$colums_disable_array = array(
    'serial_number',
    //'thumbnails',
    //'description',
    'tags',
    'weight',
    'length',
    'width',
    'height',
);

//var_dump( WOO_Product_Table::getOption() );
$columns_array = array(
    'serial_number' => 'SL',
    
    'thumbnails'    => 'Thumbnails',
    //'product_title' => 'Product Title',
    'description'   =>  'Description',
    'category'      => 'Category',
    'tags'          => 'Tags',
    'sku'           => 'SKU',
    'weight'        => 'Weight(kg)',
    'length'        => 'Length(cm)',
    'width'         => 'Width(cm)',
    'height'        => 'Height(cm)',
    'rating'        => 'Rating',
    'stock'         => 'Stock',
    //'price'         => 'Price',
    //'quantity'      => 'Quantity',
    'total'         => 'Total Price',
    //'action'        => 'Action',
    //'check'         => 'Check',
);
*/
?>
<ul id="wpt_keyword_hide_mobile" class="wpt_not_available">
    <h1 style="color: #D01040;">Hide On Mobile</h1>
    <p style="padding: 0;margin: 0;">Pleach check you column to hide from Mobile. For all type Table(Responsive or Non-Responsive).</p>
    <hr>
        <?php
    foreach( $columns_array as $keyword => $title ){
        $enabled_class = 'enabled';
        $checked_attribute = ' checked="checked"';
        if( !in_array( $keyword, $colums_disable_array ) ){
            $enabled_class = $checked_attribute = '';
        }
    ?>
    <li class="hide_on_mobile_permits <?php echo $enabled_class; ?> column_keyword_<?php echo $keyword; ?>">
        
        <div class="wpt_mobile_hide_keyword">
            <b  data-column_title="<?php echo $title; ?>" data-keyword="<?php echo $keyword; ?>" class="mobile_issue_field <?php echo $keyword; ?>" type="text" ><?php echo $title; ?></b>
        </div>
        <span title="Move Handle" class="handle checkbox_handle">
            <input title="Active Inactive Column" class="checkbox_handle_input <?php echo $enabled_class; ?>" type="checkbox" data-column_keyword="<?php echo $keyword; ?>" <?php echo $checked_attribute; ?>>
        </span>
    </li>
    <?php

    }
    ?>

</ul>
