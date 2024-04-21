<?php
class Leonardo_post_helper {

    public function download_all_images( $images, $post_id ) {

        if( count( $images ) == 0 ) { return false; }

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $ids = [];
        foreach( $images as $image ) {

            // Upload the remote image and get the attachment ID
            $attachment_id = media_sideload_image($image['url'], $post_id, '', 'id');

            // Check if the image was uploaded successfully
            if (!is_wp_error($attachment_id)) {
                $ids[] = $attachment_id;
            }

        }

        return $ids;

        echo "<pre>";
        print_r($ids);
        echo "</pre>";

        echo "<pre>";
        print_r($images);
        echo "</pre>";

    }

    public function attach_content_images( $attachments, $post_id ) {

        // Schema
        $tag = "p";
        $schema = array( 6, 12 );

        // Get the post content
        $content = get_post_field('post_content', $post_id);

        $pattern = '/<div class="content-img">(.*?)<\/div>/';
        $content = preg_replace($pattern, '', $content);

        if( $tag == 'p' ) { 
            $pattern = '/<p[^>]*>.*<\/p>/';
            //$pattern = '/<\/p>/';
            $position_offset = 4;
        }

        //$matches_all = [];
        

        foreach( $attachments as $key=>$id ) {

            $arr = preg_split($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
            print_r($arr);

            continue;

            // Exclude empty
            foreach( $matches[0] as $key=>$match ) {
                if( trim($match[0]) === '<p></p>' ) { unset( $matches[0][$key] ); }
            }
            $matches[0] = array_values( $matches[0] );

            
            echo "<pre>";
            print_r($matches);
            echo "</pre>";
            

            // 6th
            $insert_after = $schema[ $key-1 ];
            echo $insert_after;

            if( count($matches[0]) >= $insert_after ) {

                $pos = $matches[0][$insert_after+4][1];

                // If H2 tag is found, insert the image after it
                if ($pos !== false) {
                    // Get the URL of the image you want to insert
                    $image_url = wp_get_attachment_url( $id );

                    // Generate the HTML code for the image
                    $image_html = '<div class="content-img"><img src="' . esc_url($image_url) . '" alt=""></div>';

                    // Insert the image after the first H2 tag
                    $content = substr($content, 0, $pos + $position_offset) . $image_html . substr($content, $pos);
                }

            }

            break;

        }

        //echo $content;

        /*
        wp_update_post(array(
            'ID'           => $post_id,
            'post_content' => $content,
        ));
        */

        echo $content;

    }



}    