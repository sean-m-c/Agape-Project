<?php /* echo CHtml::css("
#login {
    display:none;
    position: absolute;
    left: 139px;
    top: -148px;
    max-width:220px;
    z-index: 999;
    margin: 0;
    padding: 0;
}
#login fieldset {
    border:1px solid #bbb;
    background-color:#fff;
    -moz-box-shadow: 0 1px 15px rgba(0,0,0,0.3);
    -webkit-box-shadow: 0 1px 15px rgba(0,0,0,0.3);
}
#login label {
    text-align:left;
}
.register {
    margin-left:400px;
}
ol.boxes {
    margin:0 auto;
    height:75px;
    padding:0;
    list-style-type:none;
    margin-bottom:80px;
    -moz-border-radius:20px;
}
ol.boxes li {
    text-shadow: 0 -1px 1px #fff;
    text-align:center;
    font:19px/25px Verdana, Helvetica, Geneva;
    color:#333;
    float:left;
    display:inline;
    width:250px;
    height:75px;
    padding:20px;
}
ol.boxes li span.emph {
    text-align:left;
    font:27px/25px Verdana, Helvetic, Geneva;
    padding-right:5px;

}
ol.boxes li#step1 {
    background:#e1e1e1 url(images/homelist-overlay.png) 0 0 repeat-x;
}
.gridRow ol.boxes li#step2 {
    background:#ccc url(images/alert-overlay.png) 0 0 repeat-x;
}
.gridRow ol.boxes li#step3 {
    background:#999 url(images/alert-overlay.png) 0 0 repeat-x;
}

span#register {
    position:relative;
    left:600px;
}
");*/
?>

<?php $this->pageTitle=Yii::app()->name; ?>

<?php //echo $this->renderPartial('login', array('model'=>$model)); ?>

<div class="gridRow">
    <ol class="boxes">
        <li id="step1">
            <span class="emph">1.</span> Create your free account
        </li>
        <li id="step2">
            <span class="emph">2.</span> Register or join community partners
        </li>
        <li id="step3">
            <span class="emph">3.</span>Submit your project proposal to <?php echo Yii::app()->name; ?>
        </li>
    </ol>
</div>

<span id="register">
    <?php echo CHtml::link('Join Now &raquo;',array('user/create'),array('class'=>'diffbutton')); ?>
</span>