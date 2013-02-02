<?php 
//$this->localization = $this->localization;
?>
<SCRIPT language="javascript">

// Setup the wizard slides
var wizard;
var wizarddefined =0;
$(document).ready(function () { 
		wizard = new wizard("#div_datasource_size", {skipsetup:false, placeholder: "#div_wizard_placeholder", slideEdgeSize: 40,});
		wizarddefined=1;
		//wizard.hide_slides();
});

function startWizard() {
    blackOut();
	$("#dashboard_tabs").fadeOut('slow');
	if(wizarddefined==0) {
		wizard = new wizard("#div_datasource_size", {placeholder: "#div_wizard_placeholder", slideEdgeSize: 40,});
		wizarddefined=1;
	} else {
		wizard.start();
	}   
/*
	$("#div_datasource)size").center();
	$("#div_datasource_size").hide();
	$("#div_datasource_size").delay(800).fadeIn('slow');
*/
}

function change_upload_method(ui) {
		console.log("HERE="+ui.value);
	switch (ui.value) {
		case 0: case 1: case 2: case 3:
			$('#datasource_uploadmethod').prop('selectedIndex', 0);
			break;
		case 4: case 5: case 6:
			$('#datasource_uploadmethod').prop('selectedIndex', 1);
			break;
		case 7: case 8: case 9:
			$('#datasource_uploadmethod').prop('selectedIndex', 2);
			break;
	}
	uploadmethod_onchange();
}

function uploadmethod_onchange() {
	switch ($('#datasource_uploadmethod').prop('selectedIndex')) {
		case 0: 
			wizard.nextSlide("#div_datasource_web_upload");
			break;
		case 1: 
			wizard.nextSlide("#div_datasource_transfer_tool");
			break;
		case 2: 
			wizard.nextSlide("#div_datasource_ODBC");
			break;
	}
	
}
</SCRIPT>
<FORM id="form_datasource" name="form_datasource_size">

<div style="height:90%; position: relative;">&nbsp;</div>
<!-- BEGIN clear placeholder for wizard slides ******************** -->
<div id="div_wizardBox_placeholder" class="wizardBox_placeholder" style="position: absolute; z-index: -500; border: 1px solid #f00; top: 100px; left: 100px;">
HELLO
</div>
<!-- END clear placeholder for wizard slides ********************** -->

<!-- BEGIN datasource_size -->
<div id="div_datasource_size" class="wizardBox" data-wizard-nextSlide="#div_datasource_web_upload">
<h2><? echo $this->localization->wizard->data_sources->datasource_size->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
<? echo $this->localization->wizard->data_sources->datasource_size->body; ?>

<BR>
How large is your data?<BR><BR>

<!-- BEGIN slider FIXME: -->
<div class="sliderWrapper">
    <div id="leftslider" class="sliderExtender left" style=""></div>
<div id="slider" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" style="height: 20px">
                        <ul id="tickMark">
                            <li style="left: 0%; "> </li>
                            <li style="left: 12.5%; "> </li>
                            <li style="left: 25%; "> </li>
                            <li style="left: 37.5%; "> </li>
                            <li style="left: 50%; "> </li>
                            <li style="left: 62.5%; "> </li>
                            <li style="left: 75%; "> </li>
                            <li style="left: 87.5%; "> </li>
                            <li style="left: 100%; "> </li>
                        </ul>
                        <ul id="ticker">
                            <li style="left: 0%; ">10 MB</li>
                            <li style="left: 12.5%; ">100 MB</li>
                            <li style="left: 25%; ">1 GB</li>
                            <li style="left: 37.5%; ">10 GB</li>
                            <li style="left: 50%; ">100 GB</li>
                            <li style="left: 62.5%; ">1 TB</li>
                            <li style="left: 75%; ">10 TB</li>
                            <li style="left: 87.5%; ">100 TB</li>
                            <li style="left: 100%; ">1 PB</li>
                        </ul>
                    <div class="ui-slider-range ui-widget-header ui-slider-range-max" style="width: 0%; "></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 50%; "></a></div>
    <div class="sliderExtender"></div>
    </div>    
