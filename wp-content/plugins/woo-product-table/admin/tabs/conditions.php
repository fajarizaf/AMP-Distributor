<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_table_shorting">Sorting/Order</label>
    <select data-name='sort' id="wpt_table_shorting" class="wpt_fullwidth" >
        <option value="">None for Random</option>
        <option value="ASC">ASCENDING</option>
        <option value="DESC">DESCENDING</option>
    </select>
</div>

<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_table_sort_order_by">Order By</label>
    <select data-name='sort_order_by' id="wpt_table_sort_order_by" class="wpt_fullwidth" >
        <option value="none">None</option>
        <option value="ID">ID</option>
        <option value="author">Author</option>
        <option value="title" selected="selected">Product Title</option>
        <option value="name">Name</option>
        <option value="type">Type</option>
        <option value="date">Date</option>
        <option value="modified">Modified</option>
        <option value="parent">Parent</option>
        <option value="rand">Rand</option>
        <option value="comment_count">Reviews/Comment Count</option>
        <option value="relevance">Relevance</option> 
    </select>
</div>




<div class="wpt_column">
    <label class="wpt_label" for="wpt_product_min_price">Set Minimum Price</label>
    <input data-name='min_price' id="wpt_product_min_price" class="wpt_fullwidth wpt_data_filed_atts" type="number"  name="wpt_form_array[wpt_product_min_price]" pattern="[0-9]*">
</div>
<div class="wpt_column">
    <label class="wpt_label" for="wpt_product_max_price">Set Maximum Price</label>
    <input data-name='max_price' id="wpt_product_max_price" class="wpt_fullwidth wpt_data_filed_atts" type="number"  name="wpt_form_array[wpt_product_max_price]" pattern="[0-9]*">
</div>
<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_description_length">Set Description Length</label>
    <input data-name='description_length' id="wpt_description_length" class="wpt_fullwidth" type="number" max="278" min="50"  name="wpt_form_array[wpt_description_length]"" pattern="[0-9]*">
    <p>Min: 50, Max: 278</p>
</div>

<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_table_only_stock">Only Stock Product</label>
    <select data-name='only_stock' id="wpt_table_only_stock" class="wpt_fullwidth" >
        <option value="0" selected="">Default (All Product)</option>
        <option value="1">Yes</option>
    </select>
</div>


<div class="wpt_column wpt_not_available">
    <label class="wpt_label" for="wpt_posts_per_page">Post Limit/Maximum Post Quantity</label>
    <input id="wpt_posts_per_page" class="wpt_fullwidth" type="number"  name="" pattern="[0-9]*" placeholder="Eg: 50 (for display 50 products" value="50">
</div>

