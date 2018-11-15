<?php
/**
 * Hypermarket functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * Do not add any custom code here.
 * Please use a custom plugin or child theme so that your customizations aren't lost during updates.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 *
 * @see 		https://codex.wordpress.org/Theme_Development
 * @see 		https://codex.wordpress.org/Plugin_API
 * @author  	Mahdi Yazdani
 * @package 	Hypermarket
 * @since 		1.0.5.1
 */
// Assign the "Hypermarket" info to constants.
$hypermarket_theme = wp_get_theme('hypermarket');
define('HypermarketThemeName', $hypermarket_theme->get('Name'));
define('HypermarketThemeURI', $hypermarket_theme->get('ThemeURI'));
define('HypermarketThemeAuthor', $hypermarket_theme->get('Author'));
define('HypermarketThemeAuthorURI', $hypermarket_theme->get('AuthorURI'));
define('HypermarketThemeVersion', $hypermarket_theme->get('Version'));
// Hypermarket only works in WordPress 4.4 or later.
if (version_compare($GLOBALS['wp_version'], '4.4-alpha', '<')):
	require get_template_directory() . '/includes/back-compat.php';

endif;
/**
 * Theme setup and custom theme supports.
 * Theme Customizer.
 * Bootstrap NavWalker.
 * Payment method icons widget.
 * Social icons widget.
 *
 * @since 1.0
 */
$hypermarket = (object)array(
	// Theme setup and custom theme supports.
	'setup' => require get_template_directory() . '/includes/classes/class-hypermarket.php',

	// Customizer Additions.
	'customizer' => require get_template_directory() . '/includes/classes/class-customizer.php',

	// Bootstrap NavWalker.
	'navwalker' => require get_template_directory() . '/includes/classes/class-bootstrap-navwalker.php',

	// Payment method icons widget.
	'payment-method-icons' => require get_template_directory() . '/includes/classes/class-payment-method-icons-widget.php',

	// Social icons widget.
	'payment-method-icons' => require get_template_directory() . '/includes/classes/class-social-icons-widget.php',
);
/**
 * Custom functions that act independently of the theme templates.
 *
 * @since 1.0
 */
require get_template_directory() . '/includes/extras.php';

/**
 * Template hooks.
 *
 * @since 1.0
 */
require get_template_directory() . '/includes/template-hooks.php';

/**
 * Template functions.
 *
 * @since 1.0
 */
require get_template_directory() . '/includes/template-functions.php';

/**
 * Load WooCommerce functions.
 *
 * @since 1.0
 */
if (hypermarket_is_woocommerce_activated()):
	// 3 Columns (Default)
	$product_grid_classes = 'col-md-4 col-sm-6';
	$hypermarket->woocommerce = require get_template_directory() . '/includes/classes/class-woocommerce.php';

	// Custom functions that act independently of the theme templates.
	require get_template_directory() . '/includes/woocommerce-extras.php';

	// WooCommerce template Hooks.
	require get_template_directory() . '/includes/woocommerce-template-hooks.php';

	// WooCommerce template functions.
	require get_template_directory() . '/includes/woocommerce-template-functions.php';

endif;
/**
 * Hypermarket welcome screen.
 *
 * @since 1.0.5.1
 */
if (current_user_can('manage_options')):
	require get_template_directory() . '/includes/classes/class-hypermarket-welcome-screen.php';
endif;

