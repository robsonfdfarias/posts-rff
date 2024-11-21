<?php

if(file_exists(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php')){
    require_once(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php');
}

$rffVal = new PostsRFFValidate();

class PostsRffDB{
    private $db;
    private $rffValid;
    function __construct(){
        global $wpdb;
        global $rffVal;
        $this->db = $wpdb;
        $this->rffValid = $rffVal;
    }
    function getPostById($id){
        $id = $this->rffValid->validNumber($id);
        if($id!=false){
            $table = $this->db->posts;
            $results = $this->db->get_results("SELECT * FROM $table WHERE id=$id")[0];
            // echo '<pre>'.print_r($results, true).'</pre>';
            return $results;
        }else{
            echo '<div class="notice notice-failure is-dismissible"><p>O idPost precisa ser um número.</p></div>';
            wp_die();
        }
    }
    function printDataInDiv($id){
        $post = $this->getPostById($id);
        $categories = get_the_category($post->ID);
        foreach($categories as $cat){
            $idsCat[] = esc_html($cat->term_id);
        }
        $categ = implode(',', $idsCat);
        $json = '{
            "ID": "'.$post->ID.'",
            "post_author": "'.$post->post_author.'",
            "post_date": "'.$post->post_date.'",
            "post_title": "'.$post->post_title.'",
            "post_status": "'.$post->post_status.'",
            "post_type": "'.$post->post_type.'",
            "post_categories": "'.$categ.'"
        }';
        $div = "<div id='postsRffData' style='display:none;'><div id='postsRffJson'>$json</div><div id='postsRffContent'>$post->post_content</div></div>";
        echo $div;
    }
}


?>