$(document).ready(function() {

    var jInput = $( ":input" );
    jInput.change(
        function( objEvent ){
            $("#saved").val("false");
        });

   $("#saveTab").live('click',function() {
       $(this).removeClass("i_checkmark").toggleClass('ajaxLoaderSmall');
   });

    $("#projectFormTabs").tabs({
        select: function(event, ui) {
            if($("#saved").val()!="true") {
                if(!confirm("You may have unsaved information on this tab. Press \"Ok\" to continue to the next tab, or press \"Cancel\" to go back and save your information.")) {
                    return false;
                }
            }
        }
    });

});