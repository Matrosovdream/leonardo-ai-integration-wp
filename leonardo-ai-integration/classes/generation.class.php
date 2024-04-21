<?php
class Leonardo_post {

    private $API;
    private $post_id;
    private $helper;

    public function __construct( $post_id ) {

        $this->post_id = $post_id;
        $this->API = new Leonardo_API;
        $this->helper = new Leonardo_post_helper;

    }

    public function generate_post_image() {

        $post = $this->get_post();

        $generation_id = $this->API->generate_image( $post->post_title );
        if( $generation_id ) {
            $this->update_post_generation_id( $generation_id );
            update_post_meta( $post->ID, 'leo_generation_force', false);
        }
       

        return $generation_id;
        
    }

    public function process_post_images() {

        $generation_id = $this->get_post_generation_id();
        $images = $this->API->get_images( $generation_id );

        $attachments = $this->helper->download_all_images( $images, $this->post_id );

        if( is_array($attachments) && count($attachments) > 0 ) {

            // Featured image
            $attachment_id = $attachments[0];
            $this->set_featured_image( $attachment_id );
            unset( $attachments[0] );

            // Other images
            //$this->helper->attach_content_images( $attachments,$this->post_id );

             // Remove the generation_id
            $this->update_post_generation_id( $generation_id='' );

        }

    }





    public function full_process() { // Very slow

        $this->generate_post_image();
        sleep(15);
        $this->process_post_images();

    }

    public function update_post_generation_id( $generation_id='' ) {

        update_post_meta( $this->post_id, 'leo_generation_id', $generation_id );

    }

    public function get_post_generation_id() {
        return get_post_meta( $this->post_id, 'leo_generation_id', true);
    }

    public function attach_all_images( $images ) {
        update_post_meta( $this->post_id, 'leo_generated_images', json_encode($images) );
    }

    public function get_all_images() {
        return json_decode( get_post_meta( $this->post_id, 'leo_generated_images', true), true );
    }

    private function set_featured_image( $attachment_id ) {

        // Set the uploaded image as the featured image
        set_post_thumbnail($this->post_id, $attachment_id);

    }

    private function get_post() {
        return get_post( $this->post_id );
    }

}