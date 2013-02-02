<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<? if ($this->security->canRead("Jobs")) { ?>
<div class="button-general"><a href="/jobs/index"><img src="/images/glyphicons/MRR_Round_Play_white.png" class="button-general-icon"><?=$this->localization->buttons->AddJobtext; ?></a></div>
<div class="button-spacer"></div>
<? } ?>
<? if ($this->security->canWrite("Jobs")) { ?>
<div id="button_cancel_job" class="button-general disabled"><a href="javascript:void(0);" onclick="alert('ERROR201208282100: Sorry, stopping a job is not currently supported.')"><img src="/images/glyphicons/MRR_Round_Stop_white.png" class="button-general-icon"><?=$this->localization->buttons->StopJobtext; ?></a></div>
<? } ?>
<BR clear=all> <BR clear=all>

<table id="jobs_list"><tr><td/></tr></table>
<div id="jobs_list_pager"></div>

<script type="text/javascript">
$(function(){
    jobChooser = new chooserJob($('#jobs_list'), {
        onSelectRow:    JobRowOnClick
    });
});

var JobRowOnClick = function (id) {
    id = jobChooser.fixId(id);
    $('#button_cancel_job').removeClass("disabled");
    $('#button_cancel_job').data("selected_id", id);
};
</script>

