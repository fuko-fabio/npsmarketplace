function litAutoResize(id_seller){
    var newheight;
    var id = 'lit_seller_' + id_seller;

    if(document.getElementById)
        newheight=document.getElementById(id).contentWindow.document .body.scrollHeight;

    document.getElementById(id).height= (newheight) + "px";
}