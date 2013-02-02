<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<? if ($this->security->canRead("Market")) { ?>
<div class="button-general"><a href="/Market/datasources"><img src="/images/glyphicons/glyphicons_027_search_white.png" class="button-general-icon"><?=$this->localization->buttons->FindDataSourcetext; ?></a></div>
<div class="button-spacer"></div>
<? } ?>
<? if ($this->security->canWrite("DataSources")) { ?>
<div class="button-general"><a href="javascript:void(0);" onclick="startWizard();"><img src="/images/glyphicons/glyphicons_190_circle_plus_white.png" class="button-general-icon"><?=$this->localization->buttons->AddDataSourcetext; ?></a></div>
<div class="button-spacer"></div>
<div class="button-general disabled" id="button_modifyDataSource"><a href="javascript:void(0);" onclick="startDataSourceProperties();"><img src="/images/glyphicons/glyphicons_030_pencil_white.png" class="button-general-icon"><?=$this->localization->buttons->ModifyDataSourcetext; ?></a></div>
<? } ?>
<BR clear=all> <BR clear=all>

<table id="datasource_list"><tr><td/></tr></table>
<div id="datasource_list_pager"></div>

<script type="text/javascript" src="/js/DataSource.js"></script>
<script type="text/javascript">
var dsChooser;
$(function(){
    dsChooser = new chooserDataSource($('#datasource_list'), {
        onSelectRow:    DataSourceRowOnClick
    ,   ondblClickRow:  DataSourceRowOnDblClick
    });
});

var DataSourceRowOnClick = function (id) {
    id = dsChooser.fixId(id);
    $('#button_modifyDataSource').removeClass("disabled");
    $('#button_modifyDataSource').data("selected_id", id);
};

var DataSourceRowOnDblClick = function(id) {
    id = dsChooser.fixId(id);
    window.open("<?=$this->API_URL;?>"+"dataset/"+id+"?authToken="+"<?=$this->usersAuthTokens[0]['token']; ?>"); 
};
                        
        var datasourceobj;
        //var dataSourceId=null;
        
        function startDataSourceProperties() {
            var dataSourceId = $('#button_modifyDataSource').data("selected_id");
            console.log("DEBUG201212081106: selected_id="+dataSourceId);
            if (dataSourceId) {
                datasourceobj = new DataSource(dataSourceId);
                datasourceobj.retrieve();
                //console.log($("#div_datasource_properties"));

                showWizardPopup('#div_datasource_properties');
                window.setTimeout(validateDataSourceProperties, 800);
            } else {
                alert("ERROR201208231052: A Data Source was not selected.");
            }
        }
        
	var DataSourceGridPublished = function (celDiv,id) {
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
	</script>