<!-- END slider -->
<div style="width:95%; padding-left: 12px; padding-top: 100px;">
	<div style="text-align: center">
		<BR><BR><BR>
		Recommended Connection Method<br>
		<SELECT ID="datasource_uploadmethod" NAME="datasource_uploadmethod" onchange="uploadmethod_onchange();">
			<OPTION value="web_upload">Web Upload</OPTION>
			<OPTION value="upload_toold">Upload Tool</OPTION>
			<OPTION value="odbc">ODBC Connection</OPTION>
		</SELECT>
	</div>	
</div>
<SCRIPT>
    ticks = 8,
    tickerWidth = 100/ticks,
    tickerInc = 0;
    max = 9;

$("#slider").slider({
            range: 'max',
            min: 1,
            max: max,
            value: 1,
            step: 1,
            animate: true,
	    slide: function(event, ui) {
		$("#ticker li").removeClass("highlight")
		$("#ticker li").eq((ui.value)-1).addClass("highlight");
	    },
	    change: function(event, ui) {
		change_upload_method(ui);
	    }
});

$('.ui-slider-handle').css("height","2.5em");
$('.ui-slider-handle').css("top","-.8em");
$('.ui-slider-handle').css("background", "url(/images/sliderKnob.png)");
$('.ui-slider-handle').css("background-size", "90%");
</SCRIPT>
<? # print_r($this->localization); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px; padding-right: 12px;">
		<div class="button-cancel" style="float: left;"><a href="javascript:void(0);" onclick="removeBlackOut();wizard.fadeAll();$('#dashboard_tabs').delay(800).fadeIn('slow');"><?=$this->localization->buttons->canceltext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$this->localization->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
</FORM>

<!-- BEGIN datasource_web_upload ************************************************************************** -->
<div id="div_datasource_web_upload" class="wizardBox" data-wizard-previousSlide="#div_datasource_size" data-wizard-nextSlide="#div_datasource_complete">

<h2><? echo $this->localization->wizard->data_sources->datasource_web_upload->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
<? echo $this->localization->wizard->data_sources->datasource_web_upload->body; ?>

<BR>
<!-- BEGIN Garland stuff -->
<!--
<form method="POST" enctype="multipart/form-data" action="/api/v1/class/DataSources/method/upload">
<input type="hidden" name="authToken" value="<?=$this->usersAuthTokens[0]['token']?>"/>
File to upload: <input type="file" name="theFile"><br>
File Type:<input type="text" name="type"/><br/>
Filename: <input type="text" name="friendly_name"/><br/>
description: <input type="text" name="friendly_description"/><br/>
version: <input type="text" name="version"/><br/>
<input type="submit" value="submit"/>

</form>
-->
<!-- END Garland stuff -->

<div style="width:95%; padding-left: 12px;">
<!-- BEGIN FILEUPLOAD -->
<div class="container">
<!-- BEGIN: TRY DIRECT POSTING TO API MRR20120607 -->
<form id="fileupload" action="<?=$this->url->datasourceUpload; ?>" method="POST" enctype="multipart/form-data">
<INPUT TYPE="hidden" NAME="type" value="rec"><INPUT TYPE="hidden" NAME="friendly_name" value="test name"><INPUT TYPE="hidden" NAME="friendly_description" VALUE="test description"><INPUT TYPE="hidden" NAME="version" value="1">
<INPUT TYPE="hidden" NAME="authKey" value="541b393f52b097d3e589ea63ccdfd49e">
<!-- END: TRY DIRECT POSTING TO API -->
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="zspan7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add Data Files</span>
                    <input type="file" name="files[]" multiple>
                    <zinput type="file" name="theFile[]" multiple><!-- Note: The API uses theFile where my script uses "files[]" MRR20120615 -->
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start Transfer</span>
                </button>
		<!--
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">
		-->
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade" style="height: 20px; border: 0px solid;">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active">
                    <div class="bar" style="width:0%; color:#000;">&nbsp;UPLOADING</div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended" style="font-size: 8pt;">&nbsp;</div>
            </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
	<DIV style="width: 90%; height: 150px; overflow-y: auto; overflow-x: hidden;">
        <table class="table table-bordered table-striped" style="width: 850px; overflow: hidden;"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
	</DIV>
    </form>
    <br>
