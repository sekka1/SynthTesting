<script type="text/javascript" src="/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<script>
    $(function() {
        
    });
</script>

<FORM id="form_flowProperties" name="form_flowProperties">
        <div id="properties_tabs">
        <ul>
            <li><a href="#properties_general">General</a></li>
            <li><a href="#properties_details">Details</a></li>
        </ul>
        <div id="properties_general">
        <LABEL for="form_flow_name"><? echo $this->localization->IDE->Properties->Form->Labels->Title; ?></LABEL>
        <INPUT name="form_flow_name" id="form_flow_name" TYPE="text" SIZE="40" style="width: 500px;" onkeyup="validateFlowProperties();">
        
        <LABEL for="form_flow_description"><? echo $this->localization->IDE->Properties->Form->Labels->Description; ?></LABEL>
        <TEXTAREA name="form_flow_description" id="form_flow_description" TYPE="text" SIZE="40" style="width: 500px; height: 100px;" onkeyup="validateFlowProperties();"></TEXTAREA>
        </div>
        <div id="properties_details">
                    <TEXTAREA id="form_flow_details" class="htmlEditor" style="width:100%"></TEXTAREA><BR>
                            <a href="javascript:void(0);" onclick="$('#form_flow_hidden_workflow').slideDown('slow')">Details</a>
                            <textarea id="form_flow_hidden_workflow" style="display:none;"></textarea>
            
        <SCRIPT>
$(function(){
//tinyMCE.init({
$('.htmlEditor').tinymce({
        // General options
        script_url: '/js/tiny_mce/tiny_mce.js',
        mode : "specific_textareas",
        editor_selector: "htmlEditor",
        theme : "advanced",
        plugins : "imagemanager,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        //theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "/css/style.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
});


        </SCRIPT>       
        </div>
    </div>
        <BR clear="all">
</FORM>