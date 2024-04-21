<?php
class Leonardo_API extends Leonardo_HTTP {

    private $api_key;
    private $api_url;
    private $api_url_models;
    private $model_id;
    private $width;
    private $height;
    private $alchemy;
    private $num_images;
    
    public function __construct() {

        $this->api_url = 'https://cloud.leonardo.ai/api/rest/v1/generations/';
        $this->api_url_models = 'https://cloud.leonardo.ai/api/rest/v1/platformModels';

        $this->api_key = get_option('leonardo_api_key');
        $this->model_id = get_option('leonardo_model');
        $this->width = get_option('leonardo_image_width');
        $this->height = get_option('leonardo_image_height');
        $this->alchemy = get_option('leonardo_alchemy');
        $this->num_images = 3;

    }

    public function generate_image( $prompt ) {
    
        // Request data
        $data = array(
            "height" => (int) $this->height,
            "width" => (int) $this->width,
            "modelId" => $this->model_id,
            "prompt" => $prompt,
            "alchemy" => ($this->alchemy) ? true:false,
            "num_images" => $this->num_images
        );

        $response = $this->request( $this->api_url, $this->api_key, $method="POST", $data );

        if( $response['error'] ) {
            return array(
                "error" => $response['error']
            );
        } 
        
        /*
        echo "<pre>";
        print_r($response); 
        echo "</pre>";
        die();
        */

        return $response['sdGenerationJob']['generationId'];
    
        
    
    }

    public function get_images( $generation_id ) {

        $url = $this->api_url.$generation_id;
        $response = $this->request( $url, $this->api_key, $method="GET" );

        $images = $response['generations_by_pk']['generated_images'];

        if( is_array($images) && count($images) ) {
            return $images;
        } else {
            return [];
        }

        echo "<pre>";
        print_r($response); 
        echo "</pre>";

    }

    public function get_models() {

        $response = $this->request( $this->api_url_models, $this->api_key, $method="GET" );

        return $response['custom_models'];

    }

}