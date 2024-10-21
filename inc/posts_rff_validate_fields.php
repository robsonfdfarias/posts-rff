<?php

class PostsRFFValidate {
    function __construct(){}

    function post_status($valor){
        $array = array('publish', 'draft', 'pending', 'private');
        $vv = sanitize_key($valor);
        if(in_array($vv, $array, true)){
            return $vv;
        }else{
            wp_die('Status inválido!');
        }
    }
    function post_type($valor){
        $array = array('post', 'page', 'attachment', 'revision', 'custom_post_type');
        $vv = sanitize_key($valor);
        if(in_array($vv, $array, true)){
            return $vv;
        }else{
            wp_die('Tipo de post inválido!');
        }
    }
    function cats($ar){
        for($i=0;$i<count($ar); $i++){
            if(!preg_match('/\d+/', $ar[$i])){
                wp_die('Formato inválido das categorias!');
            }
        }
        return $ar;
    }
    // function validFilterDate($month, $year){
    //     echo preg_match('/\d{2}/', $month).' <------<br>';
    //     echo preg_match('/\d{4}/', $year).' <------<br>';
    //     if(preg_match('/\d{2}/', $month) && preg_match('/\d{4}/', $year)){
    //         return [$month, $year];
    //     }else{
    //         return false;
    //     }
    // }
}