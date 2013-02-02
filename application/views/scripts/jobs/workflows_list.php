<!-- BEGIN WORKFLOW LIST -->

	<table id="workflows_list" class="flexme3" zstyle="display: none"></table>

	<script type="text/javascript">

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
                                
                                flowSelected(id);
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
        
        var flowSelected = function(id) {
            $('#button_job_workflow_continue').removeClass("disabled");
            $('#button_job_workflow_continue').data("selected_id", id);
            myjob.set("flow_id", id); // Set the Job's Flow ID'            
        }
        
        var flowsGridOnLoad = function(data) {
            if(flow_id > 0) {
                // We have a flow_id passed in, lets select that
                $('#row'+flow_id).addClass("selectedRow");
                flowSelected(flow_id);
                console.log($('#row'+flow_id));
            }
        }

		$("#workflows_list").flexigrid({
			url : '<?=$this->API_URL."/flows/?authToken=".$this->usersAuthTokens[0]['token']; ?>',
                        method: 'GET',
			//dataType : 'xml',
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
				width : 150,
				sortable : true,
				align : 'center',
				process: flowsRowOnClick,
			}, {
				display : 'Description',
				name : 'description',
				width : 300,
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
			height : 220,
			//resizable: false,
			showTableToggleBtn : true,
			resizable: true,
			singleSelect: true,
                        onSuccess: flowsGridOnLoad
			
		});
	</script>
<!-- END WORKFLOW LIST -->