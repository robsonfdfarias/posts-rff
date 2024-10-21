<?php

if(!defined('WPINC')){
    die();
 }

function posts_rff_install(){
    // ação que deve ocorrer na instalação do plugin
 }


function posts_rff_uninstall(){
    global $wpdb;
    $table = $wpdb->prefix.'posts';

    $meta_key = '_posts_rff'; // Chave do meta
    $query_items = "
        SELECT p.ID as id, p.*
        FROM $wpdb->posts p
        JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
    ";
    $q = $wpdb->prepare($query_items, $meta_key);
    $retorno = $wpdb->get_results($q);
    for($i=0; $i<count($retorno); $i++){
        $wpdb->delete($wpdb->postmeta, ['post_id'=>$retorno[$i]->id]);
        wp_delete_post($retorno[$i]->id, true); // O segundo parâmetro 'true' força a exclusão permanente
    }
 }


 //Adiciona um meta_query ao posts que são criados pelo plugin
function custom_post_meta_rff($post_id, $post, $update) {
    if ($post->post_type === 'post' && isset($_GET['page']) && $_GET['page']=='Posts_Rff') { // Verifica se é um post
        add_post_meta($post_id, '_posts_rff', '1', true);
    }
}

//essa função faz com que o plugin do post padrão do WP não pegue os posts criados pelo meu plugin.
//e no meu plugin ele aparece apenas os posts criados por ele mesmo.
function my_posts_rff_privide($query) {
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'post') {
        // Verifique se um parâmetro específico está na URL
        if (!isset($_GET['page'])) {
            $query->set('meta_query', array(
                array(
                    'key' => '_posts_rff',
                    'compare' => 'NOT EXISTS'
                )
            ));
        }else if(isset($_GET['page']) && $_GET['page']=='Posts_Rff'){
            $query->set('meta_query', array(
                array(
                    'key' => '_posts_rff',
                    'compare' => 'EXISTS'
                )
            ));
        }
    }
}