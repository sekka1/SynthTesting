<?php ?>
<? require_once realpath(dirname(__FILE__).'/../library/AlgorithmsIO/config.php'); ?>
<? 
#$wizard_height="500px";ls 

$GLOBALS[htmllinktags] .= <<<EOS
<!-- Bootstrap CSS Toolkit styles -->
<link rel="stylesheet" href="/css/fileupload/bootstrap.min.css">
<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
<link rel="stylesheet" href="/css/fileupload/bootstrap-responsive.min.css">
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="/css/fileupload/bootstrap-ie6.min.css"><![endif]-->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="/css/fileupload/jquery.fileupload-ui.css">
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="/js/html5.js"></script><![endif]-->

<link rel="stylesheet" type="text/css" href="/css/flexigrid/flexigrid.pack.css" />
<link rel="stylesheet" href="/js/codemirror/codemirror.css">
EOS;
#<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

echo pageheader(); 

function wizard_quicknav() { 
	// Use the default
	return wizard_navigation(array("data_sources"=>1,"algorithms"=>1,"security_roles"=>0,"delivery"=>0)); 
}

?>
<SCRIPT src="/js/algo.js"></script>
<SCRIPT language="javascript">

// Setup the wizard slides
var wizard;
$(document).ready(function () { 
	wizard = new wizard("#div_modify_algorithms_intro", {placeholder: "#div_wizard_placeholder", slideEdgeSize: 40,});
});


function change_wizard_algorithm_method() {
	var val=$('input:radio[name=wizard_algorithm_method]:checked').val()
	switch (val) {
		case "expert": 
			wizard.nextSlide("#div_modify_algorithms_choose_datasource");
			break;
		case "novice": 
			wizard.nextSlide("#div_modify_algorithms_tutorial");
			break;
		case "browse": 
			wizard.nextSlide("#div_modify_algorithms_list");
			break;
		case "skip": 
			wizard.nextSlide("#div_modify_algorithms_complete");
			break;
	}
	
}
</SCRIPT>
<FORM id="form_datasource" name="form_datasource_size">

<div style="height:90%; position: relative;">&nbsp;</div>
<!-- BEGIN clear placeholder for wizard slides ******************** -->
<div id="div_wizardBox_placeholder" class="wizardBox_placeholder">
</div>
<!-- END clear placeholder for wizard slides ********************** -->

<!-- BEGIN intro -->
<div id="div_modify_algorithms_intro" class="wizardBox" data-wizard-nextSlide="#div_modify_algorithms_tutorial">
<h2><? echo $localstrings->wizard->modify_algorithms->intro->title; ?></h2>
<? echo wizard_quicknav(); ?> 

<BR style="clear: both">
<BR style="clear: both">
<? echo $localstrings->wizard->modify_algorithms->intro->body; ?>

<BR>
Please choose the option that fits best:<BR><BR>
<div style="width:95%; padding-left: 12px;">
  <div class="switch switch_verticle">
	<INPUT type="radio" name="wizard_algorithm_method" id="wizard_algorithm_method_expert" value="expert"/><LABEL for="wizard_algorithm_method_expert"><?=$localstrings->wizard->modify_algorithms->intro->method->expert_text; ?></LABEL>
	<INPUT type="radio" name="wizard_algorithm_method" id="wizard_algorithm_method_novice" value="novice" checked="checked" /><LABEL for="wizard_algorithm_method_novice"> <?=$localstrings->wizard->modify_algorithms->intro->method->novice_text; ?></LABEL>
	<INPUT type="radio" name="wizard_algorithm_method" id="wizard_algorithm_method_browse" value="browse" /><LABEL for="wizard_algorithm_method_browse"><?=$localstrings->wizard->modify_algorithms->intro->method->browse_text; ?></LABEL>
	<INPUT type="radio" name="wizard_algorithm_method" id="wizard_algorithm_method_skip" value="skip" /><LABEL for="wizard_algorithm_method_skip"><?=$localstrings->wizard->modify_algorithms->intro->method->skip_text; ?></LABEL>
  </div>

<SCRIPT>
$('input[name=wizard_algorithm_method]:radio').click(function(){ change_wizard_algorithm_method(); });
</SCRIPT>
</div>
<? # print_r($localstrings); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px; padding-right: 12px;">
		<div class="button-cancel" style="float: left;"><a href="start.php"><?=$localstrings->buttons->canceltext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$localstrings->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
</FORM>
<!-- END intro -->

