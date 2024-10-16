<?php
/*
Plugin Name: POSTS Rff
Description: Cria posts com recurso css em inline para facilitar no uso do GraphQl.
Version: 2.1
Author: Robson Ferreira de Farias
*/

if(!defined('WPINC')){
    die();
}



add_action('wp_insert_post', 'custom_post_meta_rff', 10, 3);

function custom_post_meta_rff($post_id, $post, $update) {
    if ($post->post_type === 'post') { // Verifica se é um post
        add_post_meta($post_id, '_posts_rff', '1', true);
    }
}

add_action('pre_get_posts', 'my_posts_rff_privide');

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


 //Definição das constantes
 define('POSTS_RFF_CORE_INC', dirname(__FILE__).'/inc/'); //Caminho da pasta dos arquivos PHP
 define('POSTS_RFF_DIR_IMG', dirname(__FILE__).'/img/'); //Caminho da pasta das imagens
 define('POSTS_RFF_URL_IMG', plugins_url('img/', __FILE__)); //Caminho da pasta das imagens
 define('POSTS_RFF_URL_INC', plugins_url('inc/', __FILE__)); //Caminho da pasta das imagens
 define('POSTS_RFF_URL_CSS', plugins_url('css/', __FILE__)); //Caminho da pasta dos arquivos CSS
 define('POSTS_RFF_URL_JS', plugins_url('js/', __FILE__)); //Caminho da pasta dos arquivos JS
 define('POSTS_RFF_URL_EDITOR', plugins_url('rffeditor/', __FILE__)); //URL da pasta dos arquivos do editor
 define('POSTS_RFF_DIR_EDITOR', dirname(__FILE__).'/rffeditor/'); //Caminho da pasta dos arquivos do editor


 // Adiciona o CSS e JS
function posts_rff_adicionar_scripts() {
    wp_enqueue_style('posts-rff-editor-p1-css', plugin_dir_url(__FILE__) . 'rffeditor/editorRobsonFarias.css');
    wp_enqueue_style('posts-rff-editor-p2-css', plugin_dir_url(__FILE__) . 'rffeditor/janMovEdiExc.css');
    wp_enqueue_style('posts-rff-editor-p3-css', plugin_dir_url(__FILE__) . 'rffeditor/print.css');
    wp_enqueue_style('posts-rff-modal-css', plugin_dir_url(__FILE__) . 'css/posts_rff_style.css');
    wp_enqueue_script('posts-rff-modal-js', plugin_dir_url(__FILE__) . 'js/posts_rff_functions.js', array('jquery'), null, true);

    wp_enqueue_script('posts-rff-admin-get-url-js', plugin_dir_url(__FILE__) . 'js/posts_rff_admin_get_url.js', array('jquery'), null, true);
  }
  
  add_action('admin_enqueue_scripts', 'posts_rff_adicionar_scripts');

 // Adiciona o CSS e JS
 function posts_rff_adicionar_scripts_wp() {
    wp_enqueue_style('posts-rff-editor-p1-css', plugin_dir_url(__FILE__) . 'rffeditor/editorRobsonFarias.css');
  }
  
  add_action('wp_enqueue_scripts', 'posts_rff_adicionar_scripts_wp');

   /**
   * Includes PHP
   */
if(file_exists(plugin_dir_path(__FILE__).'posts-rff-core.php')){
    require_once(plugin_dir_path(__FILE__).'posts-rff-core.php');
}

