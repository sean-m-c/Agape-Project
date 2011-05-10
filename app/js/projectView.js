$(document).ready(function() {
    jQuery(".IGSTClick").live('click', function() {
        var container = $("#ajaxGuiContainer");
        var loading = $("#loading");

        container.fadeOut('fast');
        loading.addClass('loading').fadeIn('fast');
        
        IGSTClick(jQuery(this).attr("href"),container,loading);
        return false;
    });
});

function IGSTClick(url,container,loading) {

    var urlParams = getUrlVars(url);

    $.ajax({
        url: url.substring(0,url.indexOf('&')),
        global: false,
        type: 'GET',
        data: ({
            id : urlParams['parentID'],
            ajaxPanel : urlParams['ajaxPanel'],
            action : urlParams['action']
        }),
        dataType: 'html',
        error: function(a,b,c) {
            alert('Error retrieving panel., XMLHttpRequest: '+ a + '; textStatus: '+b+' errorThrown: '+c);
        },
        success: function(data){
            loading.fadeOut('fast').removeClass('loading');
            $('#issue-grid').remove();
            container.fadeOut('fast').html(data).fadeIn('fast');
        }
    });

}

function getUrlVars(url) {
    var map = {};
    var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        map[key] = value;
    });
    return map;
}
