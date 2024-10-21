function selectionAllItemList(obj){
    let table = document.getElementById('tableListPostsRff');
    let checkboxTable = table.children[1].getElementsByClassName('checkboxpostsrff');
    let status;
    if(obj.checked){
        status=true;
    }else{
        status=false;
    }
    for(let i=0; i<checkboxTable.length; i++){
        checkboxTable[i].checked = status;
    }
}

function removeSelectionGeneralCheckbox(){
    let checkbox = document.getElementById('cb-select-all-1');
    checkbox.checked=false;
}