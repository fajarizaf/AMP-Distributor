<?php
$colums_disable_array = WOO_Product_Table::$colums_disable_array;
$columns_array = WOO_Product_Table::$columns_array;
$current_inactive = array(
    'tags',
    'sku',
    'weight',
    'length',
    'width',
    'height',
    'stock',
    'total',
    'check',
);
?>
<ul id="wpt_column_sortable">
    <?php
    foreach( $columns_array as $keyword => $title ){
        $enabled_class = 'enabled';
        $checked_attribute = ' checked="checked"';
        if( in_array( $keyword, $colums_disable_array ) ){
            $enabled_class = $checked_attribute = '';
        }
        $wpt_not_available = false;
        $handle = 'handle';
        $colum_data_input = 'colum_data_input';
        if( in_array( $keyword, $current_inactive ) ){
            $wpt_not_available = 'wpt_not_available';
            $handle = $colum_data_input = false;
        }
        
    ?>
    <li class="wpt_sortable_peritem <?php echo $enabled_class; ?> column_keyword_<?php echo $keyword; ?> <?php echo $wpt_not_available; ?>">
        <span title="Move Handle" class="<?php echo $handle; ?>"></span>
        <div class="wpt_shortable_data">
            <input  data-column_title="<?php echo $title; ?>" data-keyword="<?php echo $keyword; ?>" class="<?php echo $colum_data_input . ' ' . $keyword; ?>" type="text" value="<?php echo $title; ?>" >
        </div>
        <span title="Move Handle" class="handle checkbox_handle">
            <input title="Active Inactive Column" class="checkbox_handle_input <?php echo $enabled_class; ?>" type="checkbox" data-column_keyword="<?php echo $keyword; ?>" <?php echo $checked_attribute; ?>>
        </span>
    </li>
    <?php

    }
    ?>

</ul>
