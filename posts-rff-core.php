<?php

if(!defined('WPINC')){
    die();
}


function add_menu_admin_page(){
    add_menu_page(
        "Posts Rff",
        "Posts Rff",
        "manage_options",
        "Posts Rff",
        "posts_rff",
        "dashicons-menu",
        7,
    );
}

add_action('admin_menu', 'add_menu_admin_page');

$url_rff_dir_editor = POSTS_RFF_DIR_EDITOR;

function posts_rff(){
    if(isset($_POST['cadastrarPostRFF'])){
        if(isset($_POST['titulo']) && isset($_POST['conteudo'])){
            echo $_POST['titulo'];
            $current_user_id = get_current_user_id();
            // echo '<br>'.$current_user_id.'++++++++++++';
            $post_data = array(
                'post_title'    => $_POST['titulo'],
                'post_content'  => $_POST['conteudo'],
                'post_status'   => $_POST['post_status'],
                'post_author'   => $current_user_id,
                'post_type'     => $_POST['post_type'],
            );
            
            $post_id = wp_insert_post($post_data);
        }
    }else if(isset($_POST['editarPostRff'])){
        if(isset($_POST['titulo']) && isset($_POST['conteudo'])){
            // Dados do post a serem atualizados
            $post_data = array(
                'ID'           => $_POST["idPost"],
                'post_title'    => $_POST['titulo'],
                'post_content'  => $_POST['conteudo'],
                'post_status'   => $_POST['post_status'],
                'post_type'   => $_POST['post_type'],
            );

            // Atualiza o post
            $updated_post_id = wp_update_post( $post_data );

            // Verifica se a atualização foi bem-sucedida
            if ( is_wp_error( $updated_post_id ) ) {
                // Trata o erro
                $error_message = $updated_post_id->get_error_message();
                echo 'Erro ao atualizar o post: ' . $error_message;
            } else {
                echo 'Post atualizado com sucesso. ID do post: ' . $updated_post_id;
            }
        }
    }
    $args = array(
        'posts_per_page' => 10,
    );
    $posts = get_posts($args);

    ?>
    <div id="wrap">
        <div class="tab">
            <!-- <button id="btInsert" onclick="newPost()">Inserir novo</button> -->
            <h1 class="wp-heading-inline">Posts</h1>
            <a onclick="newPost()" class="page-title-action">Adicionar novo post</a>

            <span id="urlRff" style="display:none;"><?php echo POSTS_RFF_URL_EDITOR; ?></span>
            <span id="dirRff" style="display:none;"><?php echo POSTS_RFF_DIR_EDITOR; ?></span>
        </div>
        <div id="content">
            <div id="divView">
                
                <?php
                    $table = new Meu_Plugin_Posts_Table();
                    $table->prepare_items(); // Prepara os itens
    
                    if (empty($table->items)) {
                        echo '<p>Nenhum post encontrado.</p>'; // Mensagem quando não há posts
                    } else {
                        // echo '<form method="post">';
                        $table->display(); // Renderiza a tabela
                        // echo '</form>';
                    }
                ?>
            </div>
            <div id="divForm" style="position:absolute; width:90%; height:100%; display:none; flex-direction:column; padding: 20px; top:0;left:0; background-color: #fff;">
                <h1 id="formTituloPostsRff">Você está na new</h1>
                <?php
                    include_once(POSTS_RFF_DIR_EDITOR."editText2.php");
                ?>
                <!-- <form action="" method="post" id="formulario"  enctype="multipart/form-data" autocomplete="on" onsubmit="return ValidateContactForm();"> -->
                <form method="post" name="formulario" id="formulario">
                    <input type="text" id="idPost" name="idPost" style="display:none" required spellcheck="true">
                    <input type="text" id="titulo" name="titulo" placeholder="Insira o título do artigo" required spellcheck="true">
                    <select name="post_status" id="post_status">
                        <option value="publish">O post é publicado e visível ao público.</option>
                        <option value="draft">O post está salvo como um rascunho e não está visível ao público.</option>
                        <option value="pending">O post está aguardando revisão.</option>
                        <option value="private">O post está visível apenas para usuários com permissões adequadas.</option>
                    </select>
                    <select name="post_type" id="post_type">
                        <option value="post">Um post de blog padrão.</option>
                        <option value="page">Uma página estática.</option>
                        <option value="attachment">Um arquivo anexado (como uma imagem).</option>
                        <option value="revision">Uma revisão do post.</option>
                        <option value="custom_post_type">Tipos de post personalizados definidos por plugins ou temas.</option>
                    </select>
                    <textarea name="conteudo" id="conteudo" cols="30" rows="10" style="display:none;" required></textarea>
                    <button type="submit" id="cadastrarPostRff" name="cadastrarPostRff" style="display:none;">Cadastrar</button>
                    <button type="submit" id="editarPostRff" name="editarPostRff" style="display:none;">Editar</button>
                    <button type="submit" name="cancelarPostRff" id="cancelarPostRff" onclick="breakOperation()">Cancelar</button>
                </form>
            </div>
            <script>
                localStorage.setItem("POSTS_RFF_URL_EDITOR", document.getElementById('urlRff').innerHTML);
                localStorage.setItem("POSTS_RFF_DIR_EDITOR", document.getElementById('dirRff').innerHTML);
                // alert(localStorage.getItem("POSTS_RFF_DIR_EDITOR"))

                document.getElementById("cadastrarPostRff").addEventListener("click", async function(){
                    await insertTextoEmTextarea();
                })
                document.getElementById('editarPostRff').addEventListener("click", async function(){
                    await insertTextoEmTextarea();
                })
                async function insertTextoEmTextarea(){
                    var campoTexto = document.getElementById('texto').innerHTML;
                    // document.getElementById("texto").innerHTML = '<div>Digite o seu artigo aqui...</div>';
                    console.log(campoTexto)
                    var conteudo = document.getElementById('conteudo');
                    conteudo.innerHTML = campoTexto;
                }

                async function ValidateContactForm(event){
                    await insertTextoEmTextarea();
                    var titulo = document.getElementById("titulo");
                    var conteudo = document.getElementById("conteudo");
                    if(!empty(titulo)){
                        alert("O campo titulo não pode ficar em branco!");
                        return false;
                    }else if(conteudo.value==''){
                        alert("O campo conteudo não pode ficar em branco!");
                        return false;
                    }
                    console.log(event)
                }

                document.getElementById('formulario').addEventListener('submit', async function(event){
                    alert('robson')
                    await ValidateContactForm(event);
                })

                function openEditPost(json, id){
                    let type = [];
                    type["post"] = `<option value="post" class="deleteItemType" selected>-> Um post de blog padrão.</option>`;
                    type["page"] = `<option value="page" class="deleteItemType" selected>-> Uma página estática.</option>`;
                    type["attachment"] = `<option value="attachment" class="deleteItemType" selected>-> Um arquivo anexado (como uma imagem).</option>`;
                    type["revision"] = `<option value="revision" class="deleteItemType" selected>-> Uma revisão do post.</option>`;
                    type["custom_post_type"] = `<option value="custom_post_type" class="deleteItemType" selected>-> Tipos de post personalizados definidos por plugins ou temas.</option>`;
                    let status = [];
                    status["publish"] = `<option value="publish" class="deleteItemStatus" selected>-> O post é publicado e visível ao público.</option>`;
                    status["draft"] = `<option value="draft" class="deleteItemStatus" selected>-> O post está salvo como um rascunho e não está visível ao público.</option>`;
                    status["pending"] = `<option value="pending" class="deleteItemStatus" selected>-> O post está aguardando revisão.</option>`;
                    status["private"] = `<option value="private" class="deleteItemStatus" selected>-> O post está visível apenas para usuários com permissões adequadas.</option>`;
                    let post = document.getElementById('contentPost'+id)
                    let post_type=document.getElementById('post_type');
                    for(let i=0;i<post_type.children.length; i++){
                        if(post_type.children[i].getAttribute('class')==='deleteItemType'){
                            post_type.children[i].remove();
                        }
                    }
                    let post_status=document.getElementById('post_status');
                    for(let i=0;i<post_status.children.length; i++){
                        if(post_status.children[i].getAttribute('class')==='deleteItemStatus'){
                            post_status.children[i].remove();
                        }
                    }
                    document.getElementById('editarPostRff').style.display='inline';
                    document.getElementById('cadastrarPostRff').style.display='none';
                    document.getElementById('texto').innerHTML = post.innerHTML;
                    document.getElementById('idPost').value=json.ID;
                    document.getElementById('titulo').value=json.post_title;
                    document.getElementById('post_status').innerHTML+=status[json.post_status];
                    document.getElementById('post_type').innerHTML+=type[json.post_type];
                    document.getElementById('formTituloPostsRff').innerHTML='Editar Poste';
                    document.getElementById('divForm').style.display='flex';
                }
                function breakOperation(){
                    let post_type=document.getElementById('post_type');
                    for(let i=0;i<post_type.children.length; i++){
                        if(post_type.children[i].getAttribute('class')==='deleteItemType'){
                            post_type.children[i].remove();
                        }
                    }
                    let post_status=document.getElementById('post_status');
                    for(let i=0;i<post_status.children.length; i++){
                        if(post_status.children[i].getAttribute('class')==='deleteItemStatus'){
                            post_status.children[i].remove();
                        }
                    }
                    document.getElementById('titulo').value='';
                    document.getElementById('texto').innerHTML = '<div>Digite o seu artigo aqui...</div>';
                    document.getElementById('divForm').style.display='none';
                }
                function newPost(){
                    let post_type=document.getElementById('post_type');
                    for(let i=0;i<post_type.children.length; i++){
                        if(post_type.children[i].getAttribute('class')==='deleteItemType'){
                            post_type.children[i].remove();
                        }
                    }
                    let post_status=document.getElementById('post_status');
                    for(let i=0;i<post_status.children.length; i++){
                        if(post_status.children[i].getAttribute('class')==='deleteItemStatus'){
                            post_status.children[i].remove();
                        }
                    }
                    document.getElementById('editarPostRff').style.display='none';
                    document.getElementById('cadastrarPostRff').style.display='inline';
                    document.getElementById('titulo').value='';
                    document.getElementById('texto').innerHTML = '<div>Digite o seu artigo aqui...</div>';
                    document.getElementById('divForm').style.display='flex';
                    document.getElementById('formTituloPostsRff').innerHTML='Inserir Poste';
                }
            </script>
        </div>
    </div>
    <?php
    
}


