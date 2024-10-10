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
            SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
            FROM $wpdb->posts
            WHERE post_type = 'post' AND post_status = 'publish'
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
        $rffValid = new PostsRffValidate();
        // Captura os filtros
        $filter_category = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
        $filter_date = isset($_GET['m']) ? sanitize_text_field($_GET['m']) : '';

        // Argumentos para a consulta
        $args = [
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ];

        // Adiciona filtros
        if ($filter_category && $filter_category != '0') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $filter_category
                ]
            ];
        }

        // Filtro por data (exemplo simplificado)
        if ($filter_date && $filter_date != '0' && $filter_date != '') {
            $year = substr($filter_date, 0, 4);
            $month = substr($filter_date, 4, 2);
            $args['year'] = $year;
            $args['monthnum'] = $month;
        }

        $this->posts = get_posts($args);

        // Verifica se há posts
        $this->items = !empty($this->posts) ? $this->posts : [];
    }

    function display_filters() {
        ?>
        <form method="post">
            <input type="hidden" name="page" value="lista-de-posts" />

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Selecionar ação em massa</label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="-1">Ações em massa</option>
                    <option value="trash">Mover para lixeira</option>
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
                        echo $dp['month'];
                        echo $convertMonth[$dp['month']];
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
            </div>
            <br class="clear">
        <!-- </form> -->
        <?php
    }

    function trashPost(){
        if(isset($_POST['action']) && isset($_POST['post_id'])){
            if(count($_POST['post_id'])>0 && $_POST['action']==='trash'){
                foreach($_POST['post_id'] as $post_id){
                    wp_trash_post(intval($post_id));
                }
            }
        }
    }

    function addFilter(){
        $this->trashPost();
        $args = array('post_type' => 'post', 'posts_per_page' => -1);

        // Filtrar por data
        if (!empty($_POST['m']) && $_POST['m'] != '0') {
            $year_month = intval($_POST['m']);
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
        if (!empty($_POST['cat']) && $_POST['cat'] != '0') {
            $args['cat'] = intval($_POST['cat']);
        }

        // Realizar a consulta
        $query = new WP_Query($args);

        // Se existir conteúdo, então ele é atribuído a $this->items.
        if (count($query->posts)>0) {
            $this->items = $query->posts;
        } else {
            $this->items =[];
            // echo 'Nenhum post encontrado.';
        }

        // Reset post data
        wp_reset_postdata();
    }

    function column_cb($post) {
        return '<input type="checkbox" name="post_id[]" value="' . $post->ID . '" />';
    }

    function column_title($post) {
        $categories = get_the_category($post->ID);
        foreach($categories as $cat){
            $idsCat[] = esc_html($cat->term_id);
        }
        $categ = implode(',', $idsCat);
        $p = '{
            "ID": "'.$post->ID.'",
            "post_author": "'.$post->post_author.'",
            "post_date": "'.$post->post_date.'",
            "post_title": "'.$post->post_title.'",
            "post_status": "'.$post->post_status.'",
            "post_type": "'.$post->post_type.'",
            "post_categories": "'.$categ.'"
        }';
        $status = ["draft"=>"Rascunho", "pending"=>"Pendente", "private"=>"Privado"];
        if($post->post_status=='publish'){
            $notify = '';
        }else{
            $notify = ' <strong> - '.$status[$post->post_status].'</strong>';
        }
        return "<a onClick='openEditPost(".$p.", ".$post->ID.")' style='cursor:pointer;'>".esc_html($post->post_title)."</a>".
                $notify.
                '<div id="rffmenuitem'.$post->ID.'" style="display:none; float:right;"> | '.
                    '<a href="'.home_url().'?p='.$post->ID.'" target="_blank">Ver</a> | '.
                    "<a onClick='openEditPost(".$p.", ".$post->ID.")' style='cursor:pointer;'>Editar</a>".
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
            $this->addFilter();
        }

        // Exibe os filtros
        $this->display_filters();

        if (empty($this->items)) {
            echo '<p>Nenhum post encontrado.</p>'; // Mensagem quando não há posts
            return;
        }
        // Cabeçalho da tabela
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        foreach ($this->get_columns() as $column_key => $column_title) {
            if($column_title==''){
                echo '<th scope="col" class="' . esc_attr($column_key) . '"><input id="cb-select-all-1" type="checkbox" style="margin:auto;"></th>';
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
                        $this->{'column_' . $column_key}($item) .
                    '<div id="contentPost'.$item->ID.'" style="display:none;">'.
                        $item->post_content.
                    '</div>'. '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</form>';
    }
}
