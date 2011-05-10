<?php
echo CHtml::css('
.map_canvas {
    float:left;
    height: 200px;
    width:200px;
    margin-right:2em;
    -moz-box-shadow: 0px 2px 5px #999;
    -webkit-box-shadow: 0px 2px 5px #ccc;
    border-bottom:1px solid #999;
    -moz-border-radius:5px;
    -webkit-border-radius:5px;
    border-radius:5px;
}');
?>

<div class="view" style="float:left;width:100%;">


    <div class="map_canvas" id="map<?php echo $data->location_oid; ?>"></div>

<p style="margin-bottom:1em;">
        <?php
        echo CHtml::link('Delete Location',
                array('/location/delete',
                    'id' => $data->location_oid,
                    'ajax' => true),
                array(
                    'class' => 'i_logout buttonLink noBGPad deleteLocation',
                    'id' => 'deleteLocation_' . $data->location_oid,
        ));
        ?>
    </p>
    <?php if (!empty($data->address_line_1)) : ?>
            <b><?php echo CHtml::encode($data->getAttributeLabel('address_line_1')); ?>:</b>
    <?php echo CHtml::encode($data->address_line_1); ?>
            <br />
    <?php endif; ?>

    <?php if (!empty($data->address_line_2)) : ?>
                <b><?php echo CHtml::encode($data->getAttributeLabel('address_line_2')); ?>:</b>
    <?php echo CHtml::encode($data->address_line_2); ?>
                <br />
    <?php endif; ?>

    <?php if (!empty($data->city)) : ?>
                    <b><?php echo CHtml::encode($data->getAttributeLabel('city')); ?>:</b>
    <?php echo CHtml::encode($data->city); ?>
                    <br />
    <?php endif; ?>

    <?php if (!empty($data->state)) : ?>
                        <b><?php echo CHtml::encode($data->getAttributeLabel('state')); ?>:</b>
    <?php echo CHtml::encode($data->state); ?>
                        <br />
    <?php endif; ?>

    <?php if (!empty($data->zip)) : ?>
                            <b><?php echo CHtml::encode($data->getAttributeLabel('zip')); ?>:</b>
    <?php echo CHtml::encode($data->zip); ?>
                            <br />
    <?php endif; ?>

    <?php if (!empty($data->country)) : ?>
                                <b><?php echo CHtml::encode($data->getAttributeLabel('country')); ?>:</b>
    <?php echo CHtml::encode($data->country); ?>
                                <br />
    <?php endif; ?>

    <?php echo CHtml::script('

    var script = document.createElement("script");
    script.src = "http://www.google.com/jsapi?key=ABQIAAAA1eUnjLfMmXT7VevjJjJRoBTPeaomCSTIHU1og4orDQNfSGDYzBTS_VkhAaV7U0uYhU8-VUZswtCKIA&amp;callback=loadMaps1";
    script.type = "text/javascript";
    document.getElementsByTagName("head")[0].appendChild(script);

    function loadMaps1()
    {
        //AJAX API is loaded successfully. Now lets load the maps api
        google.load("maps", "3", {other_params : "sensor=false", "callback" : initialize});
    }

    function initialize() {
        var latlng = new google.maps.LatLng(' . $data->lat . ',' . $data->lng . ');
        var myOptions = {
            zoom: 8,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("map' . $data->location_oid . '"),myOptions);

        var marker = new google.maps.Marker({
        position: latlng,
        map: map
    });
}'); ?>

</div>