function gridButtonClick(url,gridId) { 
    var id = url.substring(url.indexOf('id=')+3);
    
    $.fn.yiiGridView.update(gridId, {
        type:'POST',
        url: url.substring(0,url.indexOf('&')),
        data: {
            'ajaxData':{
                'id':id
            }
        },
        success:function(data, status) {
            $.fn.yiiGridView.update(gridId);
            if(data != '') {
                alert(data);
            }
        }
    });
}
