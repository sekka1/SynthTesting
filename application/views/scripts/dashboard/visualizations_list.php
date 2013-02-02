<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<? if ($this->security->canRead("Market")) { ?>
<div class="button-general"><a href="/market/visualizations"><img src="/images/glyphicons/glyphicons_027_search_white.png" class="button-general-icon"><?=$this->localization->buttons->FindVisualizationtext; ?></a></div>
<div class="button-spacer"></div>
<? } ?>
<? if ($this->security->canWrite("Visualizations")) { ?>
<div class="button-general"><a href="/viside/index" zonclick="startWizard();"><img src="/images/glyphicons/glyphicons_190_circle_plus_white.png" class="button-general-icon"><?=$this->localization->buttons->AddVisualizationtext; ?></a></div>
<div class="button-spacer"></div>
<div id="button_modifyVisualization" class="button-general disabled"><a href="javascript:void(0);" onclick="window.location='/viside/index?visualization_id='+$(this).parent().data('selected_id');"><img src="/images/glyphicons/glyphicons_030_pencil_white.png" class="button-general-icon"><?=$this->localization->buttons->ModifyVisualizationtext; ?></a></div> 
<? } ?>
<BR clear=all> <BR clear=all>

<table id="visualization_list"><tr><td/></tr></table>
<div id="visualization_list_pager"></div>

<script type="text/javascript">
$(function(){
    visualizationChooser = new chooserVisualization($('#visualization_list'), {
        onSelectRow:    VisualizationRowOnClick
    ,   ondblClickRow:  DataSourceRowOnDblClick
    });
});

var VisualizationRowOnClick = function (id) {
    id = visualizationChooser.fixId(id);
    $('#button_modifyVisualization').removeClass("disabled");
    $('#button_modifyVisualization').data("selected_id", id);
};
</script>

