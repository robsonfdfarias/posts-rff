<?php

if(!defined('WPINC')){
    die();
}

   /**
   * Includes PHP
   */
if(file_exists(POSTS_RFF_CORE_INC.'posts-rff-class-table.php')){
    require_once(POSTS_RFF_CORE_INC.'posts-rff-class-table.php');
}
if(file_exists(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php')){
  require_once(POSTS_RFF_CORE_INC.'posts_rff_validate_fields.php');
}


function add_menu_admin_page(){
    add_menu_page(
        "Posts_Rff",
        "Posts Rff",
        "manage_options",
        "Posts_Rff",
        "posts_rff",
        "dashicons-menu",
        7,
    );
}

add_action('admin_menu', 'add_menu_admin_page');

$url_rff_dir_editor = POSTS_RFF_DIR_EDITOR;

function posts_rff(){
    $rffValid = new PostsRFFValidate();
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
    $args = array(
        'posts_per_page' => 10,
    );
    $posts = get_posts($args);

    ?>
    <div id="wrap">
        <div class="tab">
            <h1 class="wp-heading-inline">Posts</h1>
            <a onclick="newPost()" class="page-title-action">Adicionar novo post</a>

            <span id="urlRff" style="display:none;"><?php echo POSTS_RFF_URL_EDITOR; ?></span>
            <span id="dirRff" style="display:none;"><?php echo POSTS_RFF_DIR_EDITOR; ?></span>
        </div>
        <div id="content">
            
            <div id="divForm" style="position:absolute; width:98%; height:100%; display:none; flex-direction:column; padding: 20px; top:0;left:0; background-color: #fff; margin-left:-20px;">
                <h1 id="formTituloPostsRff">Você está na new</h1>
                <?php
                    include_once(POSTS_RFF_DIR_EDITOR."editText2.php");
                ?>
                <form method="post" name="formulario" id="formulario">
                    <input type="text" id="idPost" name="idPost" style="display:none" spellcheck="true">
                    <input type="text" id="titulo" name="titulo" placeholder="Insira o título do artigo" required spellcheck="true" style="width:100%; margin-top: 10px;">
                    <select name="post_status" id="post_status" style="margin-top: 10px;">
                        <option value="publish">(PUBLIC) O post é publicado e visível ao público.</option>
                        <option value="draft">(DRAFT) O post está salvo como um rascunho e não está visível ao público.</option>
                        <option value="pending">(PENDING) O post está aguardando revisão.</option>
                        <option value="private">(PRIVATE) O post está visível apenas para usuários com permissões adequadas.</option>
                    </select>
                    <select name="post_type" id="post_type" style="margin-top: 10px;">
                        <option value="post">Um post de blog padrão.</option>
                        <option value="page">Uma página estática.</option>
                        <option value="attachment">Um arquivo anexado (como uma imagem).</option>
                        <option value="revision">Uma revisão do post.</option>
                        <option value="custom_post_type">Tipos de post personalizados definidos por plugins ou temas.</option>
                    </select><br>
                        <?php
                            $categories = get_categories(['hide_empty' => false]);
                            if(!empty($categories)){
                                echo '<div style="margin-top: 10px;">';
                                foreach($categories as $catogorie){
                                    echo '<label>';
                                    echo '<input class="rffChecked" type="checkbox" name="cats[]" value="'.$catogorie->term_id.'" style="margin: 0 5px 0 20px;"> '.esc_html($catogorie->name);
                                    echo '</label>';
                                }
                                echo '</div>';
                            }
                        ?>
                    <textarea name="conteudo" id="conteudo" cols="30" rows="10" style="display:none;" required></textarea>
                    <button type="submit" id="cadastrarPostRff" name="cadastrarPostRff" style="display:none;">Cadastrar</button>
                    <button type="submit" id="editarPostRff" name="editarPostRff" style="display:none;">Editar</button>
                    <button type="submit" name="cancelarPostRff" id="cancelarPostRff" onclick="breakOperation()">Cancelar</button>
                </form>
            </div>
            <script src="<?php echo POSTS_RFF_URL_JS; ?>posts_rff_admin_get_url.js"></script>
            <script>
                var adminGetUrl = new PostsRffAdminGetUrl();
                adminGetUrl.verifyPostsRffVar(); // verifica se a variável posts_rff existe, se não, ele cria
                
                localStorage.setItem("POSTS_RFF_URL_EDITOR", document.getElementById('urlRff').innerHTML);
                localStorage.setItem("POSTS_RFF_DIR_EDITOR", document.getElementById('dirRff').innerHTML);
                
                document.getElementById("cadastrarPostRff").addEventListener("click", async function(){
                    await insertTextoEmTextarea();
                })
                document.getElementById('editarPostRff').addEventListener("click", async function(){
                    await insertTextoEmTextarea();
                })
                async function insertTextoEmTextarea(){
                    var campoTexto = document.getElementById('texto').innerHTML;
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
                    let arrayCat = json.post_categories.split(',');
                    // console.log(arrayCat);
                    let cbCateg = document.getElementsByClassName('rffChecked');
                    // console.log(cbCateg.length);
                    for(let i=0; i<cbCateg.length; i++){
                        cbCateg[i].checked=false;
                    }
                    for(let i=0; i<cbCateg.length; i++){
                        if(arrayCat.includes(cbCateg[i].value)){
                            cbCateg[i].checked=true;
                        }
                    }
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
                    document.getElementById('formTituloPostsRff').innerHTML='Inserir Post';
                }

                function rffmenuover(obj){
                    document.getElementById(obj).style.display='flex';
                }

                function rffmenuout(obj){
                    document.getElementById(obj).style.display='none';
                }
            </script>
            <div id="divView">
                
                <?php
                    $table = new Posts_RFF_Posts_Table();
                    $table->prepare_items(); // Prepara os itens
    
                    if (empty($table->items)) {
                        echo '<p>Nenhum post encontrado.</p>'; // Mensagem quando não há posts
                    } else {
                        $table->display(); // Renderiza a tabela
                    }
                ?>
                <script>
                    document.getElementById('post-query-submit').addEventListener('click', () => {
                        adminGetUrl.addUrlParameter('m', document.getElementById('filter-by-date').value);
                        adminGetUrl.addUrlParameter('cat', document.getElementById('cat').value);
                    })
                    document.getElementById('clear_filter').addEventListener('click', (event) => {
                        event.preventDefault();
                        adminGetUrl.removeUrlParameter('cat');
                        adminGetUrl.removeUrlParameter('m');
                        document.getElementById('filters_posts').submit();
                    })
                </script>
            </div>
        </div>
    </div>
    <?php
    
}