<!-- BEGIN tutorial ************************************************************************** -->
<div id="div_modify_algorithms_tutorial" class="wizardBox" data-wizard-previousSlide="#div_modify_algorithms_intro" zdata-wizard-nextSlide="#div_modify_algorithms_intro">

<h2><? echo $localstrings->wizard->modify_algorithms->tutorial->title; ?></h2>
<? echo wizard_quicknav(); ?> 

<BR style="clear: both">
<BR style="clear: both">
<? echo $localstrings->wizard->modify_algorithms->tutorial->body; ?>
<BR />
<DIV style="width: 100%; height: 200px; zborder: 1px solid #f00; margin-top:100px;">
	<img src="/images/ralph_nose1.gif" style="float: right;">
	<DIV style="width: 80%; height: 100px; font-size: 40pt; zborder: 1px solid #f0f; vertical-align:middle;">Ooops! You caught us!</DIV>
</DIV>
<? # print_r($localstrings); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$localstrings->buttons->backtext; ?></a></div>
		<!-- <div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$localstrings->buttons->nexttext; ?></a></div> -->
</div>

</div><!-- END wizardBox-->
<!-- END tutorial -->

<!-- BEGIN algorithms_list ************************************************************************** -->
<div id="div_modify_algorithms_list" class="wizardBox" data-wizard-previousSlide="#div_modify_algorithms_intro" data-wizard-nextSlide="#div_modify_algorithms_complete">

<h2><? echo $localstrings->wizard->modify_algorithms->list->title; ?></h2>
<? echo wizard_quicknav(); ?> 

<BR style="clear: both">
<BR style="clear: both">
<? echo $localstrings->wizard->modify_algorithms->list->body; ?>

<BR>
<div style="width:95%; padding-left: 12px; zborder: 1px dashed red;">

