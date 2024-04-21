<?php
class Leonardo_HTTP {

    private $response = [];

    protected function request( $api_url, $api_key, $method, $data=array() ) {

        // Initialize cURL session
        $ch = curl_init();

        // Request headers
        $headers = [
            'content-type: application/json',
            'accept: application/json',
            'Authorization: Bearer ' . $api_key,
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if( $method == 'POST' ) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // Execute CURL session
        $response = curl_exec($ch);
        // Close cURL session
        curl_close($ch);

        if( $response ) {

            $this->response = $response;
            $this->process_response();
            return $this->response;

        } else {
            return array("error" => "API error");
        }       

    }

    private function process_response() {
        $this->response = json_decode( $this->response, true );
    }

}