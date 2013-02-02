<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<? if ($this->security->canRead("Market")) { ?>
<div class="button-general"><a href="/market/algorithms"><img src="/images/glyphicons/glyphicons_027_search_white.png" class="button-general-icon"><?=$this->localization->buttons->FindAlgorithmtext; ?></a></div>
<div class="button-spacer"></div>
<? } ?>
<? if ($this->security->canWrite("Algorithms")) { ?>
<div class="button-general"><a href="/ide/index" zonclick="startWizard();"><img src="/images/glyphicons/glyphicons_190_circle_plus_white.png" class="button-general-icon"><?=$this->localization->buttons->AddAlgorithmtext; ?></a></div>
<div class="button-spacer"></div>
<div id="button_modifyAlgorithm" class="button-general disabled"><a href="javascript:void(0);" onclick="window.location='/ide/index?algorithm_id='+$(this).parent().data('selected_id');"><img src="/images/glyphicons/glyphicons_030_pencil_white.png" class="button-general-icon"><?=$this->localization->buttons->ModifyAlgorithmtext; ?></a></div> 
<? } ?>
<BR clear=all> <BR clear=all>

<table id="algorithm_list"><tr><td/></tr></table>
<div id="algorithm_list_pager"></div>

<script type="text/javascript">
var algorithmChooser;
$(function(){
    algorithmChooser = new chooserAlgorithm($('#algorithm_list'), {
        onSelectRow:    AlgorithmRowOnClick
    });
});

var AlgorithmRowOnClick = function (id) {
    id = algorithmChooser.fixId(id);
    $('#button_modifyAlgorithm').removeClass("disabled");
    $('#button_modifyAlgorithm').data("selected_id", id);
};
</script>

