function algorithmIDE(algorithmID) {
    this.SDK = new AlgorithmsIO_SDK();
    this.myData = {};
    this.dirty = true;
    this.myid = algorithmID;
    
    this.id = function(newId) {
        if(newId) {
            this.myid = newId;
        }
        return this.myid;
    }
    
    this.isDirty = function() {
        return this.dirty;
    }
    
    this.setClean = function() {
        console.log("DEBUG201208241450: Setting Clean");
        this.dirty = false;
        $('#button_saveAlgorithm').addClass("disabled"); // No need to save as we are clean
    }
    
    this.setDirty = function() {
        console.log("DEBUG201208241451: Setting Dirty");
        this.dirty = true;
        $('#button_saveAlgorithm').removeClass("disabled"); // Show Save Button
    }
    
    this.retrieve = function() {
        // Get the algorithm data
        var algoIDE = this;
        return $.when(this.SDK.getAlgorithm(this.id())).then(function(result){algoIDE.updateFromObj(result);}).then(function(result) {algoIDE.setClean(); algoIDE.updateSaveTime(); algoIDE.previewRefresh.call(algoIDE); console.log("Done retrieve");});
    };

    this.retrieveDataSource = function(ds_id) {
        var datasource_id = ds_id || this.myData["sampleDataSourceId"];
        var algoIDE = this;
        return this.SDK.getDataSource(datasource_id).pipe(function(result) {
            algoIDE.setDataSource(result);
            return result;   
        });
    };
    
    this.updateFromObj = function(data) {
        console.log("DEBUG201208211544: ",data,this);
        this.myData = data;
        this.setName(data["name"]);
        this.setDescription(data["description"]);
        if(typeof data["details"] != "undefined") {
            this.setDetails(data["details"]);
        }                
        if(typeof data["sourceCode"] != "undefined") {
            this.setSourceCode(data["sourceCode"]);
        }
        if(typeof data["inputParams"] != "undefined") {
            // Uncheck the use datasource as inputParams checkbox
            $('#form_inputParams_from_DataSource').attr('checked', false);
            this.setInputParams(data["inputParams"]);
        }
        if(typeof data["outputParams"] != "undefined") {
            this.setOutputParams(data["outputParams"]);        
        }        
        if(data["sampleDataSourceId"]) {
            this.setDataSourceId(data["sampleDataSourceId"]);
        }
        if(data["tags"]) {
            this.setTags(data["tags"]);
        }
    }
    
    this.get = function(key) {
        if(key) {
            if(this.myData[key]) {
                return this.myData[key];
            } else {
                return "";
            }
        }
        return this.myData;
    }
    
    this.set = function(key, value) {
        this.myData[key] = value;
        this.setDirty();
    }
    
    this.save = function() {
        // Save this beast
        var algoIDE = this;
        var afterSave = function(result) {
            console.log("DEBUG201208241552: afterSave response: ");
            console.log(result); 
            if(algoIDE.id()) {
                console.log("DEBUG201209031910: Already have an id="+algoIDE.id());
                // Do nothing if we have an id already 
            } else {
                if(result["id"]) {
                    algoIDE.id(result["id"]);
                    var curlocation = window.location;
                    window.location.search = '?algorithm_id='+result["id"];
                } else {
                    alert("ERROR201211091957: Saving of new algorithm failed, as we did not get a result.");
                    console.log("ERROR201208241558: Saving a new algoirthm did not return an ID:");
                    console.log(result);
                }
            }
            algoIDE.setClean();      

            var previewRefresh = function() { algoIDE.previewRefresh.call(algoIDE); }
            setTimeout(previewRefresh, 1000);
        }
        var fail = function(){alert('hi');};
        $.when(this.SDK.saveAlgorithm(this.id(), this.myData)).then(afterSave,fail).then(algoIDE.updateSaveTime, fail);
        //$.when(this.SDK.saveAlgorithm(this.id(), this.myData)).then([afterSave, algoIDE.updateSaveTime]);
        
    }

    this.previewRefresh = function() { 
        // Refresh the preview window
        console.log("DEBUG201210182019: Updating preview");
        var visualizationPreviewURL = "/visualization/index?visualization_id="+this.id()+"&datasource_id="+this.myData["sampleDataSourceId"]; 
        //$('#visualization_preview').attr("src", $('#visualization_preview').attr("src"));
        $('#visualization_preview').attr("src", visualizationPreviewURL); 
    };

    this.updateSaveTime = function() {
        var oldtime = $('#last_saved').attr("title");
        var newtime = new Date().toISOString();
        console.log("DEBUG201210182247: Updating save time from "+oldtime+" to "+newtime);
        //$('#last_saved').attr("title", newtime);
        //$('#last_saved').timeago();
        $('#last_saved').attr("title", newtime).data("timeago",null).timeago();
        console.log("DEBUG201210221206: New last_saved time: " +$('#last_saved').attr("title"));
        //$('#last_saved').html(jQuery.timeago(newtime));
    }
        
    this.setSourceCode = function(sourceCode) {
        this.myData["sourceCode"] = sourceCode;
        sourceEditor.setValue(sourceCode);
        this.setDirty();
        //$('#code').val(sourceCode);
    }
    
    this.getSourceCode = function() {
        return this.get("sourceCode");
    }
    
    this.setHTMLCode = function (htmlCode) {
        console.log("DEBUG201210221629: in setHTMLCode",htmlCode);
        this.myData["HTMLCode"] = htmlCode;
        HTMLEditor.setValue(htmlCode);
        this.setDirty();        
    }
    
    this.getHTMLCode = function() {
        return this.get("HTMLCode");
    }
    
    this.setCSSCode = function (cssCode) {
        this.myData["CSSCode"] = cssCode;
        CSSEditor.setValue(cssCode);
        this.setDirty();        
    }
    
    this.getCSSCode = function() {
        return this.get("CSSCode");
    }
    
    this.setDataSource = function(data) {
        this.datasource = data;
        console.log(data);
        if($('#form_inputParams_from_DataSource').is(':checked')) {
            console.log("DEBUG201208241457: In setDataSource checked");
            var inputparams = JSON.stringify(data["outputParams"], null, '\t');
            $('#form_inputparams').val(inputparams);
            this.setInputParams(inputparams);
            this.setDirty();
        } else {
            console.log("DEBUG201208241458: Not updating inputParams with the DataSource outputParams");
        }
    }
    
    this.setDataSourceId = function(datasource_id) {
        this.setDirty();
        this.myData["sampleDataSourceId"] = datasource_id;
        return $.when(this.retrieveDataSource()).then(doInputParamsTable); // Note .when should call functions, .then should have function passed in
        //return this.retrieveDataSource();
    }
    
    this.setSampleDataSourceId = function(datasource_id) {
        this.setDirty();
        return this.setDataSourceId(datasource_id);
    }
    
    this.setName = function(name) {
        this.setDirty();
        this.myData["name"] = name;
        $('#algorithm_name').html(name); // Title at top
        $('#form_algorithm_name').val(name); // Popup form
    }
    
    this.setDescription = function(description) {
        this.setDirty();
        this.myData["description"] = description;
        $('#form_algorithm_description').val(description); // Popup form
    }

    this.setDetails = function(details) {
        this.setDirty();
        this.myData["details"] = details;
        $('#form_visualization_details').val(details); // Popup form
    }
    
    this.setTags = function(tags) {
        this.setDirty();
        this.myData["tags"] = tags;
        $('#form_algorithm_tags').val(tags); // Popup form
    }
    
    this.setInputParams = function(inputparams) {
        console.log("DEBUG201208300137: Setting inputparams");
        this.setDirty();
        if(typeof inputparams == "string") {
            inputparams = jQuery.parseJSON(inputparams);
        }
        this.myData["inputParams"]=inputparams;
        $('#form_inputparams').val(JSON.stringify(inputparams, null, '\t'));
                var inputparams_jstree = paramsToJSTree(myIDE.get("inputParams"));
        console.log("DEBUG201210241151: input_jstree=",myIDE.myData,inputparams_jstree);
	$("#inputparams").jstree({
                "themes" : {
                         "theme" : "apple",
                         "dots"  : true,
                         "icons" : false
                },

		"json_data" : {
                            "data": inputparams_jstree
                            /*
                            "ajax" : {
				"url" : "/ide/inputparams",
				"data" : function (n) { 
					return { id : myIDE.id() ? myIDE.id() : 0 }; 
				}
                            }
                            */			
		},
                "set_theme" : "apple",
		"plugins" : [ "themes", "json_data", "ui" ]
	}).bind("select_node.jstree", inputparam_onclick);
    }
    
    this.setOutputParams = function(outputparams) {
        this.setDirty();
        if(typeof outputparams == "string") {
            outputparams = jQuery.parseJSON(outputparams);
        }
        this.myData["outputParams"]=outputparams;
        $('#form_outputparams').val(JSON.stringify(outputparams, null, '\t'));
    }    
}   

