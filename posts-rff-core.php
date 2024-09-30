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
            <button id="btInsert" onclick="newPost()">Inserir novo</button>

            <span id="urlRff" style="display:none;"><?php echo POSTS_RFF_URL_EDITOR; ?></span>
            <span id="dirRff" style="display:none;"><?php echo POSTS_RFF_DIR_EDITOR; ?></span>
        </div>
        <div id="content">
            <div id="divView">
                <h1>Você está na view</h1>
                <table class="widefat fixed">
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
                                    <!-- <td> -->
                                    <!-- <td><a href="<?php //echo esc_url(get_edit_post_link($post->ID)); ?>"><?php //echo esc_html(get_the_title($post)); ?></a></td> -->
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
                    <button type="submit" id="cadastrarPostRff" name="cadastrarPostRff">Cadastrar</button>
                    <button type="submit" id="editarPostRff" name="editarPostRff">Editar</button>
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
