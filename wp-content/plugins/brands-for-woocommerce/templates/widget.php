<?php if( $title ) echo $args['before_title'].$title.$args['after_title'];
$args = array(
    'hide_empty' => ( @ $hide_empty ? true : false ),
);
if( ! empty( $count ) ) {
    $args['number'] = $count;
}
if( ! empty( $orderby ) ) {
    $args['orderby'] = $orderby;
}
$terms = get_terms( 'berocket_brand', $args );
if( ! empty($terms) && is_array($terms) && count($terms) > 0 ) {
    foreach($terms as $term) {
        echo '<div class="br_widget_brand_element">';
        if( $use_image ) {
            $image 	= get_woocommerce_term_meta( $term->term_id, 'brand_image_url', true );
            if( ! empty($image) ) {
                echo '<a href="', get_term_link( $term->term_id, 'berocket_brand' ), '"><img style="max-height:', $imgh, 'px;" src="', $image, '" alt="', $term->name, '"></a>';
            }
        }
        if( $use_name ) {
            echo '<a href="', get_term_link( $term->term_id, 'berocket_brand' ), '">', $term->name, '</a>';
        }
        echo '</div>';
    }
    echo '<div style="clear:both;"></div>';
}
?>