/********************* BEGIN Layout ***************************/

	var outerLayout, innerLayout;

	$(document).ready( function() {
		// create the OUTER LAYOUT
		outerLayout = $("body").layout( layoutSettings_Outer );

		// save selector strings to vars so we don't have to repeat it
		// must prefix paneClass with "body > " to target ONLY the outerLayout panes
		var westSelector = "body > .ui-layout-west"; // outer-west pane
		var eastSelector = "body > .ui-layout-east"; // outer-east pane
		 // CREATE SPANs for pin-buttons - using a generic class as identifiers
		$("<span></span>").addClass("pin-button").prependTo( westSelector );
		$("<span></span>").addClass("pin-button").prependTo( eastSelector );

		// BIND events to pin-buttons to make them functional
		outerLayout.addPinBtn( westSelector +" .pin-button", "west");
		outerLayout.addPinBtn( eastSelector +" .pin-button", "east");

		 // CREATE SPANs for close-buttons - using unique IDs as identifiers
		$("<span></span>").attr("id", "west-closer" ).prependTo( westSelector );
		$("<span></span>").attr("id", "east-closer").prependTo( eastSelector );
		// BIND layout events to close-buttons to make them functional
		outerLayout.addCloseBtn("#west-closer", "west");
		outerLayout.addCloseBtn("#east-closer", "east");


		/* Create the INNER LAYOUT - nested inside the 'center pane' of the outer layout
		 * Inner Layout is create by createInnerLayout() function - on demand
		 *
			innerLayout = $("div.pane-center").layout( layoutSettings_Inner );
		 *
		 */


		// Prevent hyperlinks from reloading page when a 'base.href' is set // Not sure if this is needed - MRR20121022
		$("a").each(function () {
			var path = document.location.href;
			if (path.substr(path.length-1)=="#") path = path.substr(0,path.length-1);
			if (this.href.substr(this.href.length-1) == "#") this.href = path +"#";
		});
                
                createInnerLayout();

                $("#accordion1").accordion({
			fillSpace:	true
                //,       heightStyle:    "fill"
		});
                
                $("#accordion2").accordion({
			fillSpace:	true
                //,       heightStyle:    "fill"
		});
	});

