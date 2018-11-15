<?php
$wpt_style_file_selection_options = WOO_Product_Table::$style_form_options;

    /**
     * To Get Category List of WooCommerce
     * @since 1.0.0 -10
     */
    $args = array(
        'hide_empty' => true,
        'orderby' => 'count',
        'order' => 'DESC',
    );

    //WooCommerce Product Category Object as Array
    $wpt_product_cat_object = get_terms('product_cat', $args);
    //var_dump($wpt_product_cat_object);
?>


<div class="wpt_column">
    <label class="wpt_label" for="wpt_product_slugs">Category Includes <small>(Click to choose Categories)</small></label>
    <select data-name="product_cat_ids" id="wpt_product_ids" class="wpt_fullwidth wpt_data_filed_atts" multiple>
        <?php
        foreach ($wpt_product_cat_object as $category) {
            echo "<option value='{$category->term_id}' " . ( is_array($wpt_product_ids) && in_array($category->term_id, $wpt_product_ids) ? 'selected' : false ) . ">{$category->name} - {$category->slug} ({$category->count})</option>";
        }
        ?>
    </select>
</div>
<div class="wpt_column wpt_not_available">
    <label class="wpt_label">Product ID Exclude (Separate with comma)</label>
    <input data-name="post_exclude" class="" type="text" placeholder="Example: 1,2,3,4">
</div>
<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_product_slugs">Category Exclude <small>(Click to choose Categories)</small></label>
    <select data-name="cat_explude" id="wpt_product_ids" class="wpt_fullwidth" multiple>
        <?php
        foreach ($wpt_product_cat_object as $category) {
            echo "<option value='{$category->term_id}' " . ( is_array($wpt_product_ids) && in_array($category->term_id, $wpt_product_ids) ? 'selected' : false ) . ">{$category->name} - {$category->slug} ({$category->count})</option>";
        }
        ?>
    </select>
</div>


<div class="wpt_column">
    <label class="wpt_label" for="wpt_style_file_selection">Select Template</label>
    <select data-name="template" id="wpt_style_file_selection"  class="wpt_fullwidth wpt_data_filed_atts" >
        <?php
        foreach ($wpt_style_file_selection_options as $key => $value) {
            echo "<option value='$key'>$value</option>";
        }
        ?>
    </select>
</div>


<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for='wpt_table_table_class'>Set a Class name for Table</label>
    <input class="" data-name="table_class" type="text" placeholder="Product's Table Class Name (Optional)" id='wpt_table_table_class'>
</div>
<div class="wpt_column">
    <label class="wpt_label" for="wpt_table_add_to_cart_text">(Add to cart) Text</label>
    <input class="wpt_data_filed_atts" data-name="add_to_cart_text" type="text" placeholder="Example: Buy" id="wpt_table_add_to_cart_text">
</div>
<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_table_add_to_cart_selected_text">(Add to cart) Text</label>
    <input class="" data-name="add_to_cart_selected_text" type="text" placeholder="Example: Add to cart Selected" id="wpt_table_add_to_cart_selected_text">
</div>

<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_table_check_uncheck_text">(All Check/Uncheck) Text</label>
    <input class="" data-name="check_uncheck_text" type="text" placeholder="Example: All Check/Uncheck" id="wpt_table_check_uncheck_text">
</div>

