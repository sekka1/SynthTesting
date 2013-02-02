<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

	<table id="algorithm_list" class="flexme3" style="display: none"></table>

	<script type="text/javascript">

	var algoRowOnClick = function (celDiv,id) {
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
                                
                                $('#button_algorithms_close').removeClass("disabled");
                                $('#div_algorithms_list').data('flowItem').setData({algorithm_id: id}); // IMPORTANT
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
//{"id":"5","type":"synchronous","class":"\/Machine Learning\/Recommenders","name":"ItemBaseRecommender - Log Likelihood No Pref","description":""}
		$("#algorithm_list").flexigrid({
			url : '/algorithms', //'getAlgorithms.php',
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
                                process : algoRowOnClick,
				//hide: true,

			}, {
				display : 'Type',
				name : 'type',
				width : 70,
				sortable : true,
				align : 'center',
				process : algoRowOnClick,
				//hide: true,
				//process : procMe,
			}, {
				display : 'Class',
				name : 'class',
				width : 200,
				sortable : true,
				align : 'left',
                                process : algoRowOnClick,
				//hide: true,

			}, {
				display : 'Name',
				name : 'name',
				width : 150,
				sortable : true,
				align : 'center',
				process : algoRowOnClick,
			}, {
				display : 'Description',
				name : 'description',
				width : 300,
				sortable : true,
				align : 'left',
                                process : algoRowOnClick,
			} ],
			searchitems : [ {
				display : 'Description',
				name : 'description'
			}, {
				display : 'Name',
				name : 'name',
				isdefault : true,
                                process : algoRowOnClick,
			} ],
			sortname : "name",
			sortorder : "desc", //asc=ascending, desc=descinding
			//usepager : false, // Was true
			usepager : true, // Was true
			title : 'Algorithms',
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

