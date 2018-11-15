/* 
 * For any important
 * As jQuery already included to Admin Section of WordPress, So jQuery is not added again
 * Already Checked, jQuery working here properly
 * 
 * onclick="document.getElementById('wpt_form_submition_status').value = '0';
 document.getElementById('wpt_configuration_form').submit();"
 * 
 *  onclick="document.getElementById('wpt_form_submition_status').value = '1';
 document.getElementById('wpt_configuration_form').submit();"
 * @since 1.0.0
 * @update 1.0.3
 */


(function($) {
    $(document).ready(function() {
        //For select, used select2 addons of jquery
        $('.wpt_wrap select,select#wpt_product_ids').select2();
        
        //code for Sortable
        $( "#wpt_column_sortable" ).sortable({
            handle:'.handle',
            beforeStop: function(e){
                e.preventDefault();
                alert("Column Moving is not posible in Free Version.");
                return false;
            }
        });
        $( "#wpt_column_sortable" ).disableSelection();
        
        $('.wpt_not_available').click(function(e){
            e.preventDefault();
            alert('Not available for Free Version');
            return false;
        });

    });
})(jQuery);