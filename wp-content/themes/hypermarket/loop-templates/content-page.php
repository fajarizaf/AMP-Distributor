<?php
/**
 * The template used for displaying page content in page.php and fluid width template.
 *
 * @see 			http://codex.wordpress.org/Template_Hierarchy
 * @author  		Mahdi Yazdani
 * @package 		Hypermarket
 * @since 		    1.0.4.2
 */
/**
 * Functions hooked into "hypermarket_featured_image_single_page" action
 *
 * @hooked hypermarket_featured_image_background_single_page        - 10
 * @since 1.0
 */
do_action('hypermarket_featured_image_single_page');
?>
<!-- Content -->

<?php
function is_page_child($pid) {// $pid = The ID of the page we're looking for pages underneath
  global $post;         // load details about this page
  $anc = get_post_ancestors( $post->ID );
  foreach($anc as $ancestor) {
      if(is_page() && $ancestor == $pid) {
          return true;
      }
  }
  if(is_page()&&(is_page($pid)))
     return true;   // we're at the page or at a sub page
  else
      return false;  // we're elsewhere
};

global $wp_query;
$parameter = $wp_query->query_vars;
?>

<?php if($parameter['pagename'] != 'dashboard') { ?>
<div class="container-fluid head-page">
	<h2 class="mobile-center">
		<div>
	    <?php if(is_page_child($post->ID) == true) { echo get_the_title( $post->post_parent ); } ?>
		</div>
		<?php the_title(); ?>	
	</h2>
</div>
<?php } ?>


<section class="container<?php echo (apply_filters('hypermarket_fluid_template', false)) ? '-fluid' : ''; ?> <?php if($parameter['pagename'] != 'dashboard') { ?> padding-top  <?php } ?>">
	<div id="page-<?php the_ID(); ?>" <?php if($parameter['pagename'] != 'dashboard') { ?> <?php post_class('row padding-top'); } else { post_class('row'); }?>>
		<?php
			/**
			 * Functions hooked into "hypermarket_before_single_page_content" action
			 *
			 * @hooked hypermarket_before_single_page_content_wrapper_start     - 20
			 * @since 1.0
			 */
			do_action('hypermarket_before_single_page_content');
			while (have_posts()):
				the_post();
				the_content();
				/**
				 * Functions hooked into "hypermarket_end_single_page_content" action
				 *
				 * @hooked hypermarket_single_page_paging     - 10
				 * @since 1.0.4.2
				 */
				do_action('hypermarket_end_single_page_content');
			endwhile;
			/**
			 * Functions hooked into "hypermarket_after_single_page_content" action
			 *
			 * @hooked hypermarket_after_single_page_content_wrapper_end       - 30
			 * @since 1.0
			 */
			do_action('hypermarket_after_single_page_content');
		?>
	</div><!-- .row -->
	<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ):
			comments_template();
		endif;
	?>
</section><!-- .container -->