function wpb_widgets_init() {

	register_sidebar( array(
		'name'          => 'Widget Filter',
		'id'            => 'widget-filter',
		'before_widget' => '<div class="widget-filter">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );


	register_sidebar( array(
		'name'          => 'Widget desc home',
		'id'            => 'widget-desc-home',
		'before_widget' => '<div class="widget-desc-home">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );


	register_sidebar( array(
		'name'          => 'Widget desc home image',
		'id'            => 'widget-desc-home-image',
		'before_widget' => '<div class="widget-desc-home-image">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );



}
add_action( 'widgets_init', 'wpb_widgets_init' );


add_action( 'init', 'Kendaraan' );
function Kendaraan() {
 $labels = array(
    'name' => _x( 'Merek Kendaraan', 'nama umum taxonomy' ),
    'singular_name' => _x( 'Merek Kendaraan', 'nama tunggal taxonomy' ),
    'search_items' =>  __( 'Cari Merek Kendaraan' ),
    'all_items' => __( 'Semua Merek Kendaraan' ),
    'parent_item' => __( 'Induk Merek Kendaraan' ),
    'parent_item_colon' => __( 'Induk Merek Kendaraan' ),
    'edit_item' => __( 'Ubah Merek Kendaraan' ),
    'update_item' => __( 'Perbaharui Merek Kendaraan' ),
    'add_new_item' => __( 'Tambah Merek Kendaraan' ),
    'new_item_name' => __( 'Tambah Merek Kendaraan' ),
  );


  register_taxonomy('kendaraan','product',array(
    'hierarchical' => true,
    'labels' => $labels,
    			'show_ui'               => true,
				'show_in_menu'          => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => true,
				'show_in_quick_edit'    => false,
				'capabilities'          => array(
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms'
				)
  ));
}


add_action( 'init', 'Tipe_Kendaraan' );
function Tipe_Kendaraan() {
 $labels = array(
    'name' => _x( 'Tipe Kendaraan', 'nama umum taxonomy' ),
    'singular_name' => _x( 'Tipe Kendaraan', 'nama tunggal taxonomy' ),
    'search_items' =>  __( 'Cari Tipe Kendaraan' ),
    'all_items' => __( 'Semua Tipe Kendaraan' ),
    'parent_item' => __( 'Induk Tipe Kendaraan' ),
    'parent_item_colon' => __( 'Induk Tipe Kendaraan' ),
    'edit_item' => __( 'Ubah Tipe Kendaraan' ),
    'update_item' => __( 'Perbaharui Tipe Kendaraan' ),
    'add_new_item' => __( 'Tambah Tipe Kendaraan' ),
    'new_item_name' => __( 'Tambah Tipe Kendaraan' ),
  );     

  register_taxonomy('tipe_kendaraan','product',array(
    'hierarchical' => true,
    'labels' => $labels,
    			'show_ui'               => true,
				'show_in_menu'          => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => true,
				'show_in_quick_edit'    => false,
				'capabilities'          => array(
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms'
				)
  ));
} 

add_action( 'init', 'claim' );
function claim() {

   $labels = array(
    'name' => _x('Claim', 'nama umum tipe content'),
    'singular_name' => _x('Klaim Produk', 'nama tunggal tipe content'),
    'add_new' => _x('Tambah Baru', 'Klaim Produk'),
    'add_new_item' => __('Tambah Klaim Produk'),
    'edit_item' => __('Ubah Klaim Produk'),
    'new_item' => __('Klaim Produk Baru'),
    'view_item' => __('Lihat Klaim Produk'),
    'search_items' => __('Cari Klaim Produk'),
    'not_found' =>  __('Tidak ada Klaim Produk ditemukan'),
    'not_found_in_trash' => __('Tidak ada Klaim Produk ditemukan pada tempat sampah'),
    'parent_item_colon' => ''
  );
  
   $supports = array('title', 'editor', 'custom-fields','excerpt','thumbnail');

   $capabilities = array(
        'edit_post'          => 'edit_claim',
        'read_post'          => 'read_claim',
        'delete_post'        => 'delete_claim',
        'edit_posts'         => 'edit_claims',
        'edit_others_posts'  => 'edit_other_claims',
        'delete_posts'       => 'delete_claims',
        'publish_posts'      => 'publish_claims',
        'read_private_posts' => 'read_private_claims'
    );
   
   register_post_type( 'claim',
    array(
      'labels' => $labels,
      'public' => true,
      'supports' => $supports,
      'hierarchical' => true,
    )
); }




add_action( 'init', 'purchase_order' );
function purchase_order() {

   $labels = array(
    'name' => _x('Purchase Order', 'nama umum tipe Permintaan pembelian'),
    'singular_name' => _x('Purchase Order', 'nama tunggal tipe Permintaan pembelian'),
    'add_new' => _x('Tambah', 'Permintaan pembelian'),
    'add_new_item' => __('Tambah Permintaan pembelian'),
    'edit_item' => __('Ubah Permintaan pembelian'),
    'new_item' => __('Permintaan pembelian Baru'),
    'view_item' => __('Lihat PPermintaan pembelian'),
    'search_items' => __('Cari Pemesanan'),
    'not_found' =>  __('Tidak ada Permintaan pembelian ditemukan'),
    'not_found_in_trash' => __('Tidak ada Permintaan pembelian ditemukan pada tempat sampah'),
    'parent_item_colon' => ''
  );
  
   $supports = array('title', 'editor', 'custom-fields','excerpt','thumbnail');

   $capabilities = array(
        'edit_post'          => 'edit_purchase_order',
        'read_post'          => 'read_purchase_order',
        'delete_post'        => 'delete_purchase_order',
        'edit_posts'         => 'edit_purchase_order',
        'edit_others_posts'  => 'edit_other_purchase_order',
        'delete_posts'       => 'delete_purchase_order',
        'publish_posts'      => 'publish_purchase_order',
        'read_private_posts' => 'read_private_purchase_order'
    );
   
   register_post_type( 'purchase_order',
    array(
      'labels' => $labels,
      'public' => true,
      'supports' => $supports,
      'hierarchical' => true,
      'capabilities' => $capabilities,
    )
); }



add_filter( 'manage_edit-claim_sortable_columns', 'my_sortable_date_column' );
function my_sortable_date_column( $columns ) {
	$columns['title'] = 'Subject';
    $columns['customer_name'] = 'Nama Pelanggan';
    $columns['email_address'] = 'Alamat Email';
    $columns['telephone'] = 'Nomor Telp';
    $columns['city'] = 'City';
    $columns['date'] = 'Tanggal Submit';
    return $columns;
}


function my_page_columns($columns) {
    $columns = array(
    	'title'     => 'Subject',
        'customer_name'     => 'Nama Pelanggan',
        'issue_description' => 'Alasan Klaim',
        'status' 			=> 'Status Klaim',
    );
    return $columns;
}


function my_page_columnss($columns) {
    $columns = array(
    	'title'     					=> 'No PO',
        'nama_kustomer'     			=> 'Nama Pelanggan',
        'total_yang_harus_dibayarkan' 	=> 'Total',
        'date' 							=> 'Tanggal Pemesanan',
        'status' 						=> 'status',
    );
    return $columns;
}



$role_object = get_role( 'shop_manager' );
$role_object->add_cap( 'edit_theme_options' );


function hide_menu() {

    if (current_user_can('shop_manager')) {

        remove_submenu_page( 'themes.php', 'themes.php' ); // hide the theme selection submenu
        remove_submenu_page( 'themes.php', 'widgets.php' ); // hide the widgets submenu
        remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2Ftools.php' ); // hide the customizer submenu
        remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2Ftools.php&#038;autofocus%5Bcontrol%5D=background_image' ); // hide the background submenu


        // these are theme-specific. Can have other names or simply not exist in your current theme.
        remove_submenu_page( 'themes.php', 'yiw_panel' );
        remove_submenu_page( 'themes.php', 'custom-header' );
        remove_submenu_page( 'themes.php', 'custom-background' );

    }
}

add_action('admin_head', 'hide_menu');

if (current_user_can('shop_manager')) {
	add_action( 'admin_menu', function () {
	global $submenu;
	if ( isset( $submenu[ 'themes.php' ] ) ) {
	    foreach ( $submenu[ 'themes.php' ] as $index => $menu_item ) {
	        foreach ($menu_item as $value) {
	            if (strpos($value,'customize') !== false) {
	                unset( $submenu[ 'themes.php' ][ $index ] );
	            }
	        }
	    }
	}
	});
	add_action( 'admin_init', 'wpse_136058_remove_menu_pages' );

	function wpse_136058_remove_menu_pages() {
		remove_menu_page( 'edit.php?post_type=acf' );
		remove_menu_page( 'acf' );
	    remove_menu_page( 'admin.php?page=woo-product-table' );
	    remove_menu_page( 'woo-product-table' );
	}

	function remove_menus(){
	  remove_menu_page( 'woocommerce' );
	  remove_menu_page( 'options-general.php' );
	  remove_menu_page( 'tools.php' );
	  remove_menu_page( 'import.php' );
	  remove_menu_page( 'export.php' );
	}
	add_action( 'admin_menu', 'remove_menus' );

	function wooninja_remove_items() {
	 $remove = array( 'wc-settings', 'wc-status', 'wc-addons', 'woocommerce-role-based-price-settings','br-product_brand' );
	  foreach ( $remove as $submenu_slug ) {
	   if ( ! current_user_can( 'update_core' ) ) {
	    remove_submenu_page( 'woocommerce', $submenu_slug );
	   }
	  }
	}

	add_action( 'admin_menu', 'wooninja_remove_items', 99, 0 );

	add_filter( 'parse_query', 'exclude_pages_from_admin' );
	function exclude_pages_from_admin($query) {
	    global $pagenow,$post_type;
	    if ($pagenow=='edit.php' && $post_type =='page') {
	        $query->query_vars['post__not_in'] = array('5','6','7','111','14');
	    }
	}


}

add_action( 'admin_menu', 'rename_woocoomerce_admin_menu', 999 );
function rename_woocoomerce_admin_menu()
{
    global $menu;
    // Pinpoint menu item
    $woo = recursive_array_search_php( 'WooCommerce', $menu );
    // Validate
    if( !$woo )
        return;
    $menu[$woo][0] = 'E-Commerce';
}

// http://www.php.net/manual/en/function.array-search.php#91365
function recursive_array_search_php( $needle, $haystack )
{
    foreach( $haystack as $key => $value )
    {
        $current_key = $key;
        if(
            $needle === $value
            OR (
                is_array( $value )
                && recursive_array_search_php( $needle, $value ) !== false
            )
        )
        {
            return $current_key;
        }
    }
    return false;
}


    function remove_metabox_for_non_admin_and_editor() {
        remove_menu_page( 'tools.php' );
        remove_menu_page( 'edit-tags.php' );
    }
    add_action( 'admin_menu', 'remove_metabox_for_non_admin_and_editor' );

function my_custom_columns($column) {
    global $post;
    if ($column == 'customer_name') {
        echo get_field( "customer_name", $post->ID );
    }
    else if($column == 'issue_description') {
        echo get_field( "issue_description", $post->ID );
    }
    else if($column == 'status') {
    	echo get_field( "status", $post->ID );
    }
}

add_action("manage_claim_posts_custom_column", "my_custom_columns");
add_filter("manage_claim_posts_columns", "my_page_columns");

function my_custom_columnss($column) {
    global $post;
    if ($column == 'nama_kustomer') {
        echo get_field( "nama_kustomer", $post->ID );
    }
    else if($column == 'total_yang_harus_dibayarkan') {
        echo get_field( "total_yang_harus_dibayarkan", $post->ID );
    }
    else if($column == 'status') {
    	echo get_field( "status", $post->ID );
    }
}

add_action("manage_purchase_order_posts_custom_column", "my_custom_columnss");
add_filter("manage_purchase_order_posts_columns", "my_page_columnss");

function wpb_list_child_pages() { 
	global $post; 
	$childpages = wp_list_pages( 'sort_column=menu_order&title_li=&child_of=236&echo=0' );
	if ( $childpages ) {
	 
	    $string = '<ul>' . $childpages . '</ul>';
	}
	return $string;
}


add_image_size( 'custom-size', 230, 230 );


function get_list_merek_kendaraan() {
	$categories = get_terms( array( 
	    'taxonomy' 	 => 'kendaraan',
	    'hide_empty' => false
	) );
		foreach ($categories as $cat) {
			if($cat->slug != $_POST['selected']) {
				$option .= '<option value="'.$cat->slug.'">';
				$option .= $cat->name;
				$option .= '</option>';
			}
		}
		if($_POST['selected'] != '0') {
			$name = get_term_by('slug', $_POST['selected'] , 'kendaraan');
			echo '<option value="'.$_POST['selected'].'" selected="selected">'.$name->name.'</option>'.$option;
		} else {
			echo $option;
		}
	die();
}
add_action('wp_ajax_get_list_merek_kendaraan', 'get_list_merek_kendaraan');
add_action('wp_ajax_nopriv_get_list_merek_kendaraan', 'get_list_merek_kendaraan');//for users that are not logged in.


function get_default_tipe_kendaraan() {
	$categories = get_terms( array( 
	    'taxonomy' 	 => 'tipe_kendaraan',
	    'parent'   	 => 0,
	    'hide_empty' => false
	) );
		foreach ($categories as $pcat) {
			$subcategories = get_terms( array('taxonomy' => 'tipe_kendaraan','parent' => $pcat->term_id,'hide_empty' => false ));
			foreach ($subcategories as $cat) {
				if($cat->slug != $_POST['selected']) {
					$option .= '<option value="'.$cat->slug.'">';
					$option .= $cat->name;
					$option .= '</option>';
				}
			}
		}
		if($_POST['selected'] != '0') {
			$name = get_term_by('slug', $_POST['selected'] , 'tipe_kendaraan');
			echo '<option value="'.$_POST['selected'].'" selected="selected">'.$name->name.'</option>'.$option;
		} else {
			echo $option;
		}
	die();
}
add_action('wp_ajax_get_default_tipe_kendaraan', 'get_default_tipe_kendaraan');
add_action('wp_ajax_nopriv_get_default_tipe_kendaraan', 'get_default_tipe_kendaraan');//for users that are not 


function get_tipe_kendaraan() {
	$parent = get_term_by('slug', $_POST['parent'] , 'tipe_kendaraan');
	$categories = get_terms( array( 
	    'taxonomy' 	 => 'tipe_kendaraan',
	    'parent'   	 => $parent->term_id,
	    'hide_empty' => false
	) );
		foreach ($categories as $cat) {
			if($cat->slug != $_POST['selected']) {
				$option .= '<option value="'.$cat->slug.'">';
				$option .= $cat->name;
				$option .= '</option>';
			}
		}
		if($_POST['selected'] != '0') {
			$name = get_term_by('slug', $_POST['selected'] , 'tipe_kendaraan');
			echo '<option value="'.$_POST['selected'].'" selected="selected">'.$name->name.'</option>'.$option;
		} else {
			echo $option;
		}
	die();
}
add_action('wp_ajax_get_tipe_kendaraan', 'get_tipe_kendaraan');
add_action('wp_ajax_nopriv_get_tipe_kendaraan', 'get_tipe_kendaraan');//for users that are not


function get_list_product() {
	$args = array(
        'post_type'      => 'product',
        'tax_query'      => array(
        array(
	            'taxonomy'      => 'product_cat',
	            'field' 		=> 'name', 
	            'terms'         => $_POST['category'],
	            'operator'      => 'IN'
	        )
	    )
    );

    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        $option .= '<option value="'.get_the_title().'">';
		$option .= get_the_title();
		$option .= '</option>';
    endwhile;
    echo $option;
    wp_reset_query();
	die();
}
add_action('wp_ajax_get_list_product', 'get_list_product');
add_action('wp_ajax_nopriv_get_list_product', 'get_list_product');//for users that are not


function wpb_woo_my_account_order() {
	$myorder = array(
		'dashboard'          => __( 'Dashboard', 'woocommerce' ),
		'orders'             => __( 'Pemesanan', 'woocommerce' ),
		'klaim'              => __( 'Klaim Garansi', 'woocommerce' ),
		'purchase-order'     => __( 'Purchase Order', 'woocommerce' ),
		'edit-address'       => __( 'Alamat Pengiriman', 'woocommerce' ),
		'edit-account'       => __( 'Detail Akun', 'woocommerce' ),
	);
	return $myorder;
}
add_filter ( 'woocommerce_account_menu_items', 'wpb_woo_my_account_order' );


function my_custom_endpoints() {
    add_rewrite_endpoint( 'klaim', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'form-klaim', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'purchase-order', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'purchase-order-form', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'my_custom_endpoints' );

function my_custom_query_vars( $vars ) {
    $vars[] = 'klaim';
    $vars[] = 'form-klaim';
    $vars[] = 'purchase-order';
    $vars[] = 'purchase-order-form';

    return $vars;
}

add_filter( 'query_vars', 'my_custom_query_vars', 0 );

function my_custom_flush_rewrite_rules() {
    flush_rewrite_rules();
}

add_action( 'wp_loaded', 'my_custom_flush_rewrite_rules' );


function my_custom_endpoint_content() {
    include 'woocommerce/myaccount/klaim.php'; 
}

add_action( 'woocommerce_account_klaim_endpoint', 'my_custom_endpoint_content' );

function klaim_form() {
    include 'woocommerce/myaccount/form-klaim.php'; 
}

add_action( 'woocommerce_account_form-klaim_endpoint', 'klaim_form' );


function content_purchase_form() {
    include 'woocommerce/myaccount/purchase-order-form.php'; 
}

add_action( 'woocommerce_account_purchase-order-form_endpoint', 'content_purchase_form' );

function content_purchase() {
    include 'woocommerce/myaccount/purchase-order.php'; 
}

add_action( 'woocommerce_account_purchase-order_endpoint', 'content_purchase' );


// WooCommerce Checkout Fields Hook
add_filter( 'woocommerce_checkout_fields' , 'custom_wc_checkout_fields' );
// Change order comments placeholder and label, and set billing phone number to not required.
function custom_wc_checkout_fields( $fields ) {
    $fields['billing']['billing_phone']['placeholder'] = 'Nomor yang bisa dihubungi';
    $fields['billing']['billing_first_name']['placeholder'] = 'Nama Panggilan';
    $fields['billing']['billing_last_name']['placeholder'] = 'Nama kepanjangan';
    $fields['billing']['billing_state']['placeholder'] = 'Negara';
    $fields['billing']['billing_company']['placeholder'] = 'Perusahaan';

    $fields['shipping']['shipping_first_name']['placeholder'] = 'Nama Panggilan';
    $fields['shipping']['shipping_last_name']['placeholder'] = 'Nama kepanjangan';
    $fields['shipping']['shipping_company']['placeholder'] = 'Nama perusahaan';
    $fields['shipping']['shipping_state']['placeholder'] = 'Negara';
    $fields['shipping']['shipping_address_1']['placeholder'] = 'Alamat Pertama';
    $fields['shipping']['shipping_address_2']['placeholder'] = 'Alamat Kedua';

    $fields['account']['account_username']['placeholder'] = 'Nama Pengguna';
    $fields['account']['account_password']['placeholder'] = 'Kata Sandi';
    $fields['account']['account_password-2']['placeholder'] = 'Konfirmasi Kata Sandi';

    $fields['order']['order_comments']['placeholder'] = _x('catatan tentang pesanan Anda, misalnya catatan khusus untuk pengiriman', 'placeholder', 'woocommerce');
    return $fields;
}

add_filter('woocommerce_default_address_fields', 'override_address_fields');
function override_address_fields( $address_fields ) {
	$address_fields['first_name']['placeholder'] = 'Nama Panggilan';
	$address_fields['last_name']['placeholder'] = 'Nama Kepanjangan';
	$address_fields['company']['placeholder'] = 'Perusahaan';
	$address_fields['city']['placeholder'] = 'Kota';
	$address_fields['postcode']['placeholder'] = 'Kode Pos';
	$address_fields['phone']['placeholder'] = 'Nomor yang dapat dihubungi';
	$address_fields['address_1']['placeholder'] = 'Alamat Pertama';
	$address_fields['address_2']['placeholder'] = 'Alamat Kedua';
	return $address_fields;
}


add_action( 'admin_menu', 'myprefix_remove_meta_box');  
function myprefix_remove_meta_box(){  
   remove_meta_box('my-tax-metabox-id', 'post', 'normal');  
}  


/**
 * Export Kendaraan
 */
/**
* Add CSV columns for exporting extra data.
*
* @param  array  $columns
* @return array  $columns
*/
function export_add_columns( $columns ) {
	$columns[ 'kendaraan'] = 'Merek Kendaraan';
	$columns[ 'tipe_kendaraan'] = 'Tipe Kendaraan';
	return $columns;
}
add_filter( 'woocommerce_product_export_column_names', 'export_add_columns' );
add_filter( 'woocommerce_product_export_product_default_columns', 'export_add_columns' );
/**
* MnM contents data column content.
*
* @param  mixed       $value
* @param  WC_Product  $product
* @return mixed       $value
*/
function add_export_data_kendaraan( $value, $product ) {
	$terms = get_terms( array( 'object_ids' => $product->get_ID(), 'taxonomy' => 'kendaraan' ) );
	if ( ! is_wp_error( $terms ) ) {
		$data = array();
		foreach ( (array) $terms as $term ) {
			$termd = get_term_by( 'id', $term->term_id, 'kendaraan' );
			$data[] = $termd->name;
		}
		$value = json_encode( $data );
	}
	return $value;
}
add_filter( 'woocommerce_product_export_product_column_kendaraan', 'add_export_data_kendaraan', 10, 2 );

function add_export_data_tipe_kendaraan( $value, $product ) {
	$terms = get_terms( array( 'object_ids' => $product->get_ID(), 'taxonomy' => 'tipe_kendaraan' ) );
	if ( ! is_wp_error( $terms ) ) {
		$data = array();
		foreach ( (array) $terms as $term ) {
			$termd = get_term_by( 'id', $term->term_id, 'tipe_kendaraan' );
			$data[] = $termd->name;
		}
		$value = json_encode( $data );
	}
	return $value;
}
add_filter( 'woocommerce_product_export_product_column_tipe_kendaraan', 'add_export_data_tipe_kendaraan', 10, 2 );
/**
 * Import
 */
/**
 * Register the 'Custom Column' column in the importer.
 *
 * @param  array  $options
 * @return array  $options
 */
function import_map_columns( $options ) {
	$options[ 'kendaraan' ] = 'Merek Kendaraan';
	$options[ 'tipe_kendaraan' ] = 'Tipe Kendaraan';
	return $options;
}
add_filter( 'woocommerce_csv_product_import_mapping_options', 'import_map_columns' );
/**
 * Add automatic mapping support for custom columns.
 *
 * @param  array  $columns
 * @return array  $columns
 */
function kia_add_columns_to_mapping_screen( $columns ) {
	$columns['Merek Kendaraan'] 	= 'kendaraan';
	$columns['Tipe Kendaraan'] 		= 'tipe_kendaraan';
	return $columns;
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'kia_add_columns_to_mapping_screen' );
/**
 * Decode data items and parse JSON IDs.
 *
 * @param  array                    $parsed_data
 * @param  WC_Product_CSV_Importer  $importer
 * @return array
 */
function kia_parse_taxonomy_json( $parsed_data, $importer ) {
	if ( ! empty( $parsed_data[ 'kendaraan' ] ) ) {
		$data = json_decode( $parsed_data[ 'kendaraan' ], true );
		unset( $parsed_data[ 'kendaraan' ] );
		if ( is_array( $data ) ) {
			$parsed_data[ 'kendaraan' ] = array();
			foreach ( $data as $term_id ) {
				$parsed_data[ 'kendaraan' ][] = $term_id;
			}
		}
	}
	if ( ! empty( $parsed_data[ 'tipe_kendaraan' ] ) ) {
		$datas = json_decode( $parsed_data[ 'tipe_kendaraan' ], true );
		unset( $parsed_data[ 'tipe_kendaraan' ] );
		if ( is_array( $datas ) ) {
			$parsed_data[ 'tipe_kendaraan' ] = array();
			foreach ( $datas as $term_ids ) {
				$parsed_data[ 'tipe_kendaraan' ][] = $term_ids;
			}
		}
	}
	return $parsed_data;
}
add_filter( 'woocommerce_product_importer_parsed_data', 'kia_parse_taxonomy_json', 10, 2 );
/**
 * Set taxonomy.
 *
 * @param  array  $parsed_data
 * @return array
 */
function kia_set_taxonomy( $product, $data ) {
	if ( is_a( $product, 'WC_Product' ) ) {
		if( ! empty( $data[ 'kendaraan' ] ) ) {
			wp_set_object_terms( $product->get_id(),  (array) $data[ 'kendaraan' ], 'kendaraan' );
		}
		if( ! empty( $data[ 'tipe_kendaraan' ] ) ) {
			wp_set_object_terms( $product->get_id(),  (array) $data[ 'tipe_kendaraan' ], 'tipe_kendaraan' );
		}
	}
	return $product;
}
add_filter( 'woocommerce_product_import_inserted_product_object', 'kia_set_taxonomy', 10, 2 );




/**
 *Export Custom Fields
 * Add the custom column to the exporter and the exporter column menu.
 *
 * @param array $columns
 * @return array $columns
 */
function add_export_column( $columns ) {

	// column slug => column name
	$columns['Ah'] = 'Ah Rate';

	return $columns;
}
add_filter( 'woocommerce_product_export_column_names', 'add_export_column' );
add_filter( 'woocommerce_product_export_product_default_columns', 'add_export_column' );

/**
 * Provide the data to be exported for one item in the column.
 *
 * @param mixed $value (default: '')
 * @param WC_Product $product
 * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
 */
function add_export_data( $value, $product ) {
	$value = $product->get_meta( 'Ah', true, 'edit' );
	return $value;
}
// Filter you want to hook into will be: 'woocommerce_product_export_product_column_{$column_slug}'.
add_filter( 'woocommerce_product_export_product_column_Ah', 'add_export_data', 10, 2 );





/**
 * Import Custom Fields
 * Register the 'Custom Column' column in the importer.
 *
 * @param array $options
 * @return array $options
 */
function add_column_to_importer( $options ) {

	// column slug => column name
	$options['Ah'] = 'Ah Rate';

	return $options;
}
add_filter( 'woocommerce_csv_product_import_mapping_options', 'add_column_to_importer' );

/**
 * Add automatic mapping support for 'Custom Column'. 
 * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
 *
 * @param array $columns
 * @return array $columns
 */
function add_column_to_mapping_screen( $columns ) {
	
	// potential column name => column slug
	$columns['Ah Rate'] = 'Ah';

	return $columns;
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'add_column_to_mapping_screen' );

/**
 * Process the data read from the CSV file.
 * This just saves the value in meta data, but you can do anything you want here with the data.
 *
 * @param WC_Product $object - Product being imported or updated.
 * @param array $data - CSV data read for the product.
 * @return WC_Product $object
 */
function process_import( $object, $data ) {
	
	if ( ! empty( $data['Ah'] ) ) {
		$object->update_meta_data( 'Ah', $data['Ah'] );
	}

	return $object;
}
add_filter( 'woocommerce_product_import_pre_insert_product_object', 'process_import', 10, 2 );

