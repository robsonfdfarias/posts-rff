<?php

function register_in_graphql_posts_rff() {
    register_graphql_object_type('CustomTableTypesPostsRffItems', [
        'fields' => [
            'id' => [
                'type' => 'ID',
                'description' => __('ID do item', 'your-textdomain'),
            ],
            'post_author' => [
                'type' => 'String',
                'description' => __('Quem criou o post', 'your-textdomain'),
            ],
            'post_date' => [
                'type' => 'String',
                'description' => __('Data do post', 'your-textdomain'),
            ],
            'post_date_gmt' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_content' => [
                'type' => 'String',
                'description' => __('Conteúdo do post', 'your-textdomain'),
            ],
            'post_title' => [
                'type' => 'String',
                'description' => __('Título do post', 'your-textdomain'),
            ],
            'post_excerpt' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_status' => [
                'type' => 'String',
                'description' => __('O status do post', 'your-textdomain'),
            ],
            'comment_status' => [
                'type' => 'String',
                'description' => __('Status do comentário', 'your-textdomain'),
            ],
            'ping_status' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_password' => [
                'type' => 'String',
                'description' => __('Senha do post', 'your-textdomain'),
            ],
            'post_name' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'to_ping' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'pinged' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_modified' => [
                'type' => 'String',
                'description' => __('Data da modificação', 'your-textdomain'),
            ],
            'post_modified_gmt' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_content_filtered' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_type' => [
                'type' => 'String',
                'description' => __('Tipo de post', 'your-textdomain'),
            ],
            'post_mime_type' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
        ]
    ]);

    register_graphql_field('RootQuery', 'postsRff', [
        'type' => ['list_of' => 'CustomTableTypesPostsRffItems'],
        'description' => __('Posts criados pelo meu plugin'),
        'args' => [
            'id' => [
                'type' => 'ID',
                'description' => __('ID od the item', 'your-textdomain'),
            ],
            'post_author' => [
                'type' => 'String',
                'description' => __('Quem criou o post', 'your-textdomain'),
            ],
            'post_date' => [
                'type' => 'String',
                'description' => __('Data do post', 'your-textdomain'),
            ],
            'post_date_gmt' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_content' => [
                'type' => 'String',
                'description' => __('Conteúdo do post', 'your-textdomain'),
            ],
            'post_title' => [
                'type' => 'String',
                'description' => __('Título do post', 'your-textdomain'),
            ],
            'post_excerpt' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_status' => [
                'type' => 'String',
                'description' => __('O status do post', 'your-textdomain'),
            ],
            'comment_status' => [
                'type' => 'String',
                'description' => __('Status do comentário', 'your-textdomain'),
            ],
            'ping_status' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_password' => [
                'type' => 'String',
                'description' => __('Senha do post', 'your-textdomain'),
            ],
            'post_name' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'to_ping' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'pinged' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_modified' => [
                'type' => 'String',
                'description' => __('Data da modificação', 'your-textdomain'),
            ],
            'post_modified_gmt' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_content_filtered' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
            'post_type' => [
                'type' => 'String',
                'description' => __('Tipo de post', 'your-textdomain'),
            ],
            'post_mime_type' => [
                'type' => 'String',
                'description' => __('...', 'your-textdomain'),
            ],
        ],
        'resolve' => function($root, $args, $context, $info) {
            global $wpdb;
            $table = $wpdb->prefix.'posts';

            $meta_key = '_posts_rff'; // Chave do meta
            $query_items = "
                SELECT p.ID as id, p.*
                FROM $wpdb->posts p
                JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
                WHERE pm.meta_key = %s
            ";

            $where = [];
            if(!empty($args['id'])){
                $where[] = $wpdb->prepare('p.ID = %d', $args['id']);
                // $query_items.= " AND ID = ".$args['id'];
            }
            if(!empty($args['post_author'])){
                $where[] = $wpdb->prepare('p.post_author = %s', $args['post_author']);
            }
            if(!empty($args['post_date'])){
                $where[] = $wpdb->prepare('p.post_date like %s', '%'.$args['post_date'].'%');
            }

            if(!empty($args['post_date_gmt'])){
                $where[] = $wpdb->prepare('p.post_date_gmt like %s', '%'.$args['post_date_gmt'].'%');
            }
            if(!empty($args['post_content'])){
                $where[] = $wpdb->prepare('p.post_content like %s', '%'.$args['post_content'].'%');
            }
            if(!empty($args['post_title'])){
                $where[] = $wpdb->prepare('p.post_title like %s', '%'.$args['post_title'].'%');
            }
            if(!empty($args['post_excerpt'])){
                $where[] = $wpdb->prepare('p.post_excerpt like %s', '%'.$args['post_excerpt'].'%');
            }
            if(!empty($args['post_status'])){
                $where[] = $wpdb->prepare('p.post_status like %s', '%'.$args['post_status'].'%');
            }
            if(!empty($args['comment_status'])){
                $where[] = $wpdb->prepare('p.comment_status like %s', '%'.$args['comment_status'].'%');
            }
            if(!empty($args['ping_status'])){
                $where[] = $wpdb->prepare('p.ping_status like %s', '%'.$args['ping_status'].'%');
            }
            if(!empty($args['post_password'])){
                $where[] = $wpdb->prepare('p.post_password like %s', '%'.$args['post_password'].'%');
            }
            if(!empty($args['post_name'])){
                $where[] = $wpdb->prepare('p.post_name like %s', '%'.$args['post_name'].'%');
            }
            if(!empty($args['to_ping'])){
                $where[] = $wpdb->prepare('p.to_ping like %s', '%'.$args['to_ping'].'%');
            }
            if(!empty($args['pinged'])){
                $where[] = $wpdb->prepare('p.pinged like %s', '%'.$args['pinged'].'%');
            }
            if(!empty($args['post_modified'])){
                $where[] = $wpdb->prepare('p.post_modified like %s', '%'.$args['post_modified'].'%');
            }
            if(!empty($args['post_modified_gmt'])){
                $where[] = $wpdb->prepare('p.post_modified_gmt like %s', '%'.$args['post_modified_gmt'].'%');
            }
            if(!empty($args['post_content_filtered'])){
                $where[] = $wpdb->prepare('p.post_content_filtered like %s', '%'.$args['post_content_filtered'].'%');
            }
            if(!empty($args['post_type'])){
                $where[] = $wpdb->prepare('p.post_type like %s', '%'.$args['post_type'].'%');
            }
            if(!empty($args['post_mime_type'])){
                $where[] = $wpdb->prepare('p.post_mime_type like %s', '%'.$args['post_mime_type'].'%');
            }

            $where_sql = '';
            if(!empty($where) && sizeof($where)>0){
                $where_sql = ' AND '.implode(' AND ', $where);
                $query_items.=$where_sql;
            }

            $q = $wpdb->prepare($query_items, $meta_key);
            $retorno = $wpdb->get_results($q);
            return $retorno;

        }
    ]);
};

?>