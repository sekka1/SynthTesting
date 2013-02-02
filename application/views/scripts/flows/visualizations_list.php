<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

	<table id="visualizations_list" class="flexme3" style="display: none"></table>

	<script type="text/javascript">

	var VisualizationRowOnClick = function (celDiv,id) {
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
                                
                                $('#button_visualization_close').removeClass("disabled");
                                $('#div_visualizations_list').data('flowItem').setData({visualization_id: id}); // IMPORTANT
                                $('#button_visualization_close').data("selected_id", id);
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

		$("#visualizations_list").flexigrid({
			url : '<?=$this->API_URL."/visualizations/?authToken=".$this->usersAuthTokens[0]['token']; ?>',
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
                                process: VisualizationRowOnClick,
				//hide: true,

			}, {
				display : 'Type',
				name : 'type',
				width : 70,
				sortable : true,
				align : 'center',
                                process: VisualizationRowOnClick,
				//process : gridOnClick,
				//hide: true,
				//process : procMe,
			}, {
				display : 'Class',
				name : 'class',
				width : 200,
				sortable : true,
				align : 'left',
                                process: VisualizationRowOnClick,
				//hide: true,

			}, {
				display : 'Name',
				name : 'name',
				width : 150,
				sortable : true,
				align : 'center',
				process: VisualizationRowOnClick,
			}, {
				display : 'Description',
				name : 'description',
				width : 300,
				sortable : true,
				align : 'left',
                                process: VisualizationRowOnClick,
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
			title : 'Visualizations',
			useRp : true, //false, //was true
			rp : 15,
			//width : 850,
			height : 300,
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

