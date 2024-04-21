<?php
class Leonardo_Init {

    public function __construct() {

        $this->include_classes();
        $this->include_hooks();
        $this->include_ajax();
        $this->include_cron();

    }

    private function include_classes() {

        require_once( LEONARDO_AI_ABSPATH.'classes/generation.class.php' );
        require_once( LEONARDO_AI_ABSPATH.'classes/http.class.php' );
        require_once( LEONARDO_AI_ABSPATH.'classes/api.class.php' );
        require_once( LEONARDO_AI_ABSPATH.'classes/settings.class.php' );

    }

    private function include_hooks() {

        require_once( LEONARDO_AI_ABSPATH.'hooks/post.hooks.php' );

    }

    private function include_ajax() {

        require_once( LEONARDO_AI_ABSPATH.'ajax/admin.ajax.php' );

    }

}