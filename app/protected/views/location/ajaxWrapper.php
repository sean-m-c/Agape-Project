<?php echo CHtml::css('.gmap { width:200px; height:200px; }'); ?>
<?php
//var_dump($model->location);
if(!empty($model->location)) {
    foreach($model->location as $location) {
        $this->renderPartial('/location/_view',array('data'=>$location),false,true);
    }
}?>


<?php echo CHtml::script('
$(document).ready(function() {
    $(".deleteLocation").live("click",function() {

        if(confirm("Are you sure you want to delete this location?")) {
            var link = $(this);

            var url = link.attr("href");
            var urlParams = getUrlVars(url);

            $.ajax({
                url: url,
                global: false,
                type: "POST",
                data: { id : urlParams["id"] },
                dataType: "json",
                error: function(a,b,c) {
                    alert("Error retrieving data, XMLHttpRequest: "+ a + "; textStatus: " + b + " errorThrown: " +c);
                },
                success : function(data) {
                    if(data.status=="f") {
                        $("#ajaxResponse").html(data.response).fadeIn();
                        $(".buttonLink").removeClass("ajaxLoaderSmall");
                    } else if(data.status=="t") {
                        link.parents("div .view").eq(0).remove();

                    }
                }

            });
        }
        return false;
    });
});
'); ?>