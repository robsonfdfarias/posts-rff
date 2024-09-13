var formUpload = document.getElementById('upload');
var preview = document.getElementById('preview');
var porcentagem = document.getElementById("porcento");
var i = 0;

var posicao;
var range;
var index;
var editor;


var width;
var height;
var srcImg;
var objImg;
var json;

formUpload.addEventListener('submit', async function(event){
  event.preventDefault();

  var xhr = new XMLHttpRequest();

  //xhr.open("POST", formUpload.getAttribute('action'));
  xhr.open("POST", "ex2.class.php");

  xhr.upload.addEventListener('onprogress', function(e) {
        var perc = (e.loaded/e.total)*100;
        porcentagem.innerHTML = perc;
	});

  var formData = new FormData(formUpload);
  formData.append("i", i++);
  await xhr.send(formData);
console.log(i)
  xhr.addEventListener('readystatechange', function() {
    if (xhr.readyState === 4 && xhr.status == 200) {
      console.log(xhr.responseText+'------------************');
      preview.innerHTML = xhr.status;
      json = JSON.parse(xhr.responseText);

      if (json.status!=='no_file' && json.status === 'ok') {
        porcentagem.innerHTML = '<img id="previewImage" src="'+json.img+'" width="300">';
        preview.innerHTML = '<img id="previewImage" src="'+json.img+'" width="300">';
      } else {
        preview.innerHTML = 'Arquivo não enviado';
      }
    } else {
      console.log('Erro robson')
      preview.innerHTML = xhr.statusText+' ------------------';
    }
  });
/*
  xhr.upload.addEventListener("progress", function(e) {
        var perc = (e.loaded/e.total)*100;
        porcentagem.innerHTML = perc;
    if (e.lengthComputable) {
      var percentage = Math.round((e.loaded * 100) / e.total);
      $preview.innerHTML = String(percentage) + '%';
    }
  }, false);*/

  xhr.upload.addEventListener("load", function(e){
    porcentagem.innerHTML = String(100) + '%';
  }, false);

}, false);



// function insertImg() {
//   var img = document.getElementById("previewImage");
//   srcImg = img.getAttribute("src");
//   width = img.getAttribute("width");
//   height = img.getAttribute("height");
//   selection = window.getSelection().toString();
//   var media = '<div class="item" id="item" dragstart="dragStart(event)" drag="drag(event)" dragend="dragend(event)" draggable="true" droppable="false" ondragover="allowDrop2(event)" contenteditable="false">'
//   media += '<div id="tools" draggable="false" droppable="false">'
//   media += '<button onclick="editVideo(this, event)" draggable="false" droppable="false">Editar</button>'
//     // media += '<div id="arrastar" dragstart="dragStart(event)" drag="drag(event)" dragend="dragend(event)" draggable="true" droppable="false" contenteditable="false"></div>'
//     media += '<button onclick="fecharJanVid(this)" draggable="false" droppable="false">X</button>'
//     media += '</div>'
//     media += '<img src="'+srcImg+'" width="'+width+'" height="'+height+'" onclick="openWindowEditImage(this)" class="imagem">';
//     media += '</div>'
//   // var imagem = '<img src="'+srcImg+'" width="'+width+'" height="'+height+'" onclick="openWindowEditImage(this)" class="imagem">';
//   document.execCommand('insertHTML', false, media);
// }



function insertImg() {
  let range = window.getSelection().getRangeAt(0);
  var img = document.getElementById("previewImage");
  srcImg = img.getAttribute("src");
  width = img.getAttribute("width");
  height = img.getAttribute("height");
  selection = window.getSelection().toString();
  let divPai = document.createElement('div');
  divPai.setAttribute('class', 'item');
  divPai.setAttribute('id', 'item');
  divPai.setAttribute('dragstart', 'dragStart(event)');
  divPai.setAttribute('drag', 'drag(event)');
  divPai.setAttribute('dragend', 'dragend(event)');
  divPai.setAttribute('draggable', 'true');
  divPai.setAttribute('droppable', 'false');
  divPai.setAttribute('ondragover', 'allowDrop2(event)');
  divPai.setAttribute('contenteditable', 'false');
  divPai.setAttribute('style', 'width:'+width+'px; height:'+height+'px;');
  var media = '<div id="tools" draggable="false" droppable="false">'
  media += '<button onclick="editVideo(this, event, \'img\')" draggable="false" droppable="false">Editar</button>'
    // media += '<div id="arrastar" dragstart="dragStart(event)" drag="drag(event)" dragend="dragend(event)" draggable="true" droppable="false" contenteditable="false"></div>'
    media += '<button onclick="fecharJanVid(this)" draggable="false" droppable="false">X</button>'
    media += '</div>'
    // media += '<img src="'+srcImg+'" width="98%" height="98%" onclick="openWindowEditImage(this)" class="imagem">';
    media += '<div id="mediaAndCaption"><img src="'+srcImg+'" width="100%" height="auto" class="imagem"></div>';
  divPai.innerHTML = media;
  range.insertNode(divPai);
}