<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<table id="algorithm_list" class="flexme3" style="display: none"></table>

	<script type="text/javascript">

	var gridOnClick = function (celDiv,id) {
		$(celDiv).click(
			function(){ 
				console.dir(celDiv);
				console.dir(id);
				//alert("Opening id="+id);
				$("#div_popup_datasource_edit").center();
				console.dir($("#popup_datasource_edit_id"));
				$("#popup_datasource_edit_id").html(id);
				$("#popup_datasource_edit_id").css("border: 5px solid #f0f;");
				$("#div_popup_datasource_edit").fadeIn('slow');
				$("#popup_datasource_edit_iframe").attr("src","editDataSource.php?id="+id);
				$("#popup_datasource_edit_iframe").reload();
				//$("#div_popup_datasource_edit").datasource_update(id);
			});

		switch($(celDiv).text()) {
			case "pending":
				$(celDiv).html('<img class="flexi_image" src="/images/glyphicons/glyphicons_196_circle_exclamation_mark_red_bevel.png">');
				break;
			case "ready":
				$(celDiv).html('<img class="flexi_image" src="/images/glyphicons/glyphicons_193_circle_ok_green_bevel.png">');
				break;
		}
	}

	var processPublished = function (celDiv,id) {
		$(celDiv).click(
			function(){ 
				console.dir(celDiv);
				console.dir(id);
			});

		switch($(celDiv).text()) {
			// TODO: I don't really like these eye icons, need to replace - MRR20120528
			case "private":
				$(celDiv).html('<img class="flexi_image" src="/images/glyphicons/glyphicons_052_eye_close.png">');
				break;
			case "forsale":
				$(celDiv).html('<img class="flexi_image" src="/images/glyphicons/glyphicons_227_usd.png">'); // Maybe make international later - TODO: MRR20120528
				break;
			case "public":
				$(celDiv).html('<img class="flexi_image" src="/images/glyphicons/glyphicons_051_eye_open.png">');
				break;
		}
	}

	var processLocation = function (celDiv,id) {
		switch($(celDiv).text()) {
			case "algorithms":
				$(celDiv).html('<img class="flexi_image" src="/images/logo_isolated.png">');
				break;
			case "amazon":
				$(celDiv).html('<img class="flexi_image" src="/images/partners/amazon_web_services.png">');
				break;
			case "rackspace":
				$(celDiv).html('<img class="flexi_image" src="/images/partners/rackspace.png">');
				break;
		}
	}

		function procMe(celDiv,id){ 
		    $(celDiv).click( 
		      function(){ 
			rowData = getRow(id);
		      } 
		    ) 
		  //process rowData here 
		} 

		function getRow(id) { 
		  console.log("id="+id);
		  //var td = document.getElementById('import_list').rows[id].cells; 
		  var tr = $('#import_list').find('#'+id).css('border', '1px solid #f0f'); 
	          console.dir(tr);
		  var str;
			/*
		  var td = tr.rows;
		  str = ''; 
		  for(j=0;j<td.length;j++){  //serialize as you like 
		     str += "'"+ $(td[j]).text()+"',"; 
		  } 
			*/
		 return str; 
		}

		$("#algorithm_list").flexigrid({
			url : '/getDataSources.php',
			//dataType : 'xml',
			dataType : 'json',
			idProperty : 'id',
			colModel : [ 
			{
				display : 'id',
				name : 'id',
				width : 40,
				sortable : true,
				align : 'center',
				hide: true,

			}, {
				display : 'id_seq',
				name : 'id_seq',
				width : 40,
				sortable : true,
				align : 'center',
				//process : gridOnClick,
				hide: true,
				//process : procMe,
			}, {
				display : 'customer_id',
				name : 'customer_id_seq',
				width : 180,
				sortable : true,
				align : 'left',
				hide: true,

			}, {
				display : 'Status',
				name : 'status',
				width : 40,
				sortable : true,
				align : 'center',
				process : gridOnClick,
			}, {
				display : 'Data Source Name',
				name : 'friendly_name',
				width : 180,
				sortable : true,
				align : 'left'
			}, {
				display : 'Type',
				name : 'type',
				width : 40,
				sortable : true,
				align : 'left'
			}, {
				display : 'Cols',
				name : 'numCols',
				width : 30,
				sortable : true,
				align : 'right',
				zhide : true
			}, {
				display : 'Rows',
				name : 'numRows',
				width : 30,
				sortable : true,
				align : 'right'
			}, {
				display : 'location',
				name : 'location',
				width : 40,
				sortable : true,
				align : 'center',
				process: processLocation,
			}, {
				display : 'Published',
				name : 'privacy',
				width : 40,
				sortable : true,
				align : 'center',
				process: processPublished,
			}, {
				display : 'filesystem_name',
				name : 'filesystem_name',
				width : 100,
				sortable : true,
				align : 'left',
				hide: true,
			}, {
				display : 'description',
				name : 'friendly_description',
				width : 100,
				sortable : true,
				align : 'left'
			}, {
				display : 'version',
				name : 'version',
				width : 40,
				sortable : true,
				align : 'left',
				hide: true,
			}, {
				display : 'ip_address',
				name : 'ip_address',
				width : 60,
				sortable : true,
				align : 'right',
				hide : true,
			}, {
				display : 'Size',
				name : 'size',
				width : 40,
				sortable : true,
				align : 'right'
			}, {
				display : 'Modified',
				name : 'datetime_modified',
				width : 100,
				sortable : true,
				align : 'left'
			}, {
				display : 'Created',
				name : 'datetime_created',
				width : 50,
				sortable : true,
				align : 'left'
			}, {
				display : 'MD5 Checksum',
				name : 'importMD5',
				width : 180,
				sortable : true,
				align : 'left'
			}, {
				display : 'Notes',
				name : 'customer_notes',
				width : 40,
				sortable : true,
				align : 'left'
			}, {
				display : 'Internal Notes',
				name : 'internal_notes',
				width : 100,
				sortable : true,
				align : 'left',
				hide: true,
			} ],
			zbuttons : [ {
				name : 'Edit',
				bclass : 'edit',
				onpress : test
			}, {
				name : 'Delete',
				bclass : 'delete',
				onpress : test
			}, {
				separator : true
			} ],
			searchitems : [ {
				display : 'Notes',
				name : 'customer_notes'
			}, {
				display : 'Data Source Name',
				name : 'friendly_name',
				isdefault : true
			} ],
			sortname : "datetime_modified",
			sortorder : "desc", //asc=ascending, desc=descinding
			//usepager : false, // Was true
			usepager : true, // Was true
			//title : 'Data Sources',
			useRp : false, //was true
			rp : 15,
			width : 850,
			height : 150,
			//resizable: false,
			showTableToggleBtn : true,
			resizable: true,
			singleSelect: true,
			
		});

		function test(com, grid) {
			if (com == 'Delete') {
				confirm('Delete ' + $('.trSelected', grid).length + ' items?')
			} else if (com == 'Add') {
				alert('Add New Item');
			}
		}
	</script>