var testResize = function (x, ui) {
        console.log("DEBUG201211032317: resize", x, ui);
	// may be called EITHER from layout-pane.onresize OR tabs.show
	var $P = ui.jquery ? ui : $(ui.panel);
	// find all VISIBLE accordions inside this pane and resize them
	$P.find(".ui-accordion:visible").each(function(){
		var $E = $(this);
		if ($E.data("accordion"))
			$E.accordion("resize");
                $E.height(500);
                $E.accordion("resize");
	});
        // We seem to need a small delay here because 
        setTimeout(function(){editorResize(x,ui);}, 200);
        //editorResize(x,ui);
};

	/*
	*#######################
	* INNER LAYOUT SETTINGS
	*#######################
	*/
	layoutSettings_Inner = {
		//applyDefaultStyles:				true // basic styling for testing & demo purposes
		minSize:						20 // TESTING ONLY
	,	spacing_closed:					14
	,	fxName:						"slide" // do not confuse with "slidable" option!
	,	fxSpeed_open:					1000
	,	fxSpeed_close:					2500
	,	fxSettings_open:				{ easing: "easeInQuint" }
	,	fxSettings_close:				{ easing: "easeOutQuint" }
        ,       autoResize:                             true
        ,	resizerClass:			"resizer"
        ,	togglerClass:			"toggler"     
        ,       center: {
                minWidth:                               200
        ,       minHeight:                              200
        }
        ,       north: {
                //        togglerLength_closed:         -1 // = 100% - so cannot 'slide open'
			resizable: 				false
		,	slidable:				true                        
                ,	slideTrigger_open:		"mouseover"
                ,       spacing_closed:                 5
                ,       onresize_end:                   testResize
                ,       onclose:                        testResize
                ,       onopen:                         testResize
                ,       onhide:                         testResize
                ,       onshow:                         testResize
        }
        ,       east: {
                        size:                           "50%"
                ,	initClosed:				true                        
                ,       resizable:                      true
                ,	slideTrigger_open:		"mouseover"
                ,       onresize_end:                   testResize
                ,       onclose:                        testResize
                ,       onopen:                         testResize
                ,       onhide:                         testResize
                ,       onshow:                         testResize            
                }
        ,       south: {
                        size:                           "25%"
                //,	togglerLength_closed:           -1          
                //,       fxName:				"drop"
                //,       fxSpeed_open:			500
                ,       fxSpeed_close:			500
                ,       spacing_closed:                 5
                ,       resizable:                      true
                ,       onresize_end:                   testResize
                ,       onclose:                        testResize
                ,       onopen:                         testResize
                ,       onhide:                         testResize
                ,       onshow:                         testResize
                //,       onresize:                       testResize
                }
	};



	/*
	*#######################
	* OUTER LAYOUT SETTINGS
	*#######################
	*/
	var layoutSettings_Outer = {
		name: "outerLayout" // NO FUNCTIONAL USE, but could be used by custom code to 'identify' a layout
		// options.defaults apply to ALL PANES - but overridden by pane-specific settings
	,	defaults: {
			size:					"auto"
		,	minSize:				50
		,	paneClass:				"pane" 		// default = 'ui-layout-pane'
		,	resizerClass:			"resizer"	// default = 'ui-layout-resizer'
		,	togglerClass:			"toggler"	// default = 'ui-layout-toggler'
		,	buttonClass:			"button"	// default = 'ui-layout-button'
		,	contentSelector:		".content"	// inner div to auto-size so only it scrolls, not the entire pane!
		,	contentIgnoreSelector:          "span"		// 'paneSelector' for content to 'ignore' when measuring room for content
		,	togglerLength_open:		35			// WIDTH of toggler on north/south edges - HEIGHT on east/west edges
		,	togglerLength_closed:           35			// "100%" OR -1 = full height
		,	hideTogglerOnSlide:		false// true		// hide the toggler when pane is 'slid open'
		,	togglerTip_open:		"Close This Pane"
		,	togglerTip_closed:		"Open This Pane"
		,	resizerTip:				"Resize This Pane"
		//	effect defaults - overridden on some panes
		,	fxName:					"slide"		// none, slide, drop, scale
		,	fxSpeed_open:			750
		,	fxSpeed_close:			1500
		,	fxSettings_open:		{ easing: "easeInQuint" }
		,	fxSettings_close:		{ easing: "easeOutQuint" }
	}
	,	north: {
			spacing_open:			5			// cosmetic spacing
		,	togglerLength_open:		35			// HIDE the toggler button
		,	togglerLength_closed:           35			// "100%" OR -1 = full width of pane
		,	resizable: 				false
		,	slidable:				true
		//	override default effect
		,	fxName:					"slide"
		}
	,	south: {
			maxSize:				200
		,	spacing_closed:			0			// HIDE resizer & toggler when 'closed'
		,	slidable:				false		// REFERENCE - cannot slide if spacing_closed = 0
		,	initClosed:				true
		//	CALLBACK TESTING...
		,	onhide_start:			function () { return confirm("START South pane hide \n\n onhide_start callback \n\n Allow pane to hide?"); }
		,	onhide_end:				function () { alert("END South pane hide \n\n onhide_end callback"); }
		,	onshow_start:			function () { return confirm("START South pane show \n\n onshow_start callback \n\n Allow pane to show?"); }
		,	onshow_end:				function () { alert("END South pane show \n\n onshow_end callback"); }
		,	onopen_start:			function () { return confirm("START South pane open \n\n onopen_start callback \n\n Allow pane to open?"); }
		,	onopen_end:				function () { alert("END South pane open \n\n onopen_end callback"); }
		,	onclose_start:			function () { return confirm("START South pane close \n\n onclose_start callback \n\n Allow pane to close?"); }
		,	onclose_end:			function () { alert("END South pane close \n\n onclose_end callback"); }
		//,	onresize_start:			function () { return confirm("START South pane resize \n\n onresize_start callback \n\n Allow pane to be resized?)"); }
		,	onresize_end:			function () { alert("END South pane resize \n\n onresize_end callback \n\n NOTE: onresize_start event was skipped."); }
		}
	,	west: {
			size:					250
		,	spacing_closed:			21			// wider space when closed
		,	togglerLength_closed:	21			// make toggler 'square' - 21x21
		,	togglerAlign_closed:	"top"		// align to top of resizer
		,	togglerLength_open:		21			// NONE - using custom togglers INSIDE west-pane
		,	togglerTip_open:		"Close West Pane"
		,	togglerTip_closed:		"Open West Pane"
		,	resizerTip_open:		"Resize West Pane"
		//,	slideTrigger_open:		"click" 	// default
		,	slideTrigger_open:		"mouseover"
		,	initClosed:				true
		//	add 'bounce' option to default 'slide' effect
		,	fxSettings_open:		{ easing: "easeOutBounce" }
                ,       onresize_end:                       testResize //$.layout.callbacks.resizePaneAccordions
		}
	,	east: {
			size:					250
		,	spacing_closed:			21			// wider space when closed
		,	togglerLength_closed:	21			// make toggler 'square' - 21x21
		,	togglerAlign_closed:	"top"		// align to top of resizer
		,	togglerLength_open:		0 			// NONE - using custom togglers INSIDE east-pane
		,	togglerTip_open:		"Close East Pane"
		,	togglerTip_closed:		"Open East Pane"
		,	resizerTip_open:		"Resize East Pane"
		,	slideTrigger_open:		"mouseover"
		,	initClosed:				true
		//	override default effect, speed, and settings
		//,	fxName:					"drop"
		//,	fxSpeed:				"normal"
		,	fxSettings_open:		{ easing: "easeOutBounce" } // nullify default easing
                ,       onresize_end:                       testResize //$.layout.callbacks.resizePaneAccordions
		}
	,	center: {
			paneSelector:			"#mainContent" 			// sample: use an ID to select pane instead of a class
		,	minWidth:				200
		,	minHeight:				200
		}
	};

