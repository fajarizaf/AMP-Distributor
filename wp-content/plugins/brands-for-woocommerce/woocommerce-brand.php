<?php
/**
 * Plugin Name: Brands for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/brands-for-woocommerce/
 * Description: WooCommerce Brand plugin allows you to create brands for products on your shop. Each brands has name, description and image.
 * Version: 1.0.9
 * Author: BeRocket
 * Requires at least: 4.0
 * Author URI: http://berocket.com
 * Text Domain: BeRocket_product_brand_domain
 * Domain Path: /languages/
 */
define( "BeRocket_product_brand_version", '1.0.9' );
define( "BeRocket_product_brand_domain", 'BeRocket_product_brand_domain'); 
define( "product_brand_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain('BeRocket_product_brand_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once(plugin_dir_path( __FILE__ ).'includes/admin_notices.php');
require_once(plugin_dir_path( __FILE__ ).'includes/functions.php');
require_once(plugin_dir_path( __FILE__ ).'includes/widget-links.php');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class BeRocket_product_brand {

    public static $info = array( 
        'id'        => 19,
        'version'   => BeRocket_product_brand_version,
        'plugin'    => '',
        'slug'      => '',
        'key'       => '',
        'name'      => ''
    );

    /**
     * Defaults values
     */
    public static $defaults = array(
        'display_thumbnail'     => '',
        'thumbnail_width'       => '100%',
        'thumbnail_align'       => 'none',
        'display_description'   => '',
        'product_thumbnail'     => '',
        'custom_css'            => '',
        'script'                => array(
            'js_page_load'          => '',
        ),
        'plugin_key'            => '',
    );
    public static $values = array(
        'settings_name' => 'br-product_brand-options',
        'option_page'   => 'br-product_brand',
        'premium_slug'  => 'woocommerce-brands',
    );
    
    function __construct () {
        register_activation_hook( __FILE__, array( __CLASS__, 'register_taxonomy' ), 10 );
        register_activation_hook( __FILE__, 'flush_rewrite_rules', 20 );
        register_uninstall_hook(__FILE__, array( __CLASS__, 'deactivation' ) );

        add_action ( 'init', array( __CLASS__, 'register_taxonomy' ) );
        if ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) && 
            br_get_woocommerce_version() >= 2.1 ) {
            $options = self::get_option();
            
            add_action ( 'init', array( __CLASS__, 'init' ) );
            add_action ( 'wp_head', array( __CLASS__, 'set_styles' ) );
            add_action ( 'admin_init', array( __CLASS__, 'admin_init' ) );
            add_action ( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
            add_action ( 'admin_menu', array( __CLASS__, 'options' ) );
            add_action( "wp_ajax_br_product_brand_settings_save", array ( __CLASS__, 'save_settings' ) );
            add_action( "woocommerce_archive_description", array ( __CLASS__, 'description' ), 10 );
            add_action ( "widgets_init", array ( __CLASS__, 'widgets_init' ) );
            add_shortcode( 'brands_products', array( __CLASS__, 'products_shortcode' ) );
            add_shortcode( 'brands_list', array( __CLASS__, 'brands_list_shortcode' ) );
            add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
            add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
            $plugin_base_slug = plugin_basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_base_slug, array( __CLASS__, 'plugin_action_links' ) );
            add_filter( 'is_berocket_settings_page', array( __CLASS__, 'is_settings_page' ) );
        }
        add_filter('berocket_admin_notices_subscribe_plugins', array(__CLASS__, 'admin_notices_subscribe_plugins'));
    }
    public static function admin_notices_subscribe_plugins($plugins) {
        $plugins[] = self::$info['id'];
        return $plugins;
    }
    public static function is_settings_page($settings_page) {
        if( ! empty($_GET['page']) && $_GET['page'] == self::$values[ 'option_page' ] ) {
            $settings_page = true;
        }
        return $settings_page;
    }
    public static function plugin_action_links($links) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page='.self::$values['option_page'] ) . '" title="' . __( 'View Plugin Settings', 'BeRocket_products_label_domain' ) . '">' . __( 'Settings', 'BeRocket_products_label_domain' ) . '</a>',
		);
		return array_merge( $action_links, $links );
    }
    public static function plugin_row_meta($links, $file) {
        $plugin_base_slug = plugin_basename( __FILE__ );
        if ( $file == $plugin_base_slug ) {
			$row_meta = array(
				'docs'    => '<a href="http://berocket.com/docs/plugin/'.self::$values['premium_slug'].'" title="' . __( 'View Plugin Documentation', 'BeRocket_products_label_domain' ) . '" target="_blank">' . __( 'Docs', 'BeRocket_products_label_domain' ) . '</a>',
				'premium'    => '<a href="http://berocket.com/product/'.self::$values['premium_slug'].'" title="' . __( 'View Premium Version Page', 'BeRocket_products_label_domain' ) . '" target="_blank">' . __( 'Premium Version', 'BeRocket_products_label_domain' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}
		return (array) $links;
    }
    public static function widgets_init () {
        register_widget("berocket_product_brand_widget");
    }
    public static function template_loader( $template ) {

		$find = array( 'woocommerce.php' );
		$file = '';

		if ( is_tax( 'berocket_brand' ) ) {

			$term = get_queried_object();

			$woocommerce_url = apply_filters( 'woocommerce_template_url', 'woocommerce/' );
            $file   = 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = $woocommerce_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = $file;
			$find[] = $woocommerce_url . $file;

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = product_brand_TEMPLATE_PATH . $file;
		}

		return $template;
	}
    public static function brands_list_shortcode($atts = array()) {
        ob_start();
        the_widget( 'berocket_product_brand_widget', $atts);
        return ob_get_clean();
    }
    public static function products_shortcode($atts = array()) {
        $atts = shortcode_atts( array(
			'columns'   => '4',
			'orderby'   => 'title',
			'order'     => 'desc',
			'brand_id'  => '',
			'brand_slug'=> '',
			'operator'  => 'IN'
		), $atts );

		if ( ! @ $atts['brand_id'] && ! @ $atts['brand_slug'] ) {
			return '';
		}

		// Default ordering args
		$ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
		$meta_query    = WC()->query->get_meta_query();
        if( @ $atts['brand_id'] ) {
            $brand = $atts['brand_id'];
            $brand_field = 'id';
        } elseif( @ $atts['brand_slug'] ) {
            $brand = $atts['brand_slug'];
            $brand_field = 'slug';
        }
		$query_args    = array(
			'post_type'            => 'product',
			'post_status'          => 'publish',
			'orderby'              => $ordering_args['orderby'],
			'order'                => $ordering_args['order'],
			'posts_per_page'       => (empty($atts['per_page']) ? '12' : $atts['per_page']),
			'meta_query'           => $meta_query,
			'tax_query'            => array(
				array(
					'taxonomy'     => 'berocket_brand',
					'terms'        => explode( ',', $brand ),
					'field'        => $brand_field,
					'operator'     => $atts['operator']
				)
			)
		);
        if( empty($atts['per_page']) ) {
            unset($atts['per_page']);
        }

		if ( isset( $ordering_args['meta_key'] ) ) {
			$query_args['meta_key'] = $ordering_args['meta_key'];
		}

		$return = self::product_loop( $query_args, $atts, 'product_cat' );

		// Remove ordering query arguments
		WC()->query->remove_ordering_args();

		return $return;
    }
	private static function product_loop( $query_args, $atts, $loop_name ) {
		global $woocommerce_loop;

		$products                    = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $query_args, $atts, $loop_name ) );
		$columns                     = absint( $atts['columns'] );
		$woocommerce_loop['columns'] = $columns;
		$woocommerce_loop['name']    = $loop_name;

		ob_start();
		if ( $products->have_posts() ) {
			?>

			<?php do_action( "woocommerce_shortcode_before_{$loop_name}_loop" ); ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php do_action( "woocommerce_shortcode_after_{$loop_name}_loop" ); ?>

			<?php
		} else {
			do_action( "woocommerce_shortcode_{$loop_name}_loop_no_results" );
		}

		woocommerce_reset_loop();
		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}
    public static function register_taxonomy () {
        if( function_exists('wc_get_page_id') ) {
            $shop_page_id = wc_get_page_id( 'shop' );
        } else {
            $shop_page_id = woocommerce_get_page_id( 'shop' );
        }
		$base_slug = $shop_page_id > 0 && get_page( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop';
		$category_base = get_option('woocommerce_prepend_shop_page_to_urls') == "yes" ? trailingslashit( $base_slug ) : '';

		register_taxonomy( 'berocket_brand',
			array('product'),
			array(
				'hierarchical'          => true,
				'update_count_callback' => '_update_post_term_count',
				'label'                 => __( 'Brands', 'BeRocket_product_brand_domain'),
				'labels'                => array(
                    'name'                  => __( 'Brands', 'BeRocket_product_brand_domain' ),
                    'singular_name'         => __( 'Brand', 'BeRocket_product_brand_domain' ),
                    'search_items'          => __( 'Search Brands', 'BeRocket_product_brand_domain' ),
                    'all_items'             => __( 'All Brands', 'BeRocket_product_brand_domain' ),
                    'parent_item'           => __( 'Parent Brand', 'BeRocket_product_brand_domain' ),
                    'parent_item_colon'     => __( 'Parent Brand:', 'BeRocket_product_brand_domain' ),
                    'edit_item'             => __( 'Edit Brand', 'BeRocket_product_brand_domain' ),
                    'update_item'           => __( 'Update Brand', 'BeRocket_product_brand_domain' ),
                    'add_new_item'          => __( 'Add New Brand', 'BeRocket_product_brand_domain' ),
                    'new_item_name'         => __( 'New Brand Name', 'BeRocket_product_brand_domain' )
				),
				'show_ui'               => true,
				'show_in_menu'          => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => true,
				'show_in_quick_edit'    => false,
				'meta_box_cb'    => 'post_categories_meta_box',
				'capabilities'          => array(
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms'
				),

				'rewrite' => array( 'slug' => $category_base . 'brands', 'with_front' => true, 'hierarchical' => true )
			)
		);
    }
    public static function init () {
		global $woocommerce;

		add_filter( 'woocommerce_coupon_is_valid', array( __CLASS__, 'validate_coupon' ), null, 2 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( __CLASS__, 'apply_discount' ), null, 5 );

        $options = self::get_option();
        wp_enqueue_script("jquery");
        wp_register_style( 'berocket_product_brand_style', 
            plugins_url( 'css/frontend.css', __FILE__ ), 
            "", 
            BeRocket_product_brand_version );
        wp_enqueue_style( 'berocket_product_brand_style' );
    }
    public static function validate_coupon($valid, $coupon) {
		if ( ! isset( $coupon->in_brands ) && ! isset( $coupon->ex_brands ) ) {
            $in_brands = get_post_meta( $coupon->id, 'berocket_brand', true );
            $ex_brands = get_post_meta( $coupon->id, 'exclude_berocket_brand', true );
            $coupon->in_brands = $in_brands;
            $coupon->ex_brands = $ex_brands;
		} else {
            $in_brands = $coupon->in_brands;
            $ex_brands = $coupon->ex_brands;
        }
        $is_in_brands = ! empty($in_brands) && is_array($in_brands) && count($in_brands) > 0;
        $is_ex_brands = ! empty($ex_brands) && is_array($ex_brands) && count($ex_brands) > 0;
        if ( ! $is_in_brands && ! $is_ex_brands ) {
            return $valid;
        }
        if( ! WC()->cart->is_empty() ) {
            $in_products_match = 0;
            $ex_products_match = 0;
            foreach(WC()->cart->get_cart() as $item) {
                $product_brands = wp_get_post_terms( $item['product_id'], 'berocket_brand', array( 'fields' => 'ids' ) );
                if( $is_in_brands ) {
                    $is_contain = count(array_intersect($product_brands, $in_brands));
                    if ($is_contain) {
                        $in_products_match++;
                    }
                }
                if( $is_ex_brands ) {
                    $is_contain = count(array_intersect($product_brands, $ex_brands));
                    if ($is_contain) {
                        $ex_products_match++;
                    }
                }
            }
        }
        $item_count = count( WC()->cart->get_cart() );
        $applicable = true;
        if( $ex_products_match === $item_count ) {
            $applicable = false;
        }
        if( $in_products_match === 0 ) {
            $applicable = false;
        }
        if ( $coupon->is_type( array( 'fixed_cart', 'percent' ) ) ) {
            if( $in_products_match < $item_count ) {
                $applicable = false;
            }
            if( $ex_products_match > 0 ) {
                $applicable = false;
            }
        }
        if( ! $applicable ) {
            return false;
        }
        return $valid;
    }
    public static function apply_discount($discount, $amount, $cart_item, $single, $coupon) {
		if ( ! is_a( $coupon, 'WC_Coupon' ) || ! $coupon->is_type( array( 'fixed_product', 'percent_product' ) ) ) {
			return $discount;
		}
		if( empty( $coupon->in_brands ) && empty( $coupon->ex_brands ) ) {
            return $discount;
        }

        $product_brands = wp_get_post_terms( $cart_item['product_id'], 'berocket_brand', array( 'fields' => 'ids' ) );
		if ( ! empty( $coupon->in_brands ) && count( array_intersect( $product_brands, $coupon->in_brands ) ) == 0 ) {
			$discount = 0;
		}

		// If our excluded coupon brands are present in the products in our cart, don't assign the discount.
		if ( ! empty( $coupon->ex_brands ) && count( array_intersect( $product_brands, $coupon->ex_brands ) ) > 0 ) {
			$discount = 0;
		}

		return $discount;
    }
    /**
     * Function set styles in wp_head WordPress action
     *
     * @return void
     */
    public static function set_styles () {
        $options = self::get_option();
        echo '<style>'.$options['custom_css'].'</style>';
    }
    /**
     * Load template
     *
     * @access public
     *
     * @param string $name template name
     *
     * @return void
     */
    public static function br_get_template_part( $name = '' ) {
        $template = '';

        // Look in your_child_theme/woocommerce-product_brand/name.php
        if ( $name ) {
            $template = locate_template( "woocommerce-product_brand/{$name}.php" );
        }

        // Get default slug-name.php
        if ( ! $template && $name && file_exists( product_brand_TEMPLATE_PATH . "{$name}.php" ) ) {
            $template = product_brand_TEMPLATE_PATH . "{$name}.php";
        }

        // Allow 3rd party plugin filter template file from their plugin
        $template = apply_filters( 'product_brand_get_template_part', $template, $name );

        if ( $template ) {
            load_template( $template, false );
        }
    }

    public static function admin_enqueue_scripts() {
        if ( function_exists( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        } else {
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'thickbox' );
        }
    }

    /**
     * Function adding styles/scripts and settings to admin_init WordPress action
     *
     * @access public
     *
     * @return void
     */
    public static function admin_init () {
        wp_register_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
        wp_enqueue_style( 'font-awesome' );
        wp_enqueue_script( 'berocket_product_brand_admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), BeRocket_product_brand_version );
        wp_register_style( 'berocket_product_brand_admin_style', plugins_url( 'css/admin.css', __FILE__ ), "", BeRocket_product_brand_version );
        wp_enqueue_style( 'berocket_product_brand_admin_style' );
		add_action( 'berocket_brand_add_form_fields', array( __CLASS__, 'add_field' ) );
		add_action( 'berocket_brand_edit_form_fields', array( __CLASS__, 'edit_field' ), 10, 2 );
		add_action( 'created_term', array( __CLASS__, 'field_save' ), 10, 3 );
		add_action( 'edit_term', array( __CLASS__, 'field_save' ), 10, 3 );
		add_filter( 'woocommerce_product_filters', array( __CLASS__, 'product_filter' ) );
		add_action( 'woocommerce_coupon_options_usage_restriction', array( __CLASS__, 'coupon_field' ) );
		add_action( 'woocommerce_coupon_options_save', array( __CLASS__, 'save_coupon' ) );
    }
    public static function add_field () {
        echo '<table class="form-table">
		<tbody><tr class="form-field term-name-wrap">
			<th scope="row"><label for="name">', __( 'Thumbnail', 'BeRocket_product_brand_domain' ), '</label></th>
			<td><div class="br_brands_image">', berocket_font_select_upload('', 'br_brand_options_ajax_load_icon', 'br_brand_image', '', false), '</div></td>
		</tr>
			</tbody></table>';
    }
    public static function edit_field ( $term, $taxonomy ) {
        $image 	= get_woocommerce_term_meta( $term->term_id, 'brand_image_url', true );
        echo '
        <table class="form-table"><tbody>
            <tr class="form-field term-name-wrap">
                <th scope="row"><label for="name">', __( 'Thumbnail', 'BeRocket_product_brand_domain' ), '</label></th>
                <td><div class="br_brands_image">', berocket_font_select_upload('', 'br_brand_options_ajax_load_icon', 'br_brand_image', @ $image, false), '</div></td>
            </tr>
        </tbody></table>';
    }
    public static function field_save ( $term_id, $tt_id, $taxonomy ) {
        if ( isset( $_POST['br_brand_image'] ) ) {
			update_woocommerce_term_meta( $term_id, 'brand_image_url', $_POST['br_brand_image'] );
		}
    }
    public static function description() {
        if( ! is_tax('berocket_brand') ) {
            return;
        }
		if ( ! get_query_var( 'berocket_brand' ) && ! get_query_var( 'term' ) ) {
			return;
        }
        $term_find = get_query_var( 'berocket_brand' );
        $term_find = ( empty($term_find) ? get_query_var( 'term' ) : $term_find );
        $term = get_term_by( 'slug', $term_find, 'berocket_brand' );
        if( empty($term) ) {
            return;
        }
        $image 	= get_woocommerce_term_meta( $term->term_id, 'brand_image_url', true );
        $options = self::get_option();
        set_query_var( 'display_thumbnail', @ $options['display_thumbnail'] );
        set_query_var( 'width', @ $options['thumbnail_width'] );
        set_query_var( 'align', @ $options['thumbnail_align'] );
        set_query_var( 'display_description', @ $options['display_description'] );
        set_query_var( 'brand_term', @ $term );
        set_query_var( 'brand_image', @ $image );
        self::br_get_template_part( 'description' );
        remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
        remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);
    }
    public static function description_post($atts = array()) {
        $atts = shortcode_atts( array(
			'post_id'   => '',
			'width'     => '35%',
			'height'    => '',
		), $atts );
        if( empty($atts['post_id']) ) {
            $atts['post_id'] = get_the_ID();
            if( empty($atts['post_id']) ) {
                return;
            }
        }
        $terms = get_the_terms($atts['post_id'], 'berocket_brand');
        if( empty($terms) ) {
            return;
        }
        if( ! empty($terms) && is_array($terms) ) {
            foreach($terms as $term) {
                $image 	= get_woocommerce_term_meta( $term->term_id, 'brand_image_url', true );
                if( ! empty($image) ) {
                    echo '<img class="berocket_brand_post_image" src="', $image, '" alt="', $term->name, '" style="', 
                    (empty($atts['width']) ? '' : 'width:'.$atts['width'].';'), 
                    (empty($atts['height']) ? '' : 'height:'.$atts['height'].';'), '">';
                }
            }
        }
    }
    /**
     * Function add options button to admin panel
     *
     * @access public
     *
     * @return void
     */
    public static function options() {
        add_submenu_page( 'woocommerce', __('Product Brands settings', 'BeRocket_product_brand_domain'), __('Product Brands', 'BeRocket_product_brand_domain'), 'manage_options', 'br-product_brand', array(
            __CLASS__,
            'option_form'
        ) );
    }
    /**
     * Function add options form to settings page
     *
     * @access public
     *
     * @return void
     */
    public static function option_form() {
        $plugin_info = get_plugin_data(__FILE__, false, true);
        $paid_plugin_info = self::$info;
        include product_brand_TEMPLATE_PATH . "settings.php";
    }
	public static function product_filter( $filters ) {
		global $wp_query;

		$current_product_brand = (! empty( $wp_query->query['berocket_brand'] ) ? $wp_query->query['berocket_brand'] : '');
		$terms = get_terms( 'berocket_brand' );

		if ( empty($terms) ) {
			return $filters;
		}
		$args                  = array(
			'pad_counts'         => 1,
			'show_count'         => 1,
			'hierarchical'       => 1,
			'hide_empty'         => 1,
			'show_uncategorized' => 1,
			'orderby'            => 'name',
			'selected'           => $current_product_brand,
			'menu_order'         => false
		);

		$filters = $filters . PHP_EOL;
		$filters .= "<select name='berocket_brand' class='dropdown_berocket_brand'>";
		$filters .= '<option value="" ' .  selected( $current_product_brand, '', false ) . '>' . __( 'Select a brand', 'BeRocket_product_brand_domain' ) . '</option>';
		$filters .= wc_walk_category_dropdown_tree( $terms, 0, $args );
		$filters .= "</select>";

		return $filters;
	}
	public static function coupon_field () {
		global $post;
        $categories   = get_terms( 'berocket_brand', 'orderby=name&hide_empty=0' );
		?>
        <div class="options_group">
		<p class="form-field">
            <label for="berocket_brand"><?php _e( 'Product brands', 'BeRocket_product_brand_domain' ); ?></label>
            <select id="berocked_brand" name="berocket_brand[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any brand', 'BeRocket_product_brand_domain' ); ?>">
                <?php
                    $category_ids = (array) get_post_meta( $post->ID, 'berocket_brand', true );
                    if ( $categories && is_array($categories) ) foreach ( $categories as $cat ) {
                        echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
                    }
                ?>
            </select>
            <?php 
            if( function_exists('wc_help_tip') ) {
                echo wc_help_tip( __( 'Products with these brands will be discounted', 'BeRocket_product_brand_domain' ) );
            } ?>
        </p>
		<p class="form-field">
            <label for="exclude_berocket_brand"><?php _e( 'Exclude brands', 'BeRocket_product_brand_domain' ); ?></label>
            <select id="exclude_berocked_brand" name="exclude_berocket_brand[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'No brands', 'BeRocket_product_brand_domain' ); ?>">
                <?php
                    $category_ids = (array) get_post_meta( $post->ID, 'exclude_berocket_brand', true );

                    if ( $categories && is_array($categories) ) foreach ( $categories as $cat ) {
                        echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
                    }
                ?>
            </select>
            <?php 
            if( function_exists('wc_help_tip') ) {
                echo wc_help_tip( __( 'Products with these brands will not be discounted', 'BeRocket_product_brand_domain' ) );
            } ?>
        </p>
        </div>
		<?php
	}
    public static function save_coupon($post_id) {
		$berocket_brand         = empty( $_POST['berocket_brand'] ) ? array() : $_POST['berocket_brand'];
		$exclude_berocket_brand = empty( $_POST['exclude_berocket_brand'] ) ? array() : $_POST['exclude_berocket_brand'];

		// Save
		update_post_meta( $post_id, 'berocket_brand', $berocket_brand );
		update_post_meta( $post_id, 'exclude_berocket_brand', $exclude_berocket_brand );
    }
    /**
     * Function remove settings from database
     *
     * @return void
     */
    public static function deactivation () {
        delete_option( self::$values['settings_name'] );
    }
    public static function save_settings () {
        if( current_user_can( 'manage_options' ) ) {
            if( isset($_POST[self::$values['settings_name']]) ) {
                update_option( self::$values['settings_name'], self::sanitize_option($_POST[self::$values['settings_name']]) );
                echo json_encode($_POST[self::$values['settings_name']]);
            }
        }
        wp_die();
    }

    public static function sanitize_option( $input ) {
        $default = self::$defaults;
        $result = self::recursive_array_set( $default, $input );
        return $result;
    }
    public static function recursive_array_set( $default, $options ) {
        $result = array();
        foreach( $default as $key => $value ) {
            if( array_key_exists( $key, $options ) ) {
                if( is_array( $value ) ) {
                    if( is_array( $options[$key] ) ) {
                        $result[$key] = self::recursive_array_set( $value, $options[$key] );
                    } else {
                        $result[$key] = self::recursive_array_set( $value, array() );
                    }
                } else {
                    $result[$key] = $options[$key];
                }
            } else {
                if( is_array( $value ) ) {
                    $result[$key] = self::recursive_array_set( $value, array() );
                } else {
                    $result[$key] = '';
                }
            }
        }
        foreach( $options as $key => $value ) {
            if( ! array_key_exists( $key, $result ) ) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    public static function get_option() {
        $options = get_option( self::$values['settings_name'] );
        if ( @ $options && is_array ( $options ) ) {
            $options = array_merge( self::$defaults, $options );
        } else {
            $options = self::$defaults;
        }
        return $options;
    }
}

new BeRocket_product_brand;

berocket_admin_notices::generate_subscribe_notice();

/**
 * Creating admin notice if it not added already
 */
new berocket_admin_notices(array(
    'start' => 1505100638, // timestamp when notice start
    'end'   => 1506816001, // timestamp when notice end
    'name'  => 'SALE_LABELS', //notice name must be unique for this time period
    'html'  => 'Only <strong>$9.6</strong> for <strong>Premium</strong> WooCommerce Advanced Product Labels!
            <a class="berocket_button" href="http://berocket.com/product/woocommerce-advanced-product-labels" target="_blank">Buy Now</a>
             &nbsp; <span>Get your <strong class="red">60% discount</strong> and save <strong>$14.4</strong> today</span>
            ', //text or html code as content of notice
    'righthtml'  => '<a class="berocket_no_thanks">No thanks</a>', //content in the right block, this is default value. This html code must be added to all notices
    'rightwidth'  => 80, //width of right content is static and will be as this value. berocket_no_thanks block is 60px and 20px is additional
    'nothankswidth'  => 60, //berocket_no_thanks width. set to 0 if block doesn't uses. Or set to any other value if uses other text inside berocket_no_thanks
    'contentwidth'  => 910, //width that uses for mediaquery is image + contentwidth + rightwidth + 210 other elements
    'subscribe'  => false, //add subscribe form to the righthtml
    'priority'  => 7, //priority of notice. 1-5 is main priority and displays on settings page always
    'height'  => 50, //height of notice. image will be scaled
    'repeat'  => '+2 week', //repeat notice after some time. time can use any values that accept function strtotime
    'repeatcount'  => 2, //repeat count. how many times notice will be displayed after close
    'image'  => array(
        'local' => plugin_dir_url( __FILE__ ) . 'images/ad_white_on_orange.png', //notice will be used this image directly
    ),
));
