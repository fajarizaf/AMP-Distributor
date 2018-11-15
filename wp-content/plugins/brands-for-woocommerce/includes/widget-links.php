<?php
class BeRocket_product_brand_Widget extends WP_Widget 
{
    public static $defaults = array(
        'title'         => '',
        'use_image'     => '1',
        'use_name'      => '',
        'per_row'       => '3',
        'hide_empty'    => '1',
        'count'         => '',
        'orderby'       => 'name',
        'slider'        => '',
        'padding'       => '3px',
        'border_color'  => '',
        'border_width'  => '',
        'imgh'          => '64',
    );
	public function __construct() {
        parent::__construct("berocket_product_brand_widget", "WooCommerce Product Brands",
            array("description" => ""));
    }
    /**
     * WordPress widget
     */
    public function widget($args, $instance)
    {
        $instance = wp_parse_args( (array) $instance, self::$defaults );
        $options = BeRocket_product_brand::get_option();
        $instance['title'] = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
        set_query_var( 'title', apply_filters( 'product_brand_widget_title', $instance['title'] ) );
        set_query_var( 'use_image', apply_filters( 'product_brand_widget_title', $instance['use_image'] ) );
        set_query_var( 'use_name', apply_filters( 'product_brand_widget_title', $instance['use_name'] ) );
        set_query_var( 'hide_empty', apply_filters( 'product_brand_widget_title', $instance['hide_empty'] ) );
        set_query_var( 'imgh', apply_filters( 'product_brand_widget_title', $instance['imgh'] ) );
        set_query_var( 'orderby', apply_filters( 'product_brand_widget_title', $instance['orderby'] ) );
        set_query_var( 'args', $args );
        ob_start();
        BeRocket_product_brand::br_get_template_part( apply_filters( 'product_brand_widget_template', 'widget' ) );
        $content = ob_get_clean();
        if( $content ) {
            echo $args['before_widget'];
            echo $content;
            echo $args['after_widget'];
        }
	}
    /**
     * Update widget settings
     */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['use_image'] = strip_tags( $new_instance['use_image'] );
		$instance['use_name'] = strip_tags( $new_instance['use_name'] );
		$instance['hide_empty'] = strip_tags( $new_instance['hide_empty'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['imgh'] = strip_tags( $new_instance['imgh'] );
		return $instance;
	}
    /**
     * Widget settings form
     */
	public function form($instance)
	{
        $instance = wp_parse_args( (array) $instance, self::$defaults );
		$title = strip_tags($instance['title']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
        <p>
            <label><input type="checkbox" value="1" name="<?php echo $this->get_field_name('use_image'); ?>"<?php if(@ $instance['use_image']) echo ' checked'; ?>><?php _e( 'Display image', 'BeRocket_product_brand_domain' ); ?></label>
        </p>
        <p>
            <label><?php _e( 'Maximum image height', 'BeRocket_product_brand_domain' ); ?></label>
            <input type="number" value="<?php echo $instance['imgh']; ?>" name="<?php echo $this->get_field_name('imgh'); ?>">
        </p>
        <p>
            <label><input type="checkbox" value="1" name="<?php echo $this->get_field_name('use_name'); ?>"<?php if(@ $instance['use_name']) echo ' checked'; ?>><?php _e( 'Display name', 'BeRocket_product_brand_domain' ); ?></label>
        </p>
        <p>
            <label><input type="checkbox" value="1" name="<?php echo $this->get_field_name('hide_empty'); ?>"<?php if(@ $instance['hide_empty']) echo ' checked'; ?>><?php _e( 'Hide empty', 'BeRocket_product_brand_domain' ); ?></label>
        </p>
        <p>
            <label><?php _e( 'Order brands by', 'BeRocket_product_brand_domain' ); ?></label>
            <select name="<?php echo $this->get_field_name('orderby'); ?>">
                <?php
                $orderby = array(
                    'name' => __( 'Brand name', 'BeRocket_product_brand_domain' ),
                    'count' => __( 'Count of products', 'BeRocket_product_brand_domain' ),
                );
                foreach($orderby as $orderby_id => $ordeby_name) {
                    echo '<option value="', $orderby_id, '"', ($orderby_id == $instance['orderby'] ? 'selected' : ''), '>', $ordeby_name, '</option>';
                }
                ?>
            </select>
        </p>
		<?php
	}
}
?>
