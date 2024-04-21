<?php
class Leonardo_CRON {

    private $post_types = [];

    public function __construct() {

        $this->post_types = get_option('leonardo_post_types');

        // For better performance
        $this->set_server_settings();

        add_action('init', array($this, 'leonardo_init_cron') );

    }

    private function set_server_settings() {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

    }

    public function generate_images_posts() {

        $posts = $this->get_generate_posts();

        foreach( $posts as $post ) {

            echo $post->post_title; echo "<br/>";

            $post_leo = new Leonardo_post( $post->ID );
            $generation_id = $post_leo->generate_post_image(); echo $generation_id;
        
        }

    }

    public function retrieve_images_posts() {

        $posts = $this->get_retrieve_posts();

        foreach( $posts as $post ) {
            
            //echo $post->post_title; echo "<br/>";
            $post_leo = new Leonardo_post( $post->ID );
            $post_leo->process_post_images();
  
        }

        //print_r($posts);

    }

    private function get_generate_posts() {

        $args = array(
            'post_type' => 'post', // Specify the post types you want to retrieve
            'post_status' => 'publish', // Retrieve only published posts
            'posts_per_page' => 15, // Retrieve all posts (you can adjust this based on your needs)
        );
    
        $posts = get_posts($args);

        $result = [];

        // Exclude posts that have featured image
        foreach( $posts as $key=>$post ) {

            $generation_id = get_post_meta($post->ID, 'leo_generation_id', true);
            if( $generation_id ) { continue; }

            $force = get_post_meta($post->ID, 'leo_generation_force', true);

            if( !has_post_thumbnail( $post->ID ) || $force ) {
                $result[] = $post;
            }
        }

        return $result;

    }

    private function get_retrieve_posts() {

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 15, 
        );
    
        $posts = get_posts($args);

        $result = [];

        foreach( $posts as $key=>$post ) {

            $generation_id = get_post_meta($post->ID, 'leo_generation_id', true);

            if( $generation_id ) {
                $result[] = $post;
            }
        }

        return $result;

    }

    public function leonardo_init_cron() {

        if( $_GET['cron-generate'] ) {

            $this->generate_images_posts();
            die();
    
        }
    
        if( $_GET['cron-retrieve'] ) {
    
            $this->retrieve_images_posts();
            die();
    
        }

    }

}

new Leonardo_CRON();