</div> <!-- Maybe DELETE ME? -->
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <!-- <td class="preview"><span class="fade"></span></td> --><!-- Commented out MRR20120523 -->
        <td class="name" style="width: 30px; word-wrap: break-word;"><div style="width: 250px;">{%=file.name%}</div></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start" style="width: 100px;">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>{%=locale.fileupload.cancel%}</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <!-- <td colspan="2"></td> -->
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>{%=locale.fileupload.destroy%}</span>
            </button>
            <!-- <input type="checkbox" name="delete" value="1"> --><!-- Commented out MRR20120523 -->
        </td>
    </tr>
{% } %}
</script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="/js/fileupload/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="/js/fileupload/tmpl.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="/js/fileupload/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="/js/fileupload/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="/js/fileupload/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="/js/fileupload/jquery.fileupload-ui.js"></script>
<!-- The localization script -->
<script src="/js/fileupload/locale.js"></script>
<!-- The main application script -->
<script src="/js/fileupload/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="/js/fileupload/cors/jquery.xdr-transport.js"></script><![endif]-->

<!-- END FILEUPLOAD -->
</div>

<? # print_r($this->localization); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$this->localization->buttons->backtext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$this->localization->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END datasource_web_upload -->

<!-- BEGIN datasource_transfer_tool ************************************************************************** -->
<div id="div_datasource_transfer_tool" class="wizardBox" style="visibility: hidden;" data-wizard-previousSlide="#div_datasource_size" data-wizard-nextSlide="#div_datasource_import_list">

<h2><? echo $this->localization->wizard->data_sources->datasource_transfer_tool->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
<? echo $this->localization->wizard->data_sources->datasource_transfer_tool->body; ?>

<BR>

<DIV id="no_download_available" class="well btn-danger" style="border-color: #005 #eef #eef #005; box-shadow:inset 1px 1px 3px #333; display: none;"><a class="close" data-dismiss="alert" href="javascript:void(0);" onClick="$('#no_download_available').fadeOut('slow');">x</a><IMG SRC="/images/glyphicons/glyphicons_196_circle_exclamation_mark.png">&nbsp;Sorry the Download Tool is Not Available During Beta</DIV>

<a href="javascript:void(0);" id="download_transfer_tool" class="btn btn-success" onclick="$('#no_download_available').fadeIn('slow');"><IMG SRC="/images/glyphicons/glyphicons_219_circle_arrow_down.png">&nbsp;Download Transfer Tool</a>

<? # print_r($this->localization); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$this->localization->buttons->backtext; ?></a></div>
                <div class="button-cancel" style="float: right;"><a href="javascript:void(0);" onclick="removeBlackOut();wizard.fadeAll();$('#dashboard_tabs').delay(800).fadeIn('slow');"><?=$this->localization->buttons->closetext; ?></a></div>
		<!-- <div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$this->localization->buttons->nexttext; ?></a></div> -->
</div>

</div><!-- END wizardBox-->
<!-- END datasource_transfer_tool -->

<!-- BEGIN datasource_ODBC ************************************************************************** -->
<div id="div_datasource_ODBC" class="wizardBox" style="visibility: hidden;" data-wizard-previousSlide="#div_datasource_size" data-wizard-nextSlide="#div_datasource_import_list">

<h2><? echo $this->localization->wizard->data_sources->datasource_ODBC->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
<? echo $this->localization->wizard->data_sources->datasource_ODBC->body; ?>

<BR>
<FORM id="form_datasource_ODBC">

<DIV id="ODBC_Failure" class="well btn-danger" style="border-color: #005 #eef #eef #005; box-shadow:inset 1px 1px 3px #333; display: none;"><a class="close" data-dismiss="alert" href="javascript:void(0);" onClick="$('#ODBC_Failure').fadeOut('slow');">x</a><IMG SRC="/images/glyphicons/glyphicons_196_circle_exclamation_mark.png">&nbsp;Connection Failed: The Server Did Not Respond</DIV>

	 <LABEL for="ODBC_Connection_String">ODBC Connection String:</LABEL> 
	 <INPUT TYPE="text" id="ODBC_Connection_String" NAME="ODBC_Connection_String" style="width: 700px;"><a href="javascript:void(0);" onClick="$('#ODBC_Failure').fadeIn('slow');" class="btn btn_success">TEST</a><BR>
