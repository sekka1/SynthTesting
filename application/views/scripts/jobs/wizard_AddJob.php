<?php 
//$this->localization = $this->localization;
?>
<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<SCRIPT language="javascript">

var myjob = new Job();
// Setup the wizard slides
var wizard;
var wizarddefined =0;
$(document).ready(function () { 
		wizard = new wizard("#div_workflow_list", {skipsetup:false, placeholder: "#div_wizard_placeholder", slideEdgeSize: 40});
		wizarddefined=1;
		//wizard.hide_slides();
                blackOut();
                wizard.start();
});

function startWizard() {
	$("#dashboard_tabs").fadeOut('slow');
	if(wizarddefined==0) {
		wizard = new wizard("#div_workflow_list", {placeholder: "#div_wizard_placeholder", slideEdgeSize: 40});
		wizarddefined=1;
	} else {
		wizard.start();
	}
}

function validateJobProperties() {
    $('#button_job_properties_continue').addClass('disabled');
    if($('#form_job_name').val().length > 5) {
        if($('#form_job_description').val().length > 10) {
            $('#button_job_properties_continue').removeClass('disabled');
        }
    } 
}

function saveJob() {
    myjob.set("name", $('#form_job_name').val());
    myjob.set("description",$('#form_job_description').val())
    myjob.set("startdate",$('#form_job_startdate').val())
    var notifications = [];
    if($('#form_job_notification_email')) {
        notifications.push({"email":""});
    }
    if($('#form_job_notification_dashboard')) {
        notifications.push({"dashboard":1});
    }    
    myjob.set("notifications",notifications);
    
    myjob.save(); 
    setTimeout(function() { window.location = "/dashboard/index#jobs"; }, 500);
    /*
    $( "#dialog-message" ).dialog({
        modal: true,
        buttons: {
            Ok: function() {
                $( this ).dialog( "close" );
                window.location = "/dashboard/index#jobs";
            }
        }
    });    
    $('#dialog-message').css("z-index", "99999");
        */
}
</SCRIPT>

<div style="height:90%; position: relative;">&nbsp;</div>
<!-- BEGIN clear placeholder for wizard slides ******************** -->
<div id="div_wizardBox_placeholder" class="wizardBox_placeholder" style="position: absolute; z-index: -500; border: 1px solid #f00; top: 100px; left: 100px;">
HELLO
</div>
<!-- END clear placeholder for wizard slides ********************** -->

<!-- BEGIN WORKFLOW_LIST -->
<div id="div_workflow_list" class="wizardBox" data-wizard-nextSlide="#div_job_properties">
<h2><? echo $this->localization->wizard->jobs->workflow->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<? echo $this->localization->wizard->jobs->workflow->body; ?>

<? require_once("workflows_list.php"); ?>

<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px; padding-right: 12px;">
		<div class="button-cancel" style="float: left;"><a href="javascript:void(0);" onclick="removeBlackOut();wizard.fadeAll();setTimeout(function(){window.history.back();},1500);"><?=$this->localization->buttons->canceltext; ?></a></div>
		<div id="button_job_workflow_continue" class="button-next disabled" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$this->localization->buttons->nexttext; ?></a></div>
</div>

</div><!-- END WORKFLOW_LIST-->

<!-- BEGIN properties ************************************************************************** -->
<div id="div_job_properties" class="wizardBox" data-wizard-previousSlide="#div_workflow_list" data-wizard-nextSlide="#div_job_complete">

<h2><? echo $this->localization->wizard->jobs->properties->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
<? echo $this->localization->wizard->jobs->properties->body; ?>
<BR>
<FORM id="form_job_properties" name="form_job_properties">

    <LABEL for="form_job_name"><?=$this->localization->wizard->jobs->properties->Form->Labels->Name; ?></LABEL>
    <INPUT name="form_job_name" id="form_job_name" TYPE="text" SIZE="40" style="width: 500px;" onkeyup="validateJobProperties();">

    <LABEL for="form_job_description"><?=$this->localization->wizard->jobs->properties->Form->Labels->Description; ?></LABEL>
    <TEXTAREA name="form_job_description" id="form_job_description" TYPE="text" SIZE="40" style="width: 500px; height: 100px;" onkeyup="validateJobProperties();"></TEXTAREA>
    
    <LABEL for="form_job_startDate"><?=$this->localization->wizard->jobs->properties->Form->Labels->StartDate; ?></LABEL>
    <INPUT name="form_job_startDate" id="form_job_startDate">
    <SCRIPT>
        $(function() {
            $( "#form_job_startDate" ).datepicker({
                "dateFormat": "yy-mm-dd",
                "defaultDate": +0
            });
            //$( "#form_job_startDate" ).datepicker("option", "showAnim", "slide");
	});
    </SCRIPT>
        
</FORM>

<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$this->localization->buttons->backtext; ?></a></div>
		<div id="button_job_properties_continue" class="button-next disabled" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$this->localization->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END properties -->

<!-- BEGIN job notifications ************************************************************************** -->
<div id="div_job_complete" class="wizardBox" data-wizard-previousSlide="#div_job_properties">

<h2><? echo $this->localization->wizard->jobs->notifications->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
<? echo $this->localization->wizard->jobs->notifications->body; ?>

        <LABEL for="form_job_notification_email"><INPUT TYPE="checkbox" name="form_job_notification_email" id="form_job_notification_email" value="1" style="display: inline;"><?=$this->localization->wizard->jobs->notifications->Form->Labels->EmailNotification; ?></LABEL>
        <LABEL for="form_job_notification_dashboard"><INPUT TYPE="checkbox" name="form_job_notification_email" id="form_job_notification_email" value="1" style="display: inline;"><?=$this->localization->wizard->jobs->notifications->Form->Labels->DashboardNotification; ?></LABEL>


<? # print_r($this->localization); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$this->localization->buttons->backtext; ?></a></div>
		<div class="button-cancel" style="float: right;"><a href="javascript:void(0);" onclick="/*wizard.fadeAll();*/saveJob();"><?=$this->localization->buttons->savetext; ?></a></div>
</div>

</div><!-- END wizardBox-->


<div id="dialog-message" title="Job Sent">
    <p>
        <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
        Your job has been queued.
    </p>
    <p>
        Press Ok to return to the job dashboard.
    </p>
</div>

<!-- END job notifications -->