/*
window.addEventListener("click", function(){
  setTimeout(function () {
    setCursor(editor, posicao);
  }, 200);
  //insertImg();
})*/


function editImg(img) {
  var larg = window.prompt("Insira a largura", "");
  if(larg!='' && larg!=null){
      img.setAttribute("width", larg);
  }
  /*
  var configStyle = img.getAttribute("style");
  console.log(configStyle)
      var opcoes = "";
  if(configStyle==null || configStyle==''){
      //configStyle+="border: 3px solid red;";
      configStyle="border: 3px solid red;";
  }else{
      //opcoes="border: 3px solid red;";
  }*/

  //testa(configStyle, "border: 3px solid red", "border");
  //testa(configStyle, larg, "width");
  //configStyle = opcoes;
  //img.setAttribute("style", configStyle);

  /*if(larg!=null && larg!=''){
      configStyle+="width: "+larg+"px;";
      img.setAttribute("style", configStyle)
  }*/
}

function testa(configStyle2, valor, arg){
  var op = configStyle2.split(";");
      var opcoes = "";
      console.log(op)
      for(i=0; i<(op.length-1); i++){
          console.log(op[i])
          var opInterno = op[i].split(":")
          if(arg!=null){
              if(opInterno[0]==arg){
                      op[i]=valor;
                  }else{
                      if(i==(op.length-2)){
                          opcoes+=valor;
                      }
                  }
          }
          /*if(opInterno=="border"){
              op[i]="border: 3px solid red";
          }
          
          if(larg!=null && larg!=''){
              if(opInterno=="width"){
                  op[i]="width: "+larg+"px";
              }
          }*/
          opcoes+=op[i]+";";
      }
      console.log(opcoes);
      return opcoes;
}


var statePaneImage = false;
function togglePaneImage(){
  /*const selection = window.getSelection();
  const range = selection.getRangeAt(0);
  const startOffset = range.startOffset;
  const endOffset = range.endOffset;
  //var posicao = selection.focusOffset
  var posicao = selection.focusNode;
  console.log(posicao)*/
  const selection = window.getSelection();
  posicao = selection.focusNode;
  if(statePaneImage==false){
      document.getElementById("geralInseriImagem").setAttribute("style", "display:flex; transition:ease-in-out 0.5s;");
      statePaneImage=true;
  }else{
      document.getElementById("geralInseriImagem").setAttribute("style", "display:none; transition:ease-in-out 0.5s;");
      statePaneImage=false;
      document.getElementById("porcento").innerHTML=''
      posicao=''
      funcBtImg();
  }
}


function setCursor(node, cursorElement) {
  var range = document.createRange();
  range.setStart(cursorElement, 0);
  range.collapse(true);
  if (window.getSelection) {
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(range);
      node.focus();
  } else if (document.selection && range.select) {
      range.select();
  }
}

editor = document.querySelector("#texto");

function funcBtImg(){
  // var imG = document.getElementsByClassName("imagem");
  var imG = document.getElementsByTagName('img');
  for(i=0;i<imG.length;i++){
      imG[i].setAttribute("onclick", "openWindowEditImage(this)")
  }
}

function onDragStart(event, elem) {
  elem.setAttribute("onclick", "openWindowEditImage(this)")
  funcBtImg()
  event
    .dataTransfer
    .setData('text/plain', event.target.id);

  event
    .currentTarget
    .style
    .backgroundColor = 'yellow';

}

function openWindowInsertImage(){
  window.open("rffeditor/windowInsertImage.php");
}

function openWindowEditImage(img){
  objImg = img;
  localStorage.setItem('endImg', img.src);
  localStorage.setItem('widImg', width);
  localStorage.setItem('heigImg', height);
  window.open("rffeditor/windowEditImage.php");
}

function editImgNovo(width, height) {
  //alert('chegou aqui tbm');
  if(width=='' || width==null){
      width='auto'
  }
  if(height=='' || height==null){
    height='auto'
  }
  objImg.setAttribute("width", width);
  objImg.setAttribute("height", height);
}