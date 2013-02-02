<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<? if ($this->security->canRead("Results")) { ?>
<div id="button_view_result" class="button-general disabled"><a href="javascript:void(0);" onclick="window.location='/visualization/index?job_id='+$(this).parent().data('selected_id');"><img src="/images/glyphicons/glyphicons_041_charts_white.png" class="button-general-icon"><?=$this->localization->buttons->ViewResulttext; ?></a></div>
<div class="button-spacer"></div>
<? } ?>
<? if ($this->security->canWrite("Results")) { ?>
<div id="button_remove_result" class="button-general disabled"><a href="javascript:void(0);" onclick="alert('ERROR201208282100: Sorry, stopping a job is not currently supported.')"><img src="/images/glyphicons/glyphicons_192_circle_remove_white.png" class="button-general-icon"><?=$this->localization->buttons->RemoveResulttext; ?></a></div>
<? } ?>
<BR clear=all> <BR clear=all>

<table id="results_list"><tr><td/></tr></table>
<div id="results_list_pager"></div>

<script type="text/javascript">
$(function(){
    jobChooser = new chooserResult($('#results_list'), {
        onSelectRow:    ResultRowOnClick
    });
});

var ResultRowOnClick = function (id) {
    id = jobChooser.fixId(id);
    $('#button_view_result').removeClass("disabled");
    $('#button_remove_result').removeClass("disabled");
    $('#button_view_result').data("selected_id", id);
    $('#button_remove_result').data("selected_id", id);
};    
</script>

