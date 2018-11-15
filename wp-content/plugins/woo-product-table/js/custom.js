/* 
 * Only for Fronend Section
 * @since 1.0.0
 */


(function($) {
    $(document).ready(function() {
        
        $('body').on('click', '.wpt_product_table_wrapper .wpt_thumbnails img', function() {
        //$('.wpt_product_table_wrapper .wpt_thumbnails img').click(function() {
            var image_source, image_array_count, final_image_url, product_title;
            image_source = $(this).attr('srcset');
            image_source = image_source.split(' ');

            image_array_count = image_source.length - 2;
            final_image_url = image_source[image_array_count];
            product_title = $(this).closest('tr').data('title');
            console.log(product_title);
            var html = '<div id="wpt_thumbs_popup" class="wpt_thumbs_popup"><div class="wpt_popup_image_wrapper"><span title="Close" id="wpt_popup_close">x</span><h2 class="wpt_wrapper_title">' + product_title + '</h2><div class="wpt_thums_inside">';
            html += '<img class="wpt_popup_image" src="' + final_image_url + '">';
            html += '</div></div></div>';
            if ($('body').append(html)) {
                $('#wpt_thumbs_popup').fadeIn('slow');
                var height = $('#wpt_thumbs_popup').height();
                var height_wrapper = $('.wpt_popup_image_wrapper').innerHeight();
                var request_top = (height - height_wrapper) / 2;
                //$('.wpt_popup_image_wrapper').css('margin-top',height_wrapper + 'px');

            }
        });
        $('body').on('click', '#wpt_thumbs_popup span#wpt_popup_close', function() {
            //$.addClass('saiful');
            $('#wpt_thumbs_popup').remove();
        });


        /*********Ajax For Table Preview at Admin Section : Not activated yet *******
         
         $().change(function(){
         var site_url = '../wp-admin/admin-ajax.php';
         jQuery.ajax({
         type: 'POST',
         url: site_url,
         data: {
         action: 'my_action',
         },
         success: function(data){
         alert('Data Updated');
         $('#wpt_preview_table_box').html(data);
         },
         error: function(){
         alert('Failed');
         },
         });
         });
         
         //console.log(site_url);
         //var site_url = '../wp-admin/admin-ajax.php';
         $('.card.wpt_preview_cart').click(function(){
         jQuery.ajax({
         type: 'POST',
         url: site_url,
         data: {
         action: 'my_action',
         },
         success: function(data){
         alert('Data Updated');
         $('#wpt_preview_table_box').html(data);
         },
         error: function(){
         alert('Failed');
         },
         });
         });
         //************ Ajax Section End Here *******/
        $('a.button.wpt_woo_add_cart_button.outofstock_add_to_cart_button.disabled').click(function(e) {
            //$('body').on('click','a.outofstock_add_to_cart_button.button.disabled',function(e){    
            e.preventDefault();
            alert('Sorry! Out of Stock!');
            return false;
        });


        $('body').on('click', 'a.wpt_variation_product.single_add_to_cart_button.button.enabled, a.add_to_cart_button.wpt_woo_add_cart_button', function(e) {
            e.preventDefault();
            var thisButton = $(this);
            //Adding disable and Loading class
            thisButton.addClass('disabled');
            thisButton.addClass('loading');

            var target_action_id = $(this).data('product_id');
            var target_temp_number = $(this).closest('.wpt_action_' + target_action_id).data('temp_number');
            var tables_cart_message_box = '.tables_cart_message_box_' + target_temp_number; //$wpt_temporary_class
            var site_url = $('#table_id_' + target_temp_number).data('site_url');
            site_url = site_url + '/wp-admin/admin-ajax.php';
            var quantity = $('#table_id_' + target_temp_number + ' table#wpt_table .wpt_row_product_id_' + target_action_id + ' .wpt_quantity .quantity input.input-text.qty.text').val();
            if(!quantity){
                quantity = 1;
            }
            var get_data = $(this).attr('href') + '&quantity=' + quantity;//$(this).data('quantity');
            //console.log($('#table_id_' + target_temp_number + ' table#wpt_table .wpt_row_product_id_' + target_action_id + ' .wpt_quantity .quantity input').val());
            console.log(get_data);
            $.ajax({
                type: 'GET',
                url: site_url + get_data,
                data: {
                    action: 'wpt_ajax_add_to_cart',
                    variation: $(this).data('variation'),
                },
                success: function(data) {
                    $(tables_cart_message_box).html(data);
                    thisButton.removeClass('disabled');
                    thisButton.removeClass('loading');
                    thisButton.addClass('added');
                },
                error: function() {
                    alert('Failed - Unable to add by ajax');
                },
            });

        });


        $('body').on('click', 'a.wpt_variation_product.single_add_to_cart_button.button.disabled', function(e) {
            e.preventDefault();
            alert("Choose Variation First");
            return false;

        });
        //Alert of out of stock 

        $('body').on('click', 'a.wpt_woo_add_cart_button.button.disabled.loading', function(e) {
            e.preventDefault();
            alert("Adding in Progress");
            return false;

        });


        $('td.data_product_variations .wpt_varition_section').change(function() {
            var target_action_id = $(this).data('product_id');
            var temp_number = $(this).data('temp_number');
            var target_class = '.wpt_action_' + target_action_id;
            //Please choose right combination.//Message
            var targetRightCombinationMsg = $('#table_id_' + temp_number).data('right_combination_message');


            /**
             * Finally targetPriceSelectorTd has removed becuase we have creaed a new function
             * for targetting any TD of selected Table.
             * This function is targetTD(td_name)
             * @type @call;$
             */
            //var targetPriceSelectorTd = $('#table_id_' + temp_number + ' #price_value_id_' + target_action_id);

            var targetThumbs = $('#table_id_' + temp_number + ' #product_id_' + target_action_id + ' td.wpt_thumbnails img');
            var variations_data = $(this).closest(target_class).data('product_variations');
            var messageSelector = $(this).children('div.wpt_message');
            var addToCartSelector = $(this).parent('td.wpt_action').children('a.wpt_variation_product.single_add_to_cart_button');
            //Checkbox Selector
            var checkBoxSelector = $('.wpt_check_temp_' + temp_number + '_pr_' + target_action_id);

            /**
             * Targetting Indivisual TD Element from Targeted Table. Our Targeted Table will come by temp_number
             * As we have used temp_number and target_action_id in inside function, So this function obvisoulsy shoud
             * declear after to these variable.
             * 
             * @param {String} td_name Actually it will be column names keyword. Suppose, we want to rarget .wpt_price td, than we will use only price as perameter.
             * @returns {$}
             */
            function targetTD(td_name) {
                var targetElement = $('#table_id_' + temp_number + ' #product_id_' + target_action_id + ' td.wpt_' + td_name);
                return targetElement;
            }
            
            /**
             * Set Variations value to the targetted column's td
             * 
             * @param {type} target_td_name suppose: weight,description,serial_number,thumbnails etc
             * @param {type} gotten_value Suppose: variations description from targatted Object
             * @returns {undefined}
             */
            function setValueToTargetTD_IfAvailable(target_td_name, gotten_value){
                //var varitions_description = targetAttributeObject.variation_description;
                if (gotten_value !== "") {
                    targetTD(target_td_name).html(gotten_value);
                }
            }
            
            /**
             * set value for without condition
             * 
             * @param {type} target_td_name for any td
             * @param {type} gotten_value Any valy
             * @returns {undefined}
             */
            function setValueToTargetTD(target_td_name, gotten_value){
                targetTD(target_td_name).html(gotten_value);
            }
            /**
             * 
             * @param {type} target_td_name suppose: weight,description,serial_number,thumbnails etc
             * @param {type} datas_name getting data value from data-something attribute. example: <td data-product_description='This is sample'> s</td>
             * @returns {undefined}
             */
            function getValueFromOldTD(target_td_name, datas_name){
                //Getting back Old Product Description from data-product_description attribute, which is set 
                var product_descrition_old = targetTD(target_td_name).data(datas_name);
                targetTD(target_td_name).html(product_descrition_old);
            }

            var current = {};
            var additionalAddToCartUrl = '';
            $(this).children('select').each(function() {
                var attribute_name = $(this).data('attribute_name');
                var attribute_value = $(this).val();
                current[attribute_name] = attribute_value;
                additionalAddToCartUrl += '&' + attribute_name + '=' + attribute_value;
            });

            var targetVariationIndex = 'not_found';
            variations_data.forEach(function(attributesObject, objectNumber) {
                //console.log(attributesObject.attributes);
                //console.log(current);
                if (JSON.stringify(current) === JSON.stringify(attributesObject.attributes)) {
                    targetVariationIndex = parseInt(objectNumber);
                }
                //targetVariationIndex = parseInt(objectNumber);
            });
            //console.log(targetVariationIndex);
            var wptMessageText = false;
            if (targetVariationIndex !== 'not_found') {
                var targetAttributeObject = variations_data[targetVariationIndex];
                console.log(targetAttributeObject);
                additionalAddToCartUrl += '&variation_id=' + targetAttributeObject.variation_id;
                //Link Adding
                additionalAddToCartUrl = addToCartSelector.data('add_to_cart_url') + additionalAddToCartUrl;
                addToCartSelector.attr('href', additionalAddToCartUrl);

                //Class adding/Removing to add to cart button
                if (targetAttributeObject.is_in_stock) {
                    disbale_enable_class();
                } else {
                    enable_disable_class();
                }

                //Set variation Array to addToCart Button
                //addToCartSelector targetAttributeObject.attributes
                addToCartSelector.attr('data-variation', JSON.stringify(targetAttributeObject.attributes));
                addToCartSelector.attr('data-variation_id', targetAttributeObject.variation_id);

                //console.log(targetAttributeObject);
                //Set stock Message
                if (targetAttributeObject.availability_html === "") {
                    wptMessageText = '<p class="stock in-stock">In stock</p>';
                } else {
                    wptMessageText = targetAttributeObject.availability_html;
                    //console.log(targetAttributeObject.is_in_stock); //targetAttributeObject.is_purchasable
                }
                //Setup Price Live
                //wptMessageText += targetAttributeObject.price_html;
                //targetPriceSelectorTd.html(targetAttributeObject.price_html);
                //targetTD('price').html(targetAttributeObject.price_html);
                setValueToTargetTD_IfAvailable('price', targetAttributeObject.price_html);

                //Set Image Live for Thumbs
                targetThumbs.attr('src', targetAttributeObject.image.gallery_thumbnail_src);
                targetThumbs.attr('srcset', targetAttributeObject.image.srcset);

                //Set SKU live based on Variations
                setValueToTargetTD_IfAvailable('sku', targetAttributeObject.sku);
                //targetTD('sku').html(targetAttributeObject.sku);
                
                //Set Total Price display_price
                var targetQty = $('#table_id_' + temp_number + ' #product_id_' + target_action_id + ' td.wpt_quantity .quantity input.input-text.qty.text').val();
                if(!targetQty){
                    targetQty = 1;
                }
                var targetQtyCurrency = targetTD('total').data('currency');
                var totalPrice = parseFloat(targetQty) * parseFloat(targetAttributeObject.display_price);
                totalPrice = totalPrice.toFixed(2);
                var totalPriceHtml = '<strong>' + targetQtyCurrency + ' ' + totalPrice + '</strong>';

                setValueToTargetTD_IfAvailable('total',totalPriceHtml);
                targetTD('total').attr('data-price', targetAttributeObject.display_price);
                targetTD('total').addClass('total_general');
                
                //Set Description live based on Varitions's Description
                
                setValueToTargetTD_IfAvailable('description', targetAttributeObject.variation_description);
                /*
                var varitions_description = targetAttributeObject.variation_description;
                if (varitions_description !== "") {
                    targetTD('description').html(targetAttributeObject.variation_description);
                }
                */
                
                //Set Live Weight //weight_html
                //targetTD('weight').html(targetAttributeObject.weight);
                
                //Set Weight,Height,Lenght,Width
                setValueToTargetTD_IfAvailable('weight', targetAttributeObject.weight);
                setValueToTargetTD_IfAvailable('height', targetAttributeObject.dimensions.height);
                setValueToTargetTD_IfAvailable('length', targetAttributeObject.dimensions.length);
                setValueToTargetTD_IfAvailable('width', targetAttributeObject.dimensions.width);
                
                
                //SEt Width height Live
                //console.log(targetAttributeObject);


            } else {
                addToCartSelector.attr('data-variation', false);
                addToCartSelector.attr('data-variation_id', false);

                wptMessageText = '<p class="wpt_warning warning">' + targetRightCombinationMsg + '</p>'; //Please choose right combination. //Message will come from targatted tables data attribute //Mainly for WPML issues
                //messageSelector.html('<p class="wpt_warning warning"></p>');

                //Class adding/Removing to add to cart button
                enable_disable_class();

                //Reset Price Data from old Price, what was First time
                getValueFromOldTD('price', 'price_html');
                getValueFromOldTD('sku', 'sku');
                setValueToTargetTD('total', '');
                targetTD('total').attr('data-price', '');
                targetTD('total').removeClass('total_general');

                //Getting back Old Product Description from data-product_description attribute, which is set 
                getValueFromOldTD('description', 'product_description');
                //getValueFromOldTD(targatted_td_name,datas_name);
                /**
                var product_descrition_old = targetTD('description').data('product_description');
                targetTD('description').html(product_descrition_old);
                */
                
                //Getting Back Old Weight,Lenght,Width,Height
                getValueFromOldTD('weight', 'weight');
                getValueFromOldTD('length', 'length');
                getValueFromOldTD('width', 'width');
                getValueFromOldTD('height', 'height');
            }

            //Set HTML Message to define div/box
            messageSelector.html(wptMessageText);


            function enable_disable_class() {
                addToCartSelector.removeClass('enabled');
                addToCartSelector.addClass('disabled');


                checkBoxSelector.removeClass('enabled');
                checkBoxSelector.addClass('disabled');


            }
            function disbale_enable_class() {
                addToCartSelector.removeClass('disabled');
                addToCartSelector.addClass('enabled');


                checkBoxSelector.removeClass('disabled');
                checkBoxSelector.addClass('enabled');
            }

        });


        /**
         $('td.data_product_variations .wpt_varition_section select').change(function(){
         var data_total_attributes = $(this).data('total_attributes');
         var target_action_id = $(this).data('product_id');
         var target_class = '.wpt_action_' + target_action_id;
         var variations_data = $(this).closest(target_class).data('product_variations');
         variations_data.forEach(function(para){
         console.log(para.attributes);
         });
         console.log(data_total_attributes);
         console.log(variations_data);
         });
         
         
         
         */


        /**
         * Working for Checkbox of our Table
         */
        $('body').on('click', 'input.wpt_tabel_checkbox.wpt_td_checkbox.disabled', function(e) {
            e.preventDefault();
            alert("Sorry, Please choose right combination.");
            return false;
        });


        //$('input.wpt_check_universal').click(function(){
        $('body').on('click', 'input.wpt_check_universal,input.enabled.wpt_tabel_checkbox.wpt_td_checkbox', function() {
            var temp_number = $(this).data('temp_number');
            var checkbox_type = $(this).data('type'); //universal_checkbox
            if (checkbox_type === 'universal_checkbox') {
                $('#table_id_' + temp_number + ' input.enabled.wpt_tabel_checkbox.wpt_td_checkbox').prop('checked', this.checked);
            }
            var temp_number = $(this).data('temp_number');
            var add_cart_text = $('#table_id_' + temp_number).data('add_to_cart');
            var currentAllSelectedButtonSelector = $('#table_id_' + temp_number + ' a.button.add_to_cart_all_selected');
            var itemAmount = 0;
            $('#table_id_' + temp_number + ' input.enabled.wpt_tabel_checkbox.wpt_td_checkbox:checked').each(function() {
                itemAmount++;//To get Item Amount
            });
            var itemText = 'Items';
            if (itemAmount === 1 || itemAmount === 0) {
                itemText = 'Item';
            }
            currentAllSelectedButtonSelector.html( add_cart_text + ' [ ' + itemAmount + ' ' + itemText + ' ]');

        });



        $('a.button.add_to_cart_all_selected').click(function() {
            var temp_number = $(this).data('temp_number');

            //Add Looading and Disable class 
            var currentAllSelectedButtonSelector = $('#table_id_' + temp_number + ' a.button.add_to_cart_all_selected');
            currentAllSelectedButtonSelector.addClass('disabled');
            currentAllSelectedButtonSelector.addClass('loading');

            var site_url = $('#table_id_' + temp_number).data('site_url');
            site_url = site_url + '/wp-admin/admin-ajax.php';
            var add_cart_text = $('#table_id_' + temp_number).data('add_to_cart');

            var tables_cart_message_box = '.tables_cart_message_box_' + temp_number; //$wpt_temporary_class
            //Getting Data from all selected checkbox
            var products_data = [];
            var itemAmount = 0;
            $('#table_id_' + temp_number + ' input.enabled.wpt_tabel_checkbox.wpt_td_checkbox:checked').each(function() {
                var product_id = $(this).data('product_id');
                var currentAddToCartSelector = $('#table_id_' + temp_number + ' #product_id_' + product_id + ' .wpt_action a.wpt_woo_add_cart_button');
                var currentVariaionId = currentAddToCartSelector.data('variation_id');
                var currentVariaion = currentAddToCartSelector.data('variation');
                var currentQantity = $('#table_id_' + temp_number + ' table#wpt_table .wpt_row_product_id_' + product_id + ' .wpt_quantity .quantity input.input-text.qty.text').val();
                products_data[product_id] = {product_id: product_id, quantity: currentQantity, variation_id: currentVariaionId, variation: currentVariaion};

                //itemAmount += currentQantity;//To get Item Amount with Quantity
                itemAmount++;
                //console.log('#table_id_'+temp_number);
            });

            //Return false for if no data
            if (products_data.length <= 1) {
                currentAllSelectedButtonSelector.removeClass('disabled');
                currentAllSelectedButtonSelector.removeClass('loading');
                alert('Please Choose items.');
                return false;
            }

            $.ajax({
                type: 'POST',
                url: site_url,
                data: {
                    action: 'wpt_ajax_mulitple_add_to_cart',
                    products: products_data,
                },
                success: function(data) {
                    $(tables_cart_message_box).html(data);

                    currentAllSelectedButtonSelector.removeClass('disabled');
                    currentAllSelectedButtonSelector.removeClass('loading');
                    currentAllSelectedButtonSelector.html(add_cart_text + ' [ ' + itemAmount + ' Added ]');

                },
                error: function() {
                    alert('Failed');
                },
            });
        });
    });
})(jQuery);
