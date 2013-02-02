<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<? if ($this->security->canRead("Market")) { ?>
<div class="button-general"><a href="/market/flows"><img src="/images/glyphicons/glyphicons_027_search_white.png" class="button-general-icon"><?=$this->localization->buttons->FindWorkflowtext; ?></a></div>
<div class="button-spacer"></div>
<? } ?>
<? if ($this->security->canWrite("Flows")) { ?>
<div class="button-general"><a href="/flows/index" zonclick="startWizard();"><img src="/images/glyphicons/glyphicons_190_circle_plus_white.png" class="button-general-icon"><?=$this->localization->buttons->AddWorkflowtext; ?></a></div>
<div class="button-spacer"></div>
<div class="button-general disabled" id="button_modifyFlow"><a href="javascript:void(0);" onclick="window.location='/flows/index?id='+$(this).parent().data('selected_id');"><img src="/images/glyphicons/glyphicons_030_pencil_white.png" class="button-general-icon"><?=$this->localization->buttons->ModifyWorkflowtext; ?></a></div> 
<BR clear=all> <BR clear=all>
<? } ?>

<table id="flows_list"><tr><td/></tr></table>
<div id="flows_list_pager"></div>

<script type="text/javascript">
$(function(){
    flowChooser = new chooserFlow($('#flows_list'), {
        onSelectRow:    FlowRowOnClick
    });
});

var FlowRowOnClick = function (id) {
    id = flowChooser.fixId(id);
    $('#button_modifyFlow').removeClass("disabled");
    $('#button_modifyFlow').data("selected_id", id);
};
	var flowsRowOnClick = function (celDiv,id) {
		$(celDiv).click(
			function(){ 
                                $myrow = $(celDiv).parent().parent();
                                //$myrow.css("border", "5px solid #f0f");
                                $('.selectedRow').removeClass("selectedRow");
                                
                                $('.tmp_erow').removeClass("tmp_erow").addClass("erow");
                                if($myrow.hasClass("erow")) {
                                    $myrow.removeClass("erow");
                                    $myrow.addClass("tmp_erow"); // As a marker so we can find them later
                                }
                                $myrow.addClass("selectedRow");
                                
                                $('#button_modifyFlow').removeClass("disabled");
                                $('#button_modifyFlow').data("selected_id", id);
				console.dir($(celDiv).parent());
				console.dir(id);
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

		$("#workflows_list").flexigrid({
			url : '<?=$this->API_URL."/flows/?authToken=".$this->usersAuthTokens[0]['token']; ?>',
                        method: 'GET',
			//dataType : 'xml',
                        nowrap: false,
			dataType : 'json',
			idProperty : 'id',
			colModel : [ 
			{
				display : 'ID',
				name : 'id',
				width : 40,
				sortable : true,
				align : 'center',
                                process: flowsRowOnClick,
				//hide: true,

			}, {
				display : 'Type',
				name : 'type',
				width : 70,
				sortable : true,
				align : 'center',
                                process: flowsRowOnClick,
				//process : gridOnClick,
				//hide: true,
				//process : procMe,
			}, {
				display : 'Name',
				name : 'name',
				width : 250,
				sortable : true,
				align : 'center',
				process: flowsRowOnClick,
			}, {
				display : 'Description',
				name : 'description',
				width : 400,
				sortable : true,
				align : 'left',
                                process: flowsRowOnClick,
			} ],
			searchitems : [ {
				display : 'Description',
				name : 'description'
			}, {
				display : 'Name',
				name : 'name',
				isdefault : true
			} ],
			sortname : "name",
			sortorder : "desc", //asc=ascending, desc=descinding
			//usepager : false, // Was true
			usepager : true, // Was true
			title : 'Workflows',
			useRp : true, //false, //was true
			rp : 15,
			//width : 850,
			height : 500,
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