function createInnerLayout () {
	innerLayout = $( outerLayout.options.center.paneSelector ).layout( layoutSettings_Inner );
}
/********************* END Layout ***********************

/********************* IDE UI Helper Functions ************************/
function restoreIDEDiv(fadeOutObj) {
    $(fadeOutObj).fadeOut('slow');
    removeBlackOut();
}

function showWizardPopup(fadeInObj, callback) {
    blackOut();
    if(typeof callback !== "function") {
        console.log("Typeof"+typeof callback);
        callback = function () {/* No Callback Do Nothing */};
    }
    //fadeInObj.center();
    //$('#IDE_container').fadeOut('slow'); $(fadeInObj).center().delay(800).fadeIn('slow', callback);
    $(fadeInObj).center().fadeIn('slow', callback);
}

function validateAlgorithmProperties() {
    $('#button_properties_close').addClass('disabled');
    if($('#form_algorithm_name').val().length > 10) {
        // Should really do ajax to validate name hasn't been taken? - MRR20120820
        if($('#form_algorithm_description').val().length > 20) {
            $('#button_properties_close').removeClass('disabled');
        }
    } 
}

function closeAlgorithmProperties() {
    myIDE.setName($('#form_algorithm_name').val());
    myIDE.setDescription($('#form_algorithm_description').val());
    myIDE.setDetails($('#form_visualization_details').val());
    myIDE.setTags($('#form_algorithm_tags').val());
    restoreIDEDiv($('#div_algorithm_properties'));
    myIDE.save(); // TODO: Not sure I like this global reference here - MRR20120903
}
function closeInputParams() {
    myIDE.setInputParams($('#form_inputparams').val());
    restoreIDEDiv($('#div_inputparams_list'));
    myIDE.save(); // TODO: Not sure I like this global reference here - MRR20120903
}
function closeOutputParams() {
    myIDE.setOutputParams($('#form_outputparams').val());
    restoreIDEDiv($('#div_outputparams_list'));
    myIDE.save(); // TODO: Not sure I like this global reference here - MRR20120903
}
function closeDataSourceList() {
    //myIDE.setDataSource(); // Already set when clicking on the row
    restoreIDEDiv($('#div_datasources_list'));
    myIDE.save(); // TODO: Not sure I like this global reference here - MRR20120903
}

