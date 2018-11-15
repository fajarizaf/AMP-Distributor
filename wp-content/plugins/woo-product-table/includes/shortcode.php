<?php

global $shortCodeText;
add_shortcode($shortCodeText, 'wpt_shortcode_generator');

/**
 * Shortcode Generator for WPT Plugin
 * https://codersaiful.net/woo-product-table-pro?add-to-cart=72&attribute_pa_size=l&attribute_pa_color=blue&quantity=1
 * 
 * @param array $atts
 * @return string
 * 
 * @since 1.0
 */
function wpt_shortcode_generator($atts = false) {
    //global $woocommerce,$wc_product_attributes;
    //var_dump($wc_product_attributes);
    
    //var_dump(wc_get_attribute_taxonomies());
    
    
    /**
     * Define Temporary Class by Random_int Function
     * Random Number Generating for temporary class place
     * Actually its need to controll all table indivisally, if anybody show one more table at a time
     * 
     * @since 1.0.0 -5
     */
    $wpt_temporary_class = random_int(10, 300); //Now only used mainly for JavaScript calling

    /**
     * Set Variable $html to return
     * 
     * @since 1.1
     */
    $html = '';
    $pairs = array('exclude' => false);
    extract(shortcode_atts($pairs, $atts));

    
    $wpt_table_class = ( isset($atts['table_class']) && !empty($atts['table_class']) ? $atts['table_class'] : false );
    
    /**
     * @reference https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
     */
    $wpt_product_short_order_by = ( isset($atts['sort_order_by']) && !empty($atts['sort_order_by']) ? $atts['sort_order_by'] : 'post_title' );
    $wpt_product_short = ( isset($atts['sort']) && !empty($atts['sort']) && in_array($atts['sort'], array('ASC', 'DESC')) ? $atts['sort'] : false ); //Setting Default value false, Becuause added Randomize Option for Default at Bellow After Query
    
    $product_cat_ids_string = ( isset($atts['product_cat_ids']) && !empty($atts['product_cat_ids']) ? $atts['product_cat_ids'] : false );
    $product_cat_ids = wpt_explode_string_to_array($product_cat_ids_string);
    
    $cat_explude_string = ( isset($atts['cat_explude']) && !empty($atts['cat_explude']) ? $atts['cat_explude'] : false );
    $cat_explude = wpt_explode_string_to_array($cat_explude_string);
    
    $post_exclude_string = ( isset($atts['post_exclude']) && !empty($atts['post_exclude']) ? $atts['post_exclude'] : false );
    $post_exclude = wpt_explode_string_to_array($post_exclude_string);
    
    $only_stock = ( isset($atts['only_stock']) && !empty($atts['only_stock']) ? $atts['only_stock'] : false );
    
    /**
     * New Classing system for Advance Mobile Responsive System
     * Added at 1.5
     * date: 12.5.2018 d/m/y
     */
    $mobile_responsive = ( isset($atts['mobile_responsive']) && !empty($atts['mobile_responsive']) ? 'mobile_responsive' : false );
    $product_cat_slugs_string = ( isset($atts['product_cat_slugs']) && !empty($atts['product_cat_slugs']) ? $atts['product_cat_slugs'] : false );
    $product_cat_slugs = wpt_explode_string_to_array($product_cat_slugs_string);


    $wpt_table_column_keywords_string = ( isset($atts['column_keyword']) && !empty($atts['column_keyword']) ? $atts['column_keyword'] : false );
    $wpt_table_column_keywords = wpt_explode_string_to_array($wpt_table_column_keywords_string);
    
    
    $wpt_table_mobileHide_keywords_string = ( isset($atts['mobile_hide']) && !empty($atts['mobile_hide']) ? $atts['mobile_hide'] : false );
    $wpt_table_mobileHide_keywords = wpt_explode_string_to_array($wpt_table_mobileHide_keywords_string);
    
    
    
    $wpt_add_to_cart_text = ( isset($atts['add_to_cart_text']) && !empty($atts['add_to_cart_text']) ? $atts['add_to_cart_text'] : 'Add to cart' );
    $wpt_add_to_cart_selected_text = ( isset($atts['add_to_cart_selected_text']) && !empty($atts['add_to_cart_selected_text']) ? $atts['add_to_cart_selected_text'] : 'Add to Cart [Selected]' );
    $wpt_check_uncheck_all_text = ( isset($atts['check_uncheck_text']) && !empty($atts['check_uncheck_text']) ? $atts['check_uncheck_text'] : 'All Check/Uncheck' );
    /**
     * Variations Added to V1.0.4
     * 
     * Again Deprecitated at V1.0.4 1/5/2018
     */
    //$wpt_variations_string = ( isset($atts['variations']) && !empty($atts['variations']) ? $atts['variations'] : false );

    //$wpt_variations = wpt_explode_string_to_array($wpt_variations_string);
    
    /**
     * Define permitted TD inside of Table, Not for Table head
     * Only for Table Body
     * Generate Array by wpt_define_permitted_td_array() which is available in functions.php file of Plugin
     * @since 1.0.4
     */
    $wpt_permitted_td = wpt_define_permitted_td_array( $wpt_table_column_keywords );

    $product_min_price = ( isset($atts['min_price']) && !empty($atts['min_price']) ? $atts['min_price'] : false );
    $product_max_price = ( isset($atts['max_price']) && !empty($atts['max_price']) ? $atts['max_price'] : false );

    //Table Column Title
    $wpt_table_column_title_string = ( isset($atts['column_title']) && !empty($atts['column_title']) ? $atts['column_title'] : false );
    $wpt_table_column_title = wpt_explode_string_to_array($wpt_table_column_title_string);
    //wpt_explode_string_to_array($string);

    /**
     * For Product Description Lenght
     * @keyword description_length
     * @since 1.0.0 -5
     */
    $wpt_description_length = ( isset($atts['description_length']) && !empty($atts['description_length']) ? $atts['description_length'] : 80 );

    /**
     * Table Template Selection Variable
     */
    $wpt_template = ( isset($atts['template']) && !empty($atts['template']) ? $atts['template'] : WOO_Product_Table::getOption('wpt_style_file_selection') );
    
    /**
     * sET posts_per_page 
     * @since v 1.5
     */
    $posts_per_page = ( isset($atts['posts_per_page']) && !empty($atts['posts_per_page']) ? $atts['posts_per_page'] : -1 );

    /**
     * DataTable Selection Section
     * Keword: DataTable
     * 
     * @since 1.0.0 -15
     */
    if ($wpt_template == 'dataTable') {
        $DataTable = 'display dataTable';
        $mobile_responsive = false;
    } else {
        $DataTable = false;
        //$wpt_template = $wpt_template;
    }
    //$DataTable = ($wpt_template == 'dataTable' ? 'display dataTable' : false);


    /**
     * Args
     */
    $args = array(
        'posts_per_page' => $posts_per_page,//-1, //Permanent value -1 has removed from version 1.5
        'post_type' => array('product'), //, 'product_variation'
        /*
          'orderby'   => 'post_title',
          'order' => $wpt_product_short,
         */
        'meta_query' => array(
            /*
              array(
              'key' => '_price',
              'value' => 11,
              'compare' => '>',
              'type' => 'NUMERIC'
              ),

              array(
              'key' => '_price',
              'value' => 13,
              'compare' => '<',
              'type' => 'NUMERIC'
              ),
             
            array(//For Available product online
                'key' => '_stock_status',
                'value' => 'instock'
            )
            */
        ),
            /*
              'tax_query' => array(
              array(
              'taxonomy' => 'product_cat',
              'field' => 'id',
              'terms' => array(19),
              )
              ),
             */
    );

    
    if($only_stock){
        $args['meta_query'][] = array(//For Available product online
                'key' => '_stock_status',
                'value' => 'instock'
            );
    }
    /**
     * Mordnanize Shorting Option
     * Actually Default Value  will be RANDOM, So If not set ASC or DESC, Than Sorting 
     * willbe Random by default. Althoug Just aftet WP_Query
     * 
     * @since 1.0.0 -9
     */
    if ($wpt_product_short) {
        $args['orderby'] = $wpt_product_short_order_by;//'post_title';
        $args['order'] = $wpt_product_short;
    }


    /**
     * Set Minimum Price for
     */
    if ($product_min_price) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => $product_min_price,
            'compare' => '>=',
            'type' => 'NUMERIC'
        );
    }

    /**
     * Set Maximum Price for
     */
    if ($product_max_price) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => $product_max_price,
            'compare' => '<=',
            'type' => 'NUMERIC'
        );
    }

    /**
     * Args Set for tax_query if available $product_cat_ids
     * 
     * @since 1.0
     */
    if ($product_cat_ids) {
        $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $product_cat_ids,
                'operator' => 'IN'
            );

    }
    //'operator' => 'IN'
    /**
     * Category Excluding System
     * 
     * @since 1.0.4
     * @date 27/04/2018
     */
    if($cat_explude){
        $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $cat_explude,
                'operator' => 'NOT IN'
            );
    }
    
    /**
     * Post Exlucde
     * 
     * @since 1.0.4
     * @date 28/04/2018
     */
    if($post_exclude){
        $args['post__not_in'] = $post_exclude;
    }
    
    /**
     * Args Set for tax_query if available $product_cat_ids
     * 
     * @since 1.0
     */
    if ($product_cat_slugs) {
        $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $product_cat_slugs,
            );
    }
    
    /**
     * Add to cart Check Select /check/unchech Section
     * 
     * @version V1.0.4 
     * @date 2/5/2018
     */
    $html_check = $html_check_footer = false;
    if($wpt_permitted_td['check']){
        //
        $add_to_cart_selected_text = $wpt_add_to_cart_selected_text;//'Add to Cart [Selected]';
        
        $html_check .= "<div class='all_check_header_footer all_check_header check_header_{$wpt_temporary_class}'>";
        $html_check_footer .= "<div class='all_check_header_footer all_check_footer check_footer_{$wpt_temporary_class}'>";
        
        $html_check .= "<span><input data-type='universal_checkbox' data-temp_number='{$wpt_temporary_class}' class='wpt_check_universal' id='wpt_check_uncheck_button_{$wpt_temporary_class}' type='checkbox'><label for='wpt_check_uncheck_button_{$wpt_temporary_class}'>{$wpt_check_uncheck_all_text}</lable></span>";
        
        $html_check .= "<a data-add_to_cart='{$wpt_add_to_cart_text}' data-temp_number='{$wpt_temporary_class}' class='button add_to_cart_all_selected'>$add_to_cart_selected_text</a>";
        $html_check_footer .= "<a data-add_to_cart='{$wpt_add_to_cart_text}' data-temp_number='{$wpt_temporary_class}' class='button add_to_cart_all_selected'>$add_to_cart_selected_text</a>";
        
        $html_check .= "</div>";
        $html_check_footer .= "</div>";
    }
    
    //var_dump(the_widget( 'WC_Widget_Cart', 'title=' ));

    $html .= apply_filters('wpt_before_table_wrapper', ''); //Apply Filter Just Before Table Wrapper div tag

    $html .= "<div data-right_combination_message='" . esc_attr( __( 'Please choose right combination.', 'wpt_table' ) ) . "' data-add_to_cart='{$wpt_add_to_cart_text}' data-site_url='" . home_url() . "' id='table_id_" . $wpt_temporary_class . "' class='wpt_temporary_wrapper_" . $wpt_temporary_class . " wpt_product_table_wrapper " . $wpt_template . "_wrapper woocommerce'>"; //Table Wrapper Div start here with class. //Added woocommerce class at wrapper div in V1.0.4
    $html .= "<div class='tables_cart_message_box_{$wpt_temporary_class}'></div>";
    $html .= $html_check; //Added at @Version 1.0.4
    $html .= apply_filters('wpt_before_table', ''); //Apply Filter Jese Before Table Tag
    $html .= "<table id='" . apply_filters('wpt_change_table_id', 'wpt_table') . "' class='{$mobile_responsive} wpt_temporary_table_" . $wpt_temporary_class . " wpt_product_table " . $wpt_template . "_table $wpt_table_class $DataTable'>"; //Table Tag start here.

    /**
     * this $responsive_table will use for responsive table css Selector.
     * I have used this table selector at the end of table
     * See at bellow inside of <<<EOF EOF;
     * 
     * @since 1.5
     */
    $responsive_table = "table#wpt_table.mobile_responsive.wpt_temporary_table_{$wpt_temporary_class}.wpt_product_table";
    
    /**
     * Table Column Field Tilte Define here
     * 
     * @since 1.0.04
     */
    $column_title_html = $responsiveTableLabelData = false;
    if ($wpt_table_column_title && is_array($wpt_table_column_title) && count($wpt_table_column_title) >= 1) {
        $column_title_html .= '<thead><tr class="wpt_table_header_row wpt_table_head">';
        foreach ( $wpt_table_column_title as $key=>$colunt_title ) {
            /**
             * this $responsiveTableLabelData will use for Responsives 
             */
            $responsiveTableLabelData .= $responsive_table . ' td:nth-of-type(' . ($key + 1) . '):before { content: "' . $colunt_title . '"; }';
            $column_class = ( isset( $wpt_table_column_keywords[$key] ) ? $wpt_table_column_keywords[$key] : '' );
            $column_title_html .= "<th class='wpt_{$column_class}'>{$colunt_title}</th>";
        }
        $column_title_html .= '</tr></thead>';
    }
    $html .= $column_title_html;

    $product_loop = new WP_Query($args);

    /**
     * If not set any Shorting (ASC/DESC) than Post loop will Random by Shuffle()
     * @since 1.0.0 -9
     */
    if (!$wpt_product_short) {
        shuffle($product_loop->posts);
    }


    //var_dump($product_loop->posts); //Development Perpose.
    //var_dump($args); //Development Perpose.



    $html .= '<tbody>'; //Starting TBody here
    $wpt_table_row_serial = 1; //For giving class id for each Row as well
    if ($product_loop->have_posts()) : while ($product_loop->have_posts()): $product_loop->the_post();
            /**
             * Product Opject Define for get Important infomation for Each Product
             * 
             * @since 1.1
             */
            $wpt_product = wc_get_product( get_the_ID() );
            $data = $wpt_product->get_data();
            //var_dump($data);
            
            
            /**
             * Check DataTable available or not
             * and set class even or odd
             */
            if ($DataTable) {
                $DataTable_even_odd = (($wpt_table_row_serial % 2) == 0 ? 'odd' : 'even');
            } else {
                $DataTable_even_odd = '';
            }
            
            
            
            
            /**
             * Table Row and
             * And Table Data filed here will display
             * Based on Query
             */
            $wpt_each_row = false;
            $html .= "<tr role='row' data-title='" . esc_attr( $data['name'] ) . "' data-product_id='" . $data['id'] . "' id='product_id_" . $data['id'] . "' class='wpt_row wpt_row_serial_$wpt_table_row_serial wpt_row_product_id_" . get_the_ID() . " " . $DataTable_even_odd . "'>"; //Starting Table row here.

            
             
            
            /**
             * Define Serial Number for Each Row
             * 
             * @since 1.0
             */
            ###if ($wpt_permitted_td['serial_number']) {
                $wpt_each_row['serial_number'] = "<td class='wpt_serial_number'> $wpt_table_row_serial </td>";
            ###}
               
                
            /**
             * Define Stock Status for Each Product
             * 
             * @since 1.0.4
             * @date 28/04/2018
             */
            ###if ($wpt_permitted_td['serial_number']) {
                $wpt_each_row['stock'] = "<td class='wpt_stock'> <span class='{$data['stock_status']}'>" . ( $data['stock_status'] != 'instock' ? 'Out of Stock' : $data['stock_quantity'] . ' In Stock' ) . " </span></td>";
            ###}
            //var_dump($data);
                
                
            /**
             * Product Title Display with Condition
             *  valign="middle"
             */
            ###if ($wpt_permitted_td['thumbnails']) {
                $wpt_single_thumbnails = false;
                $wpt_single_thumbnails .= "<td valign='middle' class='wpt_thumbnails'>";
                $wpt_single_thumbnails .= woocommerce_get_product_thumbnail(array(56, 56));
                $wpt_single_thumbnails .= "</td>";
                $wpt_each_row['thumbnails'] = $wpt_single_thumbnails;
            ###}

            /**
             * Product Title Display with Condition
             */
            ###if ($wpt_permitted_td['product_title']) {
                $wpt_single_product_title = false;
                $wpt_single_product_title .= "<td class='wpt_product_title'>";
                $wpt_single_product_title .= "<a target='_blank' href='" . esc_url(get_the_permalink()) . "'>" . get_the_title() . "</a>";
                $wpt_single_product_title .= "</td>";
                $wpt_each_row['product_title'] = $wpt_single_product_title;
            ###}

            /**
             * Product Description Display with Condition
             */
            ###if ($wpt_permitted_td['description']) {
                $wpt_each_row['description'] = "<td class='wpt_description'  data-product_description='" . esc_attr( substr(get_the_excerpt(), 0, $wpt_description_length) ) . "'><p>" . substr(get_the_excerpt(), 0, $wpt_description_length) . "</p></td>";
            ###}

            /**
             * Product Category Display with Condition
             */
            ###if ($wpt_permitted_td['category']) {
                $wpt_single_category = false;
                /**
                 * $wpt_cotegory_col Define at before of TR (Table Row)
                 * $wpt_cotegory_col = wc_get_product_category_list(get_the_ID());
                 */
                $wpt_cotegory_col = wc_get_product_category_list( $data['id'] );
                $wpt_single_category .= "<td class='wpt_category'>";
                $wpt_single_category .= $wpt_cotegory_col;
                $wpt_single_category .= "</td>";

                $wpt_each_row['category'] = $wpt_single_category;
            ###}

            /**
             * Product Tags Display with Condition
             */
            ###if ($wpt_permitted_td['tags']) {
                $wpt_single_tags = false;
                $wpt_tag_col = wc_get_product_tag_list( $data['id'] );
                $wpt_single_tags .= "<td class='wpt_tags'>";
                $wpt_single_tags .= $wpt_tag_col;
                $wpt_single_tags .= "</td>";
                $wpt_each_row['tags'] = $wpt_single_tags;
            ###}


            /**
             * Product SKU Dispaly
             */
            ###if ($wpt_permitted_td['sku']) {
                $wpt_each_row['sku'] = "<td data-sku='" . $wpt_product->get_sku() . "' class='wpt_sku'><p>" . $wpt_product->get_sku() . "</p></td>";
            ###}


            /**
             * Product Rating Dispaly
             */
            ###if ($wpt_permitted_td['rating']) {
            //Add here @version 1.0.4
            $wpt_average = $data['average_rating'];
            $wpt_product_rating = '<div class="star-rating" title="' . sprintf(__('Rated %s out of 5', 'woocommerce'), $wpt_average) . '"><span style="width:' . ( ( $wpt_average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">' . $wpt_average . '</strong> ' . __('out of 5', 'woocommerce') . '</span></div>';


                $wpt_each_row['rating'] = "<td class='wpt_rating woocommerce'><p>" . $wpt_product_rating . "</p></td>";
            ###}

            /**
             * Display Price
             */
            ###if ($wpt_permitted_td['price']) {
                $wpt_single_price = false;
                $wpt_single_price .= "<td class='wpt_price'  id='price_value_id_" . $data['id'] . "' data-price_html='" . esc_attr( $wpt_product->get_price_html() ) . "'> ";
                $wpt_single_price .= '<span class="wpt_product_price">';
                $wpt_single_price .= $wpt_product->get_price_html(); //Here was woocommerce_template_loop_price() at version 1.0
                $wpt_single_price .= '</span>';
                $wpt_single_price .= " </td>";

                $wpt_each_row['price'] = $wpt_single_price;
            ###}

            /**
             * Display Quantity for WooCommerce Product Loop
             */
            ###if ($wpt_permitted_td['quantity']) {
                $wpt_single_quantity = false;
                $wpt_single_quantity .= "<td class='wpt_quantity' data-target_id='" . $data['id'] . "'> ";
                $wpt_single_quantity .= woocommerce_quantity_input(false, false, false); //Here was only woocommerce_quantity_input() at version 1.0
                $wpt_single_quantity .= " </td>";
                $wpt_each_row['quantity'] = $wpt_single_quantity;
            ###}
 
                
            /**
             * For Variable Product
             * 
             */
            $row_class = $data_product_variations = $variation_html = $variable_for_total = false;
            
            //Out_of_stock class Variable
            $outofstock_class = ( $data['stock_status'] != 'instock' ? 'outofstock_add_to_cart_button disabled' : 'add_to_cart_button' );
                
            /**
             * Display Add-To-Cart Button
             */
            if ($wpt_permitted_td['action']) {
                $wpt_single_action = false;
                $wpt_single_action .= "<td data-temp_number='{$wpt_temporary_class}' class='{$row_class} wpt_action wpt_action_" . $data['id'] . "' data-product_id='" . $data['id'] . "' data-product_variations = '{$data_product_variations}'> ";
                $wpt_single_action .= $variation_html;
                //$wpt_single_action .= '<span class="wpt_product_price">';
                /*
                $wpt_single_action .= sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>', 
                        esc_url($wpt_product->add_to_cart_url()), 
                        esc_attr(1), esc_attr($wpt_product->get_id()), 
                        esc_attr($wpt_product->get_sku()), 
                        esc_attr('button product_type_simple add_to_cart_button ajax_add_to_cart'), 
                        esc_html($wpt_product->add_to_cart_text())
                );
                */
                
                $add_to_cart_url = '?add-to-cart=' .  $data['id']; //home_url() . 
                $wpt_single_action .= apply_filters('woocommerce_loop_add_to_cart_link', 
                        sprintf('<a rel="nofollow" data-add_to_cart_url="%s" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>', 
                                esc_attr( $add_to_cart_url ),
                                //'http://localhost/practice-wp/product-table/?add-to-cart=' . $data['id'] . '&attribute_borno=ETC&quantity=10', 
                                esc_url( $add_to_cart_url ), 
                                //esc_url( $wpt_product->add_to_cart_url() ), 
                                esc_attr( 1 ), 
                                esc_attr($wpt_product->get_id()), 
                                esc_attr($wpt_product->get_sku()), 
                                esc_attr( $row_class || !$data['price'] ? 'wpt_variation_product single_add_to_cart_button button alt disabled wc-variation-selection-needed wpt_woo_add_cart_button' : 'button wpt_woo_add_cart_button ' . $outofstock_class ), //ajax_add_to_cart
                                esc_html( $wpt_add_to_cart_text )
                                //esc_html($wpt_product->add_to_cart_text())
                        ), $wpt_product);


                //woocommerce_template_loop_add_to_cart();
                //$wpt_single_action .= '</span>';
                $wpt_single_action .= " </td>";

                $wpt_each_row['action'] = $wpt_single_action;
            }
            
            
            //var_dump($wpt_table_column_keywords);
            //var_dump($wpt_each_row);
            /**
            if(is_array($wpt_table_column_keywords) && count($wpt_table_column_keywords) > 0){
                $final_row_data = false;
                foreach($wpt_table_column_keywords as $each_keyword){
                    $final_row_data .= ( isset($wpt_each_row[$each_keyword]) ? $wpt_each_row[$each_keyword] : false );
                }
            }
             */
            
            $html .= wpt_generate_each_row_data($wpt_table_column_keywords, $wpt_each_row);
            $html .= "</tr>"; //End of Table row

            $wpt_table_row_serial++; //Increasing Serial Number.

        endwhile;
        wp_reset_query();
    else:
        $html .= apply_filters('wpt_product_not_found', 'Product Not found');
    endif;
    
    
    
    $html .= '</tbody>'; //Tbody End here
    $html .= "</table>"; //Table tag end here.
    
    $html .= $html_check_footer;
    $html .= apply_filters('wpt_after_table', ''); //Apply Filter Just Before Table Wrapper div tag
    $html .= "</div>"; //End of Table wrapper.
    $html .= apply_filters('wpt_after_table_wrapper', ''); //Apply Filter Just After Table Wrapper div tag
    
    /**
     * This veriable will generate dataTable's js code, Only if active 
     * dataTable Template select
     */
    $jscode_for_datatable = ( $DataTable ? "$('.wpt_temporary_table_{$wpt_temporary_class}').dataTable();" : false );
    
    $html .= <<<EOF

<script>
    (function($) {
        $(document).ready(function() {
            /**
             * Data Table 
             */
            //Inactive // $jscode_for_datatable

            
            $('body').on('change', '.wpt_temporary_table_{$wpt_temporary_class} .wpt_quantity input.input-text.qty.text', function() {
            //$('.wpt_temporary_table_{$wpt_temporary_class} .wpt_quantity input.input-text.qty.text').change(function() {
                var target_Qty_Val = $(this).val();
                var target_product_id = $(this).closest('td.wpt_quantity').attr('data-target_id');
                var targetTotalSelector = $('.wpt_temporary_table_{$wpt_temporary_class} .wpt_row_product_id_' + target_product_id + ' td.wpt_total.total_general');
                var targetTotalStrongSelector = $('.wpt_temporary_table_{$wpt_temporary_class} .wpt_row_product_id_' + target_product_id + ' td.wpt_total.total_general strong');
                var targetPrice = targetTotalSelector.attr('data-price');
                var targetCurrency = targetTotalSelector.data('currency');
                var totalPrice = parseFloat(targetPrice) * parseFloat(target_Qty_Val);
                totalPrice = totalPrice.toFixed(2);
                
                $('.wpt_temporary_table_{$wpt_temporary_class} .wpt_row_product_id_' + target_product_id + ' .wpt_action a.wpt_woo_add_cart_button').attr('data-quantity', target_Qty_Val);
                targetTotalStrongSelector.html(targetCurrency + ' ' + totalPrice);
                //$(target_row_id + ' a.add_to_cart_button').attr('data-quantity', target_Qty_Val);
            });
            
        });
    })(jQuery);
</script>
EOF;
    return $html;
}
