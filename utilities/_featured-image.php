<?php
/*
 * Get the featured image of post and return as object similar to ACF image object
 *
 * @param - $post, $sizes
 * 
 * @return - $image (array)
 */
function get_featured_image($post_id=false) {
	global $post;
	if($post_id) {
		$post=get_post($post_id);
		setup_postdata( $post );
	}
	$sizes = get_intermediate_image_sizes();
	
	$image=false;
	if ( has_post_thumbnail() ) :
 		$image = array(); 
		$post_thumbnail_id = get_post_thumbnail_id( $post );
		$thumb = get_post( $post_thumbnail_id );
	    $image['title'] = $thumb->post_title;
	    $image['alt'] = get_post_meta( $thumb->ID, '_wp_attachment_image_alt', true ); //alt text
	    $image['caption'] = $thumb->post_excerpt;
	    $image['description'] = $thumb->post_content;
	    $image['orientation']='landscape'; 
    	$image['url'] = $thumb->guid;
    	foreach ($sizes as $size) {
			$url_array = wp_get_attachment_image_src($post_thumbnail_id, $size, true);
			$image['sizes'][$size] = $url_array[0]; 
			if ($size==='large') {
		    	$large_width=$url_array[1]; 
		    	$large_height=$url_array[2]; 
		    	if($large_height>$large_width) {
		    		$image['orientation']='portrait'; 
		    	}
			}
    	}
	endif;
	return $image;
}

if (!function_exists('the_image')) {
	function the_image($single_post=true, $display_size='large', $image_class='thumbnail') {
		global $post;

		$image =  get_featured_image();
		if($image):
			$image_small = $image['sizes']['medium_large'];
			$image_large = $image['sizes'][$display_size];
			$image_link = $image['sizes']['large'];	
			?>
			<div class="text-center">
				<a href="<?php echo $image_link ?>" class="image-popup-vertical-fit" title="<?php the_title() ?><?php echo ($image['caption'] ? ' &vert; '.$image['caption']  : '' ) ?>">
					<img class="<?php echo $image_class ?>"  data-interchange="[<?php echo $image_small ?>, small], [<?php echo $image_large; ?>, medium], [<?php echo $image_large; ?>, large]" alt="<?php echo ($image['alt'] ? $image['alt']  : 'Image'); ?>" title="<?php echo ($image['title'] ? $image['title']  : 'defaultImgTitle' ); ?>">
				</a>
			</div>
			<?php 
		endif;
	}
}