function removeEditableTable(){
    let tables = document.getElementsByClassName('tabela');
    if(tables.length<=0)return;
    for(let i=0; i<tables.length;i++){
        tables[i].setAttribute('onkeydown', "console.log('')");
        tables[i].setAttribute('onkeyup', "console.log('')");
        let father = tables[i].parentNode;
        father.insertBefore(tables[i], tables[i]);
        recursion(tables[i]);
    }
    function recursion(tag){
        for(let c=tag.firstChild;c!=null;c=c.nextElementSibling){
            if(c.nodeType!==1)continue;
            // console.log(c)
            if(c.tagName=="TD"){
                c.setAttribute('contenteditable', false);
            }else{
                recursion(c);
            }
        }
    }
}
removeEditableTable();

function divToolsTableOut(){}

function divToolsTableOver(){}

function actionClickTd(obj){}

function allowDrop2(){}