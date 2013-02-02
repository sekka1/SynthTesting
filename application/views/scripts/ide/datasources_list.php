<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<script>
    $(function() {
        //alert(API_Version());
    });
</script>
	<table id="datasource_list" class="flexme3" style="display: none"></table>

	<script type="text/javascript">

	var dsRowOnClick = function (celDiv,id) {
            //console.log(id);
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
                                
                                $('#button_datasource_close').removeClass("disabled");
                                myIDE.setDataSourceId(id); // IMPORTANT
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
                dsRowOnClick(celDiv,id);
 
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
                dsRowOnClick(celDiv,id);
		switch($(celDiv).text()) {
			case "S3,algorithms.io":
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


//{"id":"5","type":"synchronous","class":"\/Machine Learning\/Recommenders","name":"ItemBaseRecommender - Log Likelihood No Pref","description":""}
		$("#datasource_list").flexigrid({
			url : '/datasources/index', //'getDataSources.php',
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
                                process : dsRowOnClick,
				//hide: true,
			}, {
				display : 'Type',
				name : 'type',
				width : 50,
				sortable : true,
				align : 'center',
				process : dsRowOnClick,
				//hide: true,
				//process : procMe,
			}, {
				display : 'Location',
				name : 'location',
				width : 50,
				sortable : true,
				align : 'center',
				//process : dsRowOnClick,
				//hide: true,
				process : processLocation,
			
			}, {
				display : 'Filename',
				name : 'filesystem_name',
				width : 250,
				sortable : true,
				align : 'left',
                                process : dsRowOnClick,
				//hide: true,

			}, {
				display : 'Name',
				name : 'name',
				width : 150,
				sortable : true,
				align : 'center',
				process : dsRowOnClick,
			}, {
				display : 'Description',
				name : 'description',
				width : 300,
				sortable : true,
				align : 'left'
			}, {
				display : 'Version',
				name : 'version',
				width : 30,
				sortable : true,
				align : 'left',
                                process : dsRowOnClick,
			}, {
				display : 'IP Address',
				name : 'ip_address',
				width : 300,
				sortable : true,
				align : 'left'
			}, {
				display : 'Size',
				name : 'size',
				width : 300,
				sortable : true,
				align : 'left',
                                process : dsRowOnClick,
			}, {
				display : 'Created On',
				name : 'created',
				width : 300,
				sortable : true,
				align : 'left',
                                process : dsRowOnClick,
			}, {
				display : 'Modified On',
				name : 'last_modified',
				width : 300,
				sortable : true,
				align : 'left',
                                process : dsRowOnClick,
			} ],
			searchitems : [ {
				display : 'Description',
				name : 'description'
			}, {
				display : 'Name',
				name : 'name',
				isdefault : true
			} ],
			sortname : "created",
			sortorder : "desc", //asc=ascending, desc=descinding
			//usepager : false, // Was true
			usepager : true, // Was true
			title : 'Data Sources',
			useRp : false, //was true
			rp : 15,
			//width : 850,
			height : 300,
			//resizable: false,
			showTableToggleBtn : true,
			resizable: true,
			singleSelect: true,
			
		});

	</script>

