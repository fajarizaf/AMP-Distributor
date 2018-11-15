<?php
/**
 * Plugin Name: WOO Product Table
 * Plugin URI: https://codersaiful.net/woo-product-table-pro/
 * Description: WooCommerce all products display as a table in one page by shortcode. Fully responsive and mobile friendly. Easily customizable - color,background,title,text color etc.
 * Author: Saiful Islam
 * Author URI: https://codecanyon.net/user/codersaiful
 * Tags: woocommerce product,woocommerce product table, product table
 * 
 * Version: 1.2
 * Requires at least:    4.0.0
 * Tested up to:         4.9.4
 * WC requires at least: 3.0.0
 * WC tested up to: 	 3.3.5
 * 
 * Text Domain: wpt_table
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Defining constant
 */
define( 'WPT_PLUGIN_BASE_FOLDER', plugin_basename( dirname( __FILE__ ) ) );
define( 'WPT_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );
define( "WPT_BASE_URL", WP_PLUGIN_URL . '/'. plugin_basename( dirname( __FILE__ ) ) . '/' );
define( "wpt_dir_base", dirname( __FILE__ ) . '/' );
define( "WPT_BASE_DIR", str_replace( '\\', '/', wpt_dir_base ) );


/**
 * Default Configuration for WOO Product Table
 * 
 * @since 1.0.0 -5
 */
$shortCodeText = 'WPT_SHOP';
//$wpt_ajax_permission_for_plugin = false;  //Has removed.
/**
* Including Plugin file for security
* Include_once
* 
* @since 1.0.0
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$WOO_Table = WOO_Product_Table::getInstance();

/**
 * @since 1.7
 */
WOO_Product_Table::$columns_array =  array(
    'serial_number' => 'SL',
    'thumbnails'    => 'Thumbnails',
    'product_title' => 'Product Title',
    'description'   =>  'Description',
    'category'      => 'Category',
    'tags'          => 'Tags',
    'sku'           => 'SKU',
    'weight'        => 'Weight(kg)',//Only for Paid version: https://codecanyon.net/item/woo-product-table-pro/20676867
    'length'        => 'Length(cm)',//Only for Paid version: https://codecanyon.net/item/woo-product-table-pro/20676867
    'width'         => 'Width(cm)',//Only for Paid version: https://codecanyon.net/item/woo-product-table-pro/20676867
    'height'        => 'Height(cm)',//Only for Paid version: https://codecanyon.net/item/woo-product-table-pro/20676867
    'rating'        => 'Rating',
    'stock'         => 'Stock',//Only for Paid version: https://codecanyon.net/item/woo-product-table-pro/20676867
    'price'         => 'Price',
    'quantity'      => 'Quantity',
    'total'         => 'Total Price', //Only for Paid version: https://codecanyon.net/item/woo-product-table-pro/20676867
    'action'        => 'Action',
    'check'         => 'Check', //Only for Paid version: https://codecanyon.net/item/woo-product-table-pro/20676867
);

/**
 * keyword for disabling from mobile version
 * Not availabel in free version
 * 
 * @since 1.2
 */
WOO_Product_Table::$colums_disable_array = array(
    'serial_number',
    'tags',
    'weight',
    'length',
    'width',
    'height',
);

/**
 * Only one Template available here
 * @link https://codecanyon.net/item/woo-product-table-pro/20676867 For getting All Template, Need Paid version
 */
//Set Style Selection Options.
WOO_Product_Table::$style_form_options = array(
    'none'             =>  'Select None',
    'default'       =>  'Default Style', //Only this template will work in Free version
    'green'         =>  'Green Style',
    'blue'         =>  'Blue Style',
    'dataTable'          =>  'DataTable Default Template',
    'business'      =>  'Classical Business'
);
/**
 * Set ShortCode text as Static Properties
 * 
 * @since 1.0.0 -5
 */
WOO_Product_Table::$shortCode = $shortCodeText;

/**
 * Main Manager Class for WOO Product Table Plugin.
 * All Important file included here.
 * Set Path and Constant also set WOO_Product_Table Class
 * Already set $_instance, So no need again call
 */
class WOO_Product_Table{
    /*
     * List of Path
     * 
     * @since 1.0.0
     * @var array
     */
    protected $paths = array();
    
    /**
     * Set like Constant static array
     * Get this by getPath() method
     * Set this by setConstant() method
     *  
     * @var type array
     */
    private static $constant = array();
    
    public static $shortCode;

    
    /**
     * Only for Admin Section, Collumn Array
     * 
     * @since 1.7
     * @var Array
     */
    public static $columns_array = array();

    
    /**
     * Only for Admin Section, Disable Collumn Array
     * 
     * @since 1.7
     * @var Array
     */
    public static $colums_disable_array = array();

    /**
     * Set Array for Style Form Section Options
     *
     * @var type 
     */
    public static $style_form_options = array();
    
    /**
    * Core singleton class
    * @var self - pattern realization
    */
   private static $_instance;
   
   /**
    * Set Plugin Mode as 1 for Giving Data to UPdate Options
    *
    * @var type Int
    */
   protected static $mode = 1;
   
    /**
    * Get the instane of WOO_Product_Table
    *
    * @return self
    */
   public static function getInstance() {
           if ( ! ( self::$_instance instanceof self ) ) {
                   self::$_instance = new self();
           }

           return self::$_instance;
   }
   
   
   public function __construct() {

       $dir = dirname( __FILE__ ); //dirname( __FILE__ )
       
       /**
        * See $path_args for Set Path and set Constant
        * 
        * @since 1.0.0
        */
       $path_args = array(
           'PLUGIN_BASE_FOLDER' =>  plugin_basename( $dir ),
           'PLUGIN_BASE_FILE' =>  plugin_basename( __FILE__ ),
           'BASE_URL' =>  WP_PLUGIN_URL. '/'. plugin_basename( $dir ) . '/',
           'BASE_DIR' =>  str_replace( '\\', '/', $dir . '/' ),
       );
       /**
        * Set Path Full with Constant as Array
        * 
        * @since 1.0.0
        */
       $this->setPath($path_args);
       
       /**
        * Set Constant
        * 
        * @since 1.0.0
        */
       $this->setConstant($path_args);
       
       //Load File
       if( is_admin() ){
        require_once $this->path('BASE_DIR','admin/plugin_setting_link.php');
        require_once $this->path('BASE_DIR','admin/menu.php');
        require_once $this->path('BASE_DIR','admin/style_js_adding_admin.php');
        require_once $this->path('BASE_DIR','admin/forms_admin.php');
        /**
         * Currently Removed
         * @since 1.7
         */
        //require_once $this->path('BASE_DIR','admin/ajax_table_preview.php');
       }
       
       //Load these bellow file, Only woocommerce installed as well as Only for Front-End
       if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
           require_once $this->path('BASE_DIR','includes/style_js_adding.php');
           require_once $this->path('BASE_DIR','includes/functions.php');
           require_once $this->path('BASE_DIR','includes/ajax_add_to_cart.php'); //Added at V1.0.4 2/5/2018
           require_once $this->path('BASE_DIR','includes/shortcode.php');
           //require_once $this->path('BASE_DIR','includes/hide_on_mobile_css.php'); Removed at 1.0.4
       }else{
           require_once $this->path('BASE_DIR','includes/no_woocommerce.php');
       }
       
       
        //WOO_Product_Table::$_instance;
   }
   /**
    * Set Path
    * 
    * @param type $path_array
    * 
    * @since 1.0.0
    */
   public function setPath( $path_array ) {
       $this->paths = $path_array;
   }
   
   private function setConstant( $contanst_array ) {
       self::$constant = $this->paths;
   }
   /**
    * Set Path as like Constant Will Return Full Path
    * Name should like Constant and full Capitalize
    * 
    * @param type $name
    * @return string
    */
   public function path( $name, $_complete_full_file_path = false ) {
       $path = $this->paths[$name] . $_complete_full_file_path;
       return $path;
   }
   
   /**
    * To Get Full path to Anywhere based on Constant
    * 
    * @param type $constant_name
    * @return type String
    */
   public static function getPath( $constant_name = false ) {
       $path = self::$constant[$constant_name];
       return $path;
   }
   /**
    * Plugin install Static Method
    * Nothing will happend, when Install,
    * Default value has removed from Installation times.
    * 
    * @since 1.0.0
    * @deprecated since 1.7 1.7_2_17.5.2018
    */
   public static function install() {
       //Nothing for now
   }
   
   /**
    * Plugin Uninsall Activation Hook 
    * Static Method
    * 
    * @since 1.0.0
    */
   public function uninstall() {
       //Nothing for now
   }
   
   /**
    * Getting full Plugin data. We have used __FILE__ for the main plugin file.
    * 
    * @since V 1.5
    * @return Array Returnning Array of full Plugin's data for This Woo Product Table plugin
    */
   public static function getPluginData(){
       return get_plugin_data( __FILE__ );
   }
   
   /**
    * Getting Version by this Function/Method
    * 
    * @return type static String
    */
   public static function getVersion() {
       $data = self::getPluginData();
       return $data['Version'];
   }
   
   /**
    * Getting Version by this Function/Method
    * 
    * @return type static String
    */
   public static function getName() {
       $data = self::getPluginData();
       return $data['Name'];
   }
   

}

//   $WOO_Table = WOO_Product_Table::getInstance();

/**
* Plugin Install and Uninstall
*/
register_activation_hook(__FILE__, array( 'WOO_Product_Table','install' ) );
register_deactivation_hook( __FILE__, array( 'WOO_Product_Table','uninstall' ) );
