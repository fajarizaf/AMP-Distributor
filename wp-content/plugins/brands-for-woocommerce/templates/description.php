<?php
if( ! @ $display_thumbnail && ! @ $display_description ) {
    return;
}
echo '<div class="berocket_brand_description">';
if( @ $display_thumbnail && ! empty($brand_image) ) {
    echo '<img src="', $brand_image, '" alt="', $brand_term->name, '" style="', ( empty($width) ? '' : 'width:'.$width.';' ), ( empty($align) ? '' : 'float:'.$align.';' ), '">';
}
if( @ $display_description ) {
    echo '<div class="text">'. apply_filters('content', term_description()).'</div>';
}
echo '<div style="clear:both;"></div>';
echo '</div>';
?>