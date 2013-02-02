/********************* Dashboard Helper Functions ************************/
function restoreTabsDiv(fadeOutObj) {
    $(fadeOutObj).fadeOut('slow');$('#dashboard_tabs').delay(800).fadeIn('slow');
    removeBlackOut();
}

function showWizardPopup(fadeInObj, callback) {
    blackOut();
    if(typeof callback !== "function") {
        console.log("Typeof"+typeof callback);
        callback = function () {/* No Callback Do Nothing */};
    }
    $('#dashboard_tabs').fadeOut('slow');   
    $(fadeInObj).center().delay(800).fadeIn('slow', callback);
}

