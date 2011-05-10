<div class="form">
<?php
echo CHtml::css('
div#topBar { border-bottom:1px solid #f2f2f2; padding-bottom:2em; }

div.form label {
    padding:0;  
    float:none;
    position:relative;
    width:50px;
    clear:none;
    color:#000;
}
div.form input#SearchForm_start_date { margin-right: 1em; }
table { width:auto; }
td.head {
    font-face:bold;
    background:url(images/divider.gif) bottom right no-repeat;
}
td.inner { padding-left:2em; }
td.oddRow { background-color:#f2f2f2; }
'); ?>
    <div id="topBar">
        <div class="row">
            <?php
            echo CHtml::label('Choose Statistic', 'statisticNav');
            echo CHtml::dropDownList('statisticNav', '', array(
                $this->createUrl('report/renderTaskPanel', array('panel' => 'statistics', 'form' => 'receivedProposals')) => 'Received Proposals',
                $this->createUrl('report/renderTaskPanel', array('panel' => 'statistics', 'form' => 'communityPartners')) => 'Community Partners',
                $this->createUrl('report/renderTaskPanel', array('panel' => 'statistics', 'form' => 'topicAreas')) => 'Topic Areas',
            ));
            ?>
        </div>

        <?php
            $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'search-form',
                        'enableAjaxValidation' => true,
                    ));
        ?>

        <?php echo $form->errorSummary($model); ?>

            <div class="row">
            <?php echo $form->labelEx($model, 'start_date'); ?>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'name' => 'SearchForm[start_date]',
                'value' => date("m/d/Y", strtotime('-1 years +1 day')),
            ));
            ?>
            <?php echo $form->error($model, 'start_date'); ?>
        
            <?php echo $form->labelEx($model, 'end_date'); ?>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'name' => 'SearchForm[end_date]',
                'value' => date("m/d/Y"),
            ));
            ?>
            <?php echo $form->error($model, 'end_date'); ?>
            </div>
    </div>

    <div id="statisticAjaxContainer"></div>

    <?php $this->endWidget(); ?>
</div>
  

<div id="resultContainer"></div>

<?php echo CHtml::script("

var statisticNav = $('#statisticNav');

// Load first item in list on page load
$(document).ready(function() { loadInner(statisticNav); });

// When the dropdown changes, change the inner panel item shown
statisticNav.change(function() { loadInner($(this)); });

$('#submitFormLink').live('click',function() {

    $.ajax({
        url: $(this).attr('href'),
        beforeSend: function() { $('#resultContainer').hide(); },
        global: false,
        type: 'POST',
        data: $('#search-form').serialize(),
        dataType: 'html',
        error: function(a,b,c) {
            alert('Error retrieving panel., XMLHttpRequest: '+ a + '; textStatus: '+b+' errorThrown: '+c);
        },
        success: function(data){
            $('#resultContainer').html(data).fadeIn();
        }
    });

    return false;

});

function loadInner(object) {

    $.ajax({
        beforeSend: function() { $('#statisticAjaxContainer').hide(); },
        url: object.val(),
        global: false,
        type: 'POST',
        dataType: 'html',
        error: function(a,b,c) {
            alert('Error retrieving panel., XMLHttpRequest: '+ a + '; textStatus: '+b+' errorThrown: '+c);
        },
        success: function(data){
            $('#resultContainer').empty();
            $('#statisticAjaxContainer').html(data).show('fade');
        }
    });
}
"); ?>