<SPAN class="label_example">(Ex: Server=myServerAddress;Port=1234;Database=myDataBase;Uid=myUsername;Pwd=myPassword;)</SPAN><BR><BR>
</FORM>

<BR>

<? # print_r($this->localization); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$this->localization->buttons->backtext; ?></a></div>
                <div class="button-cancel" style="float: right;"><a href="javascript:void(0);" onclick="removeBlackOut();wizard.fadeAll();$('#dashboard_tabs').delay(800).fadeIn('slow');"><?=$this->localization->buttons->closetext; ?></a></div>
                <!-- <div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$this->localization->buttons->nexttext; ?></a></div> -->
</div>

</div><!-- END wizardBox-->
<!-- END datasource_ODBC -->


<!-- BEGIN datasource_import ************************************************************************** -->
<div id="div_datasource_import_list" class="wizardBox" data-wizard-previousSlide="#div_datasource_web_upload" data-wizard-nextSlide="#div_datasource_complete">

<h2 style="border: 1px;"><? echo $this->localization->wizard->data_sources->datasource_import_list->title; ?></h2>
<!-- <? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
-->
<? echo $this->localization->wizard->data_sources->datasource_import_list->body; ?>

<BR>
<div style="width:95%; padding-left: 12px; zborder: 1px dashed red;">

<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

</div>

<? # print_r($this->localization); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$this->localization->buttons->backtext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$this->localization->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END datasource_import -->

<!-- BEGIN datasource_complete ************************************************************************** -->
<div id="div_datasource_complete" class="wizardBox" data-wizard-previousSlide="#div_datasource_web_upload">

<h2><? echo $this->localization->wizard->data_sources->datasource_complete->title; ?></h2>
<? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?>

<BR style="clear: both">
<BR style="clear: both">
<? echo $this->localization->wizard->data_sources->datasource_complete->body; ?>

<? # print_r($this->localization); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$this->localization->buttons->backtext; ?></a></div>
		<div class="button-cancel" style="float: right;"><a href="javascript:void(0);" onclick="removeBlackOut();wizard.fadeAll();$('#dashboard_tabs').delay(800).fadeIn('slow');"><?=$this->localization->buttons->closetext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END datasource_complete -->


<!-- Start DataSource Properties -->
<DIV id="div_datasource_properties" class="wizardBox" style="min-height: 550px;" zstyle="border: 5px solid red;">
    <h2><? echo $this->localization->DataSources->Properties->Title; ?></h2>
    <BR><BR>
    <? echo $this->localization->IDE->Properties->Instructions; ?><BR><BR>
    
    <FORM>
        <LABEL for="form_datasource_name"><? echo $this->localization->DataSources->Properties->Form->Labels->Title; ?></LABEL>
        <INPUT name="form_datasource_name" id="form_datasource_name" TYPE="text" SIZE="40" style="width: 500px;" onkeyup="validateDataSourceProperties();">
        
        <LABEL for="form_datasource_description"><? echo $this->localization->DataSources->Properties->Form->Labels->Description; ?></LABEL>
        <TEXTAREA name="form_datasource_description" id="form_datasource_description" TYPE="text" SIZE="40" style="width: 500px; height: 100px;" onkeyup="validateDataSourceProperties();"></TEXTAREA>

        <LABEL for="form_datasource_tags"><? echo $this->localization->DataSources->Properties->Form->Labels->Tags; ?></LABEL>
        <INPUT name="form_datasource_tags" id="form_datasource_tags" TYPE="text" SIZE="40" style="width: 500px;" onkeyup="validateDataSourceProperties();">

        <BR clear="all">
    </FORM>
    <div id="button_properties_close" class="button-cancel disabled" style="float: right;"><a href="javascript:void(0);" onclick="closeDataSourceProperties();"><?=$this->localization->buttons->savetext; ?></a></div>
</DIV>
<!-- End DataSource Properties -->