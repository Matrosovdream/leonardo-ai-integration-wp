<?php
class Leonardo_plugin_updates {

    private $remote_url;
    private $version;

    public function __construct() {

        $this->remote_url = 'https://leonardo.stan-ideas.com/update/';
        $this->version = LEONARDO_PLUGIN_VERSION;

    }

    public function update_plugin() {

        $is_update = $this->check_version();

        if( $is_update ) {
            $this->dowload_updates();
        }

    }

    private function check_version() {

        $new_version = (float) file_get_contents( $this->remote_url.'version.txt' );
        
        if( $new_version != $this->version ) {
            return true;
        } else {
            return false;
        }

    }

    private function dowload_updates() {
    
        $zipContent = file_get_contents( $this->remote_url.'update.zip' );
        $extractDir = LEONARDO_AI_ABSPATH;

        if ($zipContent !== false) {
            // Save the ZIP file locally
            $zipFilePath = LEONARDO_AI_ABSPATH . 'update.zip';
            file_put_contents($zipFilePath, $zipContent);
        
            // Create a ZipArchive object
            $zip = new ZipArchive();
        
            // Open the ZIP file
            if ($zip->open($zipFilePath) === TRUE) {
                // Extract the contents to the specified directory
                $zip->extractTo($extractDir);
        
                // Close the ZipArchive
                $zip->close();

                // Remove the file
                unlink($zipFilePath);
        
                echo 'ZIP file extracted successfully.';
            } else {
                echo 'Failed to open the ZIP file.';
            }
        } else {
            echo 'Failed to download the ZIP file.';
        }

    }

}