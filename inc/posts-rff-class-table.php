<?php

// Inclui a classe WP_List_Table
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if(file_exists(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php')){
    require_once(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php');
  }

class Posts_RFF_Posts_Table extends WP_List_Table {
    private $posts;

    function __construct() {
        parent::__construct([
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => true
        ]);
    }

    function get_columns() {
        return [
            'cb' => '', // Checkbox para seleção
            'title' => 'Título',
            'author' => 'Autor',
            'categories' => 'Categorias',
            'tags' => 'Tags',
            'comments' => 'Comentários',
            'date' => 'Data'
        ];
    }

    function getMonthAndYear() {
        global $wpdb;
    
        // Consultar os meses e anos que têm posts publicados
        $results = $wpdb->get_results("
            SELECT DISTINCT YEAR(p.post_date) AS year, MONTH(p.post_date) AS month
            FROM $wpdb->posts p
            JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
            WHERE post_type = 'post' AND post_status = 'publish' AND pm.meta_key = '_posts_rff'
            ORDER BY post_date DESC
        ");
    
        // Criar um array para armazenar os meses e anos
        $meses_e_anos = array();
    
        foreach ($results as $result) {
            $meses_e_anos[] = array(
                'year' => intval($result->year),
                'month' => intval($result->month),
            );
        }
    
        return $meses_e_anos;
    }
    
    function prepare_items() {
        $this->trashPost();
        $rffValid = new PostsRffValidate();
        // Captura os filtros
        $filter_category = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
        $filter_date = isset($_GET['m']) ? sanitize_text_field($_GET['m']) : '';

        // Argumentos para a consulta
        $args = [
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => '_posts_rff', //Pega só os posts com meta_query _posts_rff que foram criados pelo plugin atual
                    'compare' => 'EXISTS' // Para pegar apenas os posts do plugin
                )
            )
        ];

        // Filtrar por data
        if (!empty($_GET['m']) && $_GET['m'] != '0' && intval($_GET['m']) != 0) {
            $year_month = intval($_GET['m']);
            $year = floor($year_month / 100);
            $month = $year_month % 100;
            $args['date_query'] = array(
                array(
                    'year'  => $year,
                    'month' => $month,
                ),
            );
        }

        // Filtrar por categoria
        if (!empty($_GET['cat']) && $_GET['cat'] != '0' && intval($_GET['cat'])!=0) {
            $args['cat'] = intval($_GET['cat']);
        }

        $this->posts = get_posts($args);

        // Verifica se há posts
        $this->items = !empty($this->posts) ? $this->posts : [];
    }

    function display_filters() {
        ?>
        <form method="post" id="filters_posts">
            <input type="hidden" name="page" value="lista-de-posts" />

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Selecionar ação em massa</label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="-1">Ações em massa</option>
                    <!-- <option value="trash">Mover para lixeira</option> -->
                    <option value="trash">Excluir sem mover para lixeira</option>
                </select>
                <input type="submit" id="doaction" class="button action" value="Aplicar">
            </div>

            <div class="alignleft actions">
                <label for="filter-by-date" class="screen-reader-text">Filtrar por data</label>
                <select name="m" id="filter-by-date">
                    <option selected="selected" value="0">Todas as datas</option>
                    <?php
                    $datePosts = $this->getMonthAndYear();
                    $convertMonth = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
                    foreach($datePosts as $dp){
                        // echo $dp['month'];
                        // echo $convertMonth[$dp['month']];
                        echo '<option value="' . esc_attr($dp['year']).$convertMonth[$dp['month']] . '">' . esc_html(date_i18n('F Y', mktime(0, 0, 0, $dp['month'], 1, $dp['year']))) . '</option>';
                    }
                    ?>
                </select>

                <label class="screen-reader-text" for="cat">Filtrar por categoria</label>
                <select name="cat" id="cat" class="postform">
                    <option value="0">Todas as categorias</option>
                    <?php
                    $categories = get_categories(['hide_empty' => false]);
                    foreach ($categories as $category) {
                        echo '<option class="level-0" value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>

                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filtrar">
                <input type="submit" name="clear_filter" id="clear_filter" class="button" value="Limpar filtros">
            </div>
            <br class="clear">
        <!-- </form> -->
        <?php
    }

    function trashPost(){
        if(isset($_POST['action']) && isset($_POST['post_id'])){
            if(count($_POST['post_id'])>0 && $_POST['action']==='trash'){
                foreach($_POST['post_id'] as $post_id){
                    // wp_trash_post(intval($post_id)); //manda para lixeira
                    wp_delete_post(intval($post_id), true); // apaga definitivamente
                }
            }
        }
    }

    function column_cb($post) {
        return '<input type="checkbox" onclick="removeSelectionGeneralCheckbox()" class="checkboxpostsrff" name="post_id[]" value="' . $post->ID . '" />';
    }

    function column_title($post) {
        // $categories = get_the_category($post->ID);
        // foreach($categories as $cat){
        //     $idsCat[] = esc_html($cat->term_id);
        // }
        // $categ = implode(',', $idsCat);
        // $p = '{
        //     "ID": "'.$post->ID.'",
        //     "post_author": "'.$post->post_author.'",
        //     "post_date": "'.$post->post_date.'",
        //     "post_title": "'.$post->post_title.'",
        //     "post_status": "'.$post->post_status.'",
        //     "post_type": "'.$post->post_type.'",
        //     "post_categories": "'.$categ.'"
        // }';
        $status = ["draft"=>"Rascunho", "pending"=>"Pendente", "private"=>"Privado"];
        if($post->post_status=='publish'){
            $notify = '';
        }else{
            $notify = ' <strong> - '.$status[$post->post_status].'</strong>';
        }
        return "<a onClick='insertIdInURL(".$post->ID.")' style='cursor:pointer;'>".esc_html($post->post_title)."</a>".
                $notify.
                '<div id="rffmenuitem'.$post->ID.'" style="display:none; float:right;"> | '.
                    '<a href="'.home_url().'?p='.$post->ID.'" target="_blank">Ver</a> | '.
                    "<a onClick='insertIdInURL(".$post->ID.")' style='cursor:pointer;'>Editar</a>".
                '</div>';
    }

    function column_author($post) {
        $author = get_the_author_meta('display_name', $post->post_author);
        return esc_html($author);
    }

    function column_categories($post) {
        $categories = get_the_category($post->ID);
        $category_names = wp_list_pluck($categories, 'name');
        return esc_html(implode(', ', $category_names));
    }

    function column_tags($post) {
        $tags = get_the_tags($post->ID);
        if ($tags) {
            $tag_names = wp_list_pluck($tags, 'name');
            return esc_html(implode(', ', $tag_names));
        }
        return '';
    }

    function column_comments($post) {
        $comments_count = wp_count_comments($post->ID);
        return esc_html($comments_count->approved);
    }

    function column_date($post) {
        return esc_html(get_the_date('', $post));
    }

    // Renderiza a tabela
    function display() {
        if(isset($_POST['m']) || isset($_POST['cat'])){
            $this->prepare_items();
        }

        // Exibe os filtros
        $this->display_filters();

        if (empty($this->items)) {
            echo '<p>Nenhum post encontrado.</p>'; // Mensagem quando não há posts
        }else{
            // Cabeçalho da tabela
            echo '<table id="tableListPostsRff" class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>';
            foreach ($this->get_columns() as $column_key => $column_title) {
                if($column_title==''){
                    echo '<th scope="col" class="' . esc_attr($column_key) . '"><input id="cb-select-all-1" onclick="selectionAllItemList(this)" type="checkbox" style="margin:auto;"></th>';
                }else{
                    echo '<th scope="col" class="' . esc_attr($column_key) . '">' . esc_html($column_title) . '</th>';
                }
            }
            echo '</tr></thead>';

            // Corpo da tabela
            echo '<tbody id="the-list" data-wp-lists="list:post">';
            foreach ($this->items as $item) {
                echo '<tr>';
                foreach ($this->get_columns() as $column_key => $column_title) {
                    echo '<td class="' . esc_attr($column_key) . '" onmouseover="rffmenuover(\'rffmenuitem'.$item->ID.'\')" onmouseout="rffmenuout(\'rffmenuitem'.$item->ID.'\')">' . 
                            $this->{'column_' . $column_key}($item);
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        
        echo '</form>';
    }
}