</div>

<? # print_r($localstrings); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$localstrings->buttons->backtext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$localstrings->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END algorithms_list -->

<!-- BEGIN choose_datasource ************************************************************************** -->
<div id="div_modify_algorithms_choose_datasource" class="wizardBox" data-wizard-previousSlide="#div_modify_algorithms_intro" data-wizard-nextSlide="#div_modify_algorithms_editor">

<h2><? echo $localstrings->wizard->modify_algorithms->choose_datasource->title; ?></h2>
<? echo wizard_quicknav(); ?> 

<BR style="clear: both">
<BR style="clear: both">
<? echo $localstrings->wizard->modify_algorithms->choose_datasource->body; ?>

<BR>
<div style="width:95%; padding-left: 12px; zborder: 1px dashed red;">

<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

</div>

<? # print_r($localstrings); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$localstrings->buttons->backtext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$localstrings->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END algorithms_list -->

<!-- BEGIN modify_algorithms_editor ************************************************************************** -->
<div id="div_modify_algorithms_editor" class="wizardBox" data-wizard-nextSlide="#div_modify_algorithms_complete" data-wizard-previousSlide="#div_modify_algorithms_choose_datasource">

<h2><? echo $localstrings->wizard->modify_algorithms->editor->title; ?></h2>
<? echo wizard_quicknav(); ?> 

<BR style="clear: both">
<BR style="clear: both">
<!-- <? echo $localstrings->wizard->modify_algorithms->editor->body; ?> -->

<script src="/js/codemirror/codemirror.js"></script>
<script src="/js/codemirror/mode/r/r.js"></script>
<form><textarea id="code" name="code" class="well" style="border: 1px solid #fff;">
<? include("sample_r_code.php"); ?>
</textarea></form>
<script>
      var editor = CodeMirror.fromTextArea(document.getElementById("code"), {lineNumbers:true,});
      console.dir($('#'));
      $(".CodeMirror").addClass("well-small");
      $(".CodeMirror").css("background-color", "#eee");
</script>
<? # print_r($localstrings); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$localstrings->buttons->backtext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="wizard.next();"><?=$localstrings->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END modify_algorithms_editor -->



<!-- BEGIN datasource_complete ************************************************************************** -->
<div id="div_modify_algorithms_complete" class="wizardBox" data-wizard-previousSlide="#div_modify_algorithms_intro">

<h2><? echo $localstrings->wizard->modify_algorithms->complete->title; ?></h2>
<? echo wizard_quicknav(); ?> 

<BR style="clear: both">
<BR style="clear: both">
<? echo $localstrings->wizard->modify_algorithms->complete->body; ?>

<? # print_r($localstrings); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$localstrings->buttons->backtext; ?></a></div>
		<div class="button-next" style="float: right;"><a href="/wizard/algorithms.php" zonclick="wizard.next();"><?=$localstrings->buttons->nexttext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END datasource_complete -->

<!-- BEGIN datasource_edit ************************************************************************** -->
<div id="div_popup_datasource_edit" class="wizardBox" data-wizard-previousSlide="#div_datasource_import_list" data-wizard-nextSlide="div_datasource_import_list">

<h2><? echo $localstrings->wizard->data_sources->datasource_edit->title; ?> <span id="popup_datasource_edit_id">Hello</span></h2>
<!-- <? echo wizard_navigation(array("data_sources"=>1,"algorithms"=>0,"security_roles"=>0,"delivery"=>0)); ?> -->

<BR style="clear: both">
<? echo $localstrings->wizard->data_sources->datasource_edit->body; ?>

<iframe id="popup_datasource_edit_iframe" src="/wizard/editDataSource.php" style="width: 100%; height: 500px;";>
</iframe>

<? # print_r($localstrings); ?>
<BR><BR><BR>
<div style="position: absolute; bottom:10px; width:95%; padding-left: 12px;">
		<!-- <div class="button-back" style="float: left;"><a href="javascript:void(0);" onclick="wizard.previous();"><?=$localstrings->buttons->backtext; ?></a></div> -->
		<div class="button-next" style="float: right;"><a href="javascript:void(0);" onclick="$('#div_popup_datasource_edit').fadeOut('slow');"><?=$localstrings->buttons->savetext; ?></a></div>
</div>

</div><!-- END wizardBox-->
<!-- END datasource_edit -->






<BR><BR>
<? echo pagefooter(); ?>