function bk(){
    ?>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Data</th>
                            <th>Author</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): setup_postdata($post); ?>
                                <tr>
                                    <?php
                                        $p = '{
                                            "ID": "'.$post->ID.'",
                                            "post_author": "'.$post->post_author.'",
                                            "post_date": "'.$post->post_date.'",
                                            "post_title": "'.$post->post_title.'",
                                            "post_status": "'.$post->post_status.'",
                                            "post_type": "'.$post->post_type.'"
                                        }';
                                        // print_r($p);
                                    ?>
                                    <td><?php echo esc_html($post->ID); ?></td>
                                    <td>
                                        <a onClick='openEditPost(<?php print_r($p); ?>, <?php echo $post->ID; ?>)'>
                                            <?php echo esc_html(get_the_title($post)); ?>
                                        </a>
                                        <div id="contentPost<?php echo $post->ID; ?>" style="display:none;">
                                            <?php echo $post->post_content; ?></td>
                                        </div>
                                    </td>
                                    <td><?php echo esc_html(get_the_date('', $post)); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url(get_edit_user_link($post->post_author)); ?>">
                                            <?php echo esc_html(get_the_author_meta('display_name', $post->post_author)); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html(ucfirst($post->post_status)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Nenhum post encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
    <?php
}


// Inclui a classe WP_List_Table
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class Meu_Plugin_Posts_Table extends WP_List_Table {
    private $posts;

    function __construct() {
        parent::__construct([
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false
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

    function prepare_items() {
        // Captura os filtros
        $filter_category = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
        $filter_date = isset($_GET['m']) ? sanitize_text_field($_GET['m']) : '';

        // Argumentos para a consulta
        $args = [
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish'
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
        if ($filter_date && $filter_date != '0') {
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
        <form method="get">
            <input type="hidden" name="page" value="lista-de-posts" />

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Selecionar ação em massa</label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="-1">Ações em massa</option>
                    <option value="edit" class="hide-if-no-js">Editar</option>
                    <option value="trash">Mover para lixeira</option>
                </select>
                <input type="submit" id="doaction" class="button action" value="Aplicar">
            </div>

            <div class="alignleft actions">
                <label for="filter-by-date" class="screen-reader-text">Filtrar por data</label>
                <select name="m" id="filter-by-date">
                    <option selected="selected" value="0">Todas as datas</option>
                    <?php
                    // Gerando opções de data (exemplo de meses de 2024)
                    for ($month = 1; $month <= 12; $month++) {
                        $value = '2024' . str_pad($month, 2, '0', STR_PAD_LEFT);
                        echo '<option value="' . esc_attr($value) . '">' . esc_html(date_i18n('F Y', mktime(0, 0, 0, $month, 1, 2024))) . '</option>';
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
        </form>
        <?php
    }

    function column_cb($post) {
        return '<input type="checkbox" name="post_id[]" value="' . $post->ID . '" />';
    }

    function column_title($post) {
        $p = '{
            "ID": "'.$post->ID.'",
            "post_author": "'.$post->post_author.'",
            "post_date": "'.$post->post_date.'",
            "post_title": "'.$post->post_title.'",
            "post_status": "'.$post->post_status.'",
            "post_type": "'.$post->post_type.'"
        }';
        return "<a onClick='openEditPost(".$p.", ".$post->ID.")'>".esc_html($post->post_title)."</a>";
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
        if (empty($this->items)) {
            echo '<p>Nenhum post encontrado.</p>'; // Mensagem quando não há posts
            return;
        }

        // Exibe os filtros
        $this->display_filters();

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
                echo '<td class="' . esc_attr($column_key) . '">' . $this->{'column_' . $column_key}($item) .
                    '<div id="contentPost'.$item->ID.'" style="display:none;">'.
                        $item->post_content.
                    '</div>'. '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
}

// Função para mostrar a lista de posts
function posts_rff5() {
    echo '<div class="wrap">';
    echo '<h1>Lista de Posts</h1>';
    
    $table = new Meu_Plugin_Posts_Table();
    $table->prepare_items(); // Prepara os itens

    global $posts2;
    // print_r($posts2);
    // print_r($posts2[0]);
    // print_r($table->items);

    if (empty($table->items)) {
        echo '<p>Nenhum post encontrado.</p>'; // Mensagem quando não há posts
    } else {
        echo '<form method="post">';
        $table->display(); // Renderiza a tabela
        echo '</form>';
    }

    echo '</div>';
}