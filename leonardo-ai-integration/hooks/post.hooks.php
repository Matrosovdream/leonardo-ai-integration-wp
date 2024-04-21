<?php
function check_values($post_ID, $post_after, $post_before){
    if ($post_before->post_title != $post_after->post_title) { 
        // Update the meta value
        update_post_meta($post_ID, 'leo_generation_force', true);
    }   
}
add_action( 'post_updated', 'check_values', 10, 3 );