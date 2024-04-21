<?php
class Leonardo_dom_content {

    private $post_id;
    private $images;
    private $content;
    private $images_order=[];
    private $tag;

    public function __construct( $post_id ) {

        $this->post_id = $post_id;
        $this->content = $this->get_content();
        $this->images = $this->get_images();

        $this->tag = get_option('leonardo_ai_content_tag');
        $this->image_order = get_option('leonardo_ai_content_tag_order');

    }

    public function process_content() {

        if( 
            $this->tag == '' ||
            count($this->image_order) == 0
            ) { return false; }

        $content = $this->content;
        $images = $this->images;
        $order = $this->image_order;

        $content = $this->remove_ai_images($content);

        $content = $this->insert_images($content, $images, $tag=$this->tag, $order);
        if( $content ) {
            $this->save_post($content);
        }

        //echo $content;

    }

    private function insert_images($content, $images, $tag="p", $order) {

        $dom = new DOMDocument();
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
        $paragraphs = $dom->getElementsByTagName( $tag );
    
        // Find the non-empty paragraphs
        $nonEmptyParagraphs = array();
        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph->textContent) !== '') {
                $nonEmptyParagraphs[] = $paragraph;
            }
        }

        foreach( $images as $key=>$image ) {

            $after = $order[$key];
            if( !$after ) { continue; }
    
            // Insert image after paragraph 3
            if (count($nonEmptyParagraphs) >= $after) {
                $imageTag = $dom->createElement('img');
                $imageTag->setAttribute('src', $image);
        
                $imageDiv = $dom->createElement('div');
                $imageDiv->setAttribute('class', 'post-image');
                $imageDiv->appendChild($imageTag);
        
                $newParagraph = $dom->createElement('p');
                $newParagraph->appendChild($imageDiv);

                $nonEmptyParagraphs[$after-1]->parentNode->insertBefore($newParagraph, $nonEmptyParagraphs[$after-1]->nextSibling);
            }

        }
    
        // Save and output modified HTML
        $htmlWithImages = $dom->saveHTML();
    
        // Remove unwanted doctype and html/body tags added by DOMDocument
        $htmlWithImages = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $htmlWithImages);
    
        return $htmlWithImages;

    }

    private function remove_ai_images($html) {
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
        $xpath = new DOMXPath($dom);
        $postImageDivs = $xpath->query('//div[@class="post-image"]');
    
        // Remove all post-image divs
        foreach ($postImageDivs as $postImageDiv) {
            $postImageDiv->parentNode->removeChild($postImageDiv);
        }
    
        // Save and output modified HTML
        $htmlWithoutPostImages = $dom->saveHTML();
    
        // Remove unwanted doctype and html/body tags added by DOMDocument
        $htmlWithoutPostImages = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $htmlWithoutPostImages);
    
        return $htmlWithoutPostImages;
    }

    private function get_images() {

        $ids = get_post_meta( $this->post_id, 'leo_generated_images', true );

        $images = array();
        foreach( $ids as $id ) {
            $images[] = wp_get_attachment_image_url( $id, 'full' );
        }

        return $images;

    }

    private function get_content() {

        $post = get_post( $this->post_id );
        return $post->post_content;

    }

    private function save_post($content) {

        wp_update_post(array(
            'ID'           => $this->post_id,
            'post_content' => $content,
        ));

    }

}