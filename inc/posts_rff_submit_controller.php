<?php

if(file_exists(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php')){
    require_once(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php');
}

$rffVal = new PostsRFFValidate();

class PostRffSubmitController{
    private $db;
    private $rffValid;
    function __construct(){
        global $wpdb;
        global $rffVal;
        $this->db = $wpdb;
        $this->rffValid = $rffVal;
    }
    function submitController(){
        $rffValid = $this->rffValid;
        if(isset($_POST['cadastrarPostRff'])){
            if(isset($_POST['titulo']) && isset($_POST['conteudo'])){
                $current_user_id = get_current_user_id();
                $post_data = array(
                    'post_title'    => sanitize_text_field($_POST['titulo']),
                    'post_content'  => $_POST['conteudo'],
                    'post_status'   => $rffValid->post_status($_POST['post_status']),
                    'post_author'   => $current_user_id,
                    'post_type'     => $rffValid->post_type($_POST['post_type']),
                    'post_category' => $rffValid->cats($_POST['cats']) //array(1, 2) IDs das categorias
                );
                if(isset($_POST['cats'])){
                    $post_data['post_category'] = $_POST['cats'];
                }
                $post_id = wp_insert_post($post_data);
                echo $post_id;
                if($post_id>0 && $post_id!=null){
                    echo '<div class="notice notice-success is-dismissible"><p>Post criado com sucesso. ID do post: <a href="'.home_url().'?p='. $post_id.'" target="_blank">'. $post_id.'</a></p></div>';
                }else{
                    echo '<div class="notice notice-failure is-dismissible"><p>Erro ao criar o post, por favor, tente novamente.</p></div>';
                }
            }
        }else if(isset($_POST['editarPostRff'])){
            if(isset($_POST['titulo']) && isset($_POST['conteudo'])){
                // Dados do post a serem atualizados
                $post_data = array(
                    'ID'           => $_POST["idPost"],
                    'post_title'    => sanitize_text_field($_POST['titulo']),
                    'post_content'  => $_POST['conteudo'],
                    'post_status'   => $rffValid->post_status($_POST['post_status']),
                    'post_type'   => $rffValid->post_type($_POST['post_type']),
                );
                if(isset($_POST['cats'])){
                    $post_data['post_category'] = $rffValid->cats($_POST['cats']);
                }

                // Atualiza o post
                $updated_post_id = wp_update_post( $post_data );

                // Verifica se a atualização foi bem-sucedida
                if ( is_wp_error( $updated_post_id ) ) {
                    // Trata o erro
                    $error_message = $updated_post_id->get_error_message();
                    echo '<div class="notice notice-failure is-dismissible"><p>Erro ao atualizar o post: '. $error_message.'</p></div>';
                } else {
                    echo '<div class="notice notice-success is-dismissible"><p>Post atualizado com sucesso. ID do post: <a href="'.home_url().'?p='. $updated_post_id.'" target="_blank">'. $updated_post_id.'</a></p></div>';
                }
            }
        }
    }
}

?>