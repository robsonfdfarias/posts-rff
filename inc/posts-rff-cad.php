<?php
header('Content-Type: application/json');

// if(!defined('WPINC')){
//     die();
// }

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// $response = [
//     'status' => 'success',
//     'message' => 'Data processed successfully'
// ];

// echo json_encode($response);

if(isset($_POST['title']) && isset($_POST['content'])){
    $post_data = array(
        'post_title'    => 'Título do Post',
        'post_content'  => 'Conteúdo do post gerado pelo editor de blocos',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'post',
    );
    
    $post_id = wp_insert_post($post_data);
    $response = [
        'status' => 'success',
        // 'received_data' => $post_id ?? 'No data received'
        'received_data' => 'Cadastrado'
    ];
    echo json_encode($response);
}else{
    
    $response = [
        'status' => 'failed',
        'received_data' => 'Variaveis vazias'
    ];
    echo json_encode($response);
}
