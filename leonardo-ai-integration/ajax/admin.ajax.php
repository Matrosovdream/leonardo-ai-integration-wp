<?php
add_action( 'admin_print_footer_scripts', 'api_quick_test_javascript', 99 );
function api_quick_test_javascript() {
	?>
	<script>
	jQuery(document).ready( function( $ ){

        jQuery('.api-test').click(function() {

            var data = {
                action: 'api_quick_test',
            };

            jQuery.post( ajaxurl, data, function( response ){
                jQuery('.api-test-result').html(response);
            });

            return false;

        });

		
	} );
	</script>
	<?php
}


add_action( 'wp_ajax_api_quick_test', 'api_quick_test_callback' );
add_action( 'wp_ajax_nopriv_api_quick_test', 'api_quick_test_callback' );
function api_quick_test_callback(){
	
    $LEO = new Leonardo_API;
    $res = $LEO->generate_image( 'Test image' );

    if( isset($res['error']) ) {
        echo "Error: ".$res['error'];
    } else {
        echo "Generation ID: ".$res;
    }
    
	wp_die();
}


add_action( 'wp_enqueue_scripts', 'myajax_data', 99 );
function myajax_data(){

	wp_localize_script( 'leonardo-script', 'myajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);

}