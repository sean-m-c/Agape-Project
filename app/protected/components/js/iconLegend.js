$(document).ready(function() {
    var iconLegendList = $('div.iconLegend ul.iconLegendList');
    var iconLink = $('div.iconLegend > h3');
    iconLegendList.hide();

    iconLink.click(function() { return false; });
    
    iconLink.mouseover(function() {
        iconLegendList.slideToggle('fast');
        return false;
    });

    iconLink.mouseout(function() {
        iconLegendList.slideToggle('fast');
        return false;
    });
});
