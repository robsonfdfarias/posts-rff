<?php

if(!defined('WPINC')){
    die();
 }

function posts_rff_install(){
    // global $wpdb;
    // require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // $charset_collate = $wpdb->get_charset_collate();
    // $table_categ = $wpdb->prefix.DOWNLOAD_RFF_TABLE_CATEG;
    // $sqlCateg = "CREATE TABLE IF NOT EXISTS $table_categ (
    //     id mediumint(9) NOT NULL AUTO_INCREMENT,
    //     title varchar(200) NOT NULL,
    //     statusItem varchar(20),
    //     PRIMARY KEY (id)
    // ) $charset_collate;";
    // dbDelta($sqlCateg);
    // $table_item = $wpdb->prefix.DOWNLOAD_RFF_TABLE_ITEMS;
    // $sqlItem = "CREATE TABLE IF NOT EXISTS $table_item (
    //     id mediumint(9) NOT NULL AUTO_INCREMENT,
    //     title varchar(200) NOT NULL,
    //     content TEXT NOT NULL,
    //     urlPage varchar(200) NOT NULL,
    //     urlDoc varchar(200) NOT NULL,
    //     startDate varchar(20) NOT NULL,
    //     endDate varchar(20) NOT NULL,
    //     category mediumint(9) NOT NULL,
    //     clicks varchar(200) NOT NULL,
    //     tags TEXT NOT NULL,
    //     statusItem varchar(20),
    //     dateUp varchar(20),
    //     orderItems mediumint(9),
    //     PRIMARY KEY (id)
    // ) $charset_collate;";
    // dbDelta($sqlItem);
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