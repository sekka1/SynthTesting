/* 
 * A list of choosers/pickers.
 * Note you must have the following (in order):
 * <link rel="stylesheet" type="text/css" href="/inc/jqGrid/css/ui.jqgrid.css" />
 * <script type="text/javascript" src="/inc/jqGrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
 * <script type="text/javascript" src="/inc/jqGrid/js/jquery.jqGrid.min.js"></script>
 */

var jqGridDefaults = {
    scroll:         1       // Dynamically load more using AJAX
,   datatype:       "json"
,   height:         400
,   minWidth:       998
,   width:          '100%'
//,   height:         255
//,   width:          800
,   autowidth:      true
,   mtype:          "GET"
,   rowNum:         50
,   rownumbers:     false //true
,   rownumWidth:    40
,   gridview:       true    // Note docs say that if this is true, you can't use subgrid, but so far it seems to work -- MRR 20121207
,   pager:          '#datasource_list_pager'
,   sortname:       'last_modified'
,   sortorder:      'desc'
,   caption:        ''
,   jsonReader:     {
        root:           "rows"
    ,   page:           "page"
    ,   records:        "total"
    ,   id:             "id"
    ,   repeatitems:    false
    ,   total:          "totalpages"
    ,   subgrid: {
            root:           ""
        ,   row:            ""
        ,   repeatitems:    false        
        }
    }
,   viewrecords:    true
//,   multipleSearch: true
,   altRows:        true
,   subGridOptions: {
        plusicon:           "highlight ui-icon-triangle-1-e"
    ,   minusicon:          "ui-icon-triangle-1-se"
    ,   openicon:           "ui-icon-arrow-1-e"
    ,   selectOnExpand:     true
    ,   reloadOnExpand:     false // Caches the subgrids
    }
,   gridResize:     {minWidth:350,maxWidth:800,minHeight:80, maxHeight:350}
};

var chooserDataSource = function(elem, options) {
    var algoSDK = new AlgorithmsIO_SDK();
    var searchOptions = {
//            sopt: ['eq','ne','lt','le','gt','ge','bw','bn','ew','en','cn','nc','nu','nn'],
            sopt: ['eq','ne','bw','bn','ew','en','cn','nc']
    };
    var config = {
        url:        algoSDK.getDataSourceURL()
    ,   colNames: ['ID','Name','Type','Description', 'Location','Size','Created','Last Modified','Version'/*,'IP Address'*/]
    ,   colModel:[
                {name: 'id', searchoptions: {sopt: ['eq']}, fixed: true, width: 40, align: 'right'}
        ,       {name: 'name', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}
        ,       {name: 'type', searchoptions: searchOptions, fixed: true, width: 50, align: 'center'}
        ,       {name: 'description', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}        
        ,       {name: 'location', searchoptions: searchOptions, fixed: true, width: 80, align: 'center', formatter: chooserDataSourceLocation}
        ,       {name: 'size', searchoptions: {sopt: ['eq','le','ge']}, align: 'right', formatter: 'integer', fixed: true, width: 80}
        ,       {name: 'created', search: false, align: 'right', fixed: true, width: 120}
        ,       {name: 'last_modified', search: false, align: 'right', fixed: true, width: 120}        
//        ,       {name: 'filesystem_name'}
        ,       {name: 'version', search: false, fixed: true, width: 50, align: 'center'}
//        ,       {name: 'ip_address', width: 100, align: 'right'}
    ]    
    ,   pager:          '#datasource_list_pager'
    ,   caption:        'Data Sources (Double Click to View Data)'
    ,   idPrefix:       'ds_'
//    ,   prmNames:       [] // Names sent to server
    ,   subGrid:        true
    ,   subGridRowExpanded: function(pID,id) { 
            var myid = id.replace(config['idPrefix'], '');
            $.ajax({
                url: algoSDK.getDataSourceURL(),
                data: {id: myid},
                dataType: "json"
            }).then(
                function(mydata){
                    if(mydata['outputParams']) {
                        console.log(mydata['outputParams']);
                        $('#'+pID).append("<pre>"+JSON.stringify(mydata['outputParams'], null, '\t')+"</pre>");
                    } else {
                        $('#'+pID).append("No additional information is available.");
                    }
                }                
            );
        }
    };
    config = $.extend(true, {}, jqGridDefaults, config);
    $.extend(true, config, options);
    console.log("DEBUG201212071310: ",elem,config);
    jQuery(elem).jqGrid(config)
        .navGrid('#'+$(elem).attr('id')+'_pager',{del:false,add:false,edit:false})
        //.filterToolbar({autoSearch: true})
        //.jqGrid('columnChooser',{})
    ;
    //jQuery(elem).jqGrid('navGrid','#datasource_list_pager',{del:false,add:false,edit:false});
    //jQuery(elem).jqGrid('navGrid','#datasource_list_pager',{del:false,add:false,edit:false},{},{},{},{});
    //jQuery(elem).jqGrid('navGrid','#presize',{edit:false,add:false,del:false});
    //$(elem).jqGrid("gridResize", {});
    //jQuery(elem).gridResize({minWidth:350,maxWidth:800,minHeight:80, maxHeight:350});
    
    this.fixId = function(id) {
        // when given ds_## returns just ##
        var myid = id.replace(config['idPrefix'], '');
        return myid;
    };
}

var chooserDataSourceLocation = function (val, options, rowObject) {
    var ret = val;
        switch(val) {
                case "S3,algorithms.io":
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
                case "amazon":
                        ret='<img class="flexi_image" src="/images/partners/amazon_web_services.png">';
                        break;
                case "rackspace":
                        ret='<img class="flexi_image" src="/images/partners/rackspace.png">';
                        break;
                default: 
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
        }
        return ret;
}

var chooserAlgorithm = function(elem, options) {
    var algoSDK = new AlgorithmsIO_SDK();
    var searchOptions = {
//            sopt: ['eq','ne','lt','le','gt','ge','bw','bn','ew','en','cn','nc','nu','nn'],
            sopt: ['eq','ne','bw','bn','ew','en','cn','nc']
    };
    var config = {
        url:        algoSDK.getAlgorithmURL()
    ,   colNames: ['ID','Name','Type','Description', /*'Location',*/'Created','Last Modified'/*,'Version'*/]
    ,   colModel:[
                {name: 'id', searchoptions: {sopt: ['eq']}, fixed: true, width: 40, align: 'right'}
        ,       {name: 'name', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}
        ,       {name: 'type', searchoptions: searchOptions, fixed: true, width: 70, align: 'center'}
        ,       {name: 'description', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}        
//        ,       {name: 'location', searchoptions: searchOptions, width: 80, align: 'center', formatter: chooserDataSourceLocation}
        ,       {name: 'created', search: false, align: 'right', fixed: true, width: 120}
        ,       {name: 'last_modified', search: false, align: 'right', fixed: true, width: 120}        
//        ,       {name: 'version', search: false, width: 50, align: 'center'}
    ]    
    ,   pager:          '#'+$(elem).attr('id')+'_pager'
    ,   caption:        'Algorithms'
    ,   idPrefix:       'algo_'
//    ,   prmNames:       [] // Names sent to server
    ,   subGrid:        true
    ,   subGridRowExpanded: function(pID,id) { 
            var myid = id.replace(config['idPrefix'], '');
            $.ajax({
                url: algoSDK.getAlgorithmURL(),
                data: {id: myid},
                dataType: "json"
            }).then(
                function(mydata){
                    if(mydata['outputParams']) {
                        console.log(mydata['outputParams']);
                        $('#'+pID).append("<pre>"+JSON.stringify(mydata['outputParams'], null, '\t')+"</pre>");
                    } else {
                        $('#'+pID).append("No additional information is available.");
                    }
                }                
            );
        }
    };
    config = $.extend(true, {}, jqGridDefaults, config);
    $.extend(true, config, options);
    console.log("DEBUG201212071310: ",elem,config);
    jQuery(elem).jqGrid(config)
        .navGrid('#'+$(elem).attr('id')+'_pager',{del:false,add:false,edit:false})
    ;
    
    this.fixId = function(id) {
        // when given ds_## returns just ##
        var myid = id.replace(config['idPrefix'], '');
        return myid;
    };
}

var chooserAlgorithmLocation = function (val, options, rowObject) {
    var ret = val;
        switch(val) {
                case "S3,algorithms.io":
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
                case "amazon":
                        ret='<img class="flexi_image" src="/images/partners/amazon_web_services.png">';
                        break;
                case "rackspace":
                        ret='<img class="flexi_image" src="/images/partners/rackspace.png">';
                        break;
                default: 
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
        }
        return ret;
}

var chooserVisualization = function(elem, options) {
    var algoSDK = new AlgorithmsIO_SDK();
    var searchOptions = {
//            sopt: ['eq','ne','lt','le','gt','ge','bw','bn','ew','en','cn','nc','nu','nn'],
            sopt: ['eq','ne','bw','bn','ew','en','cn','nc']
    };
    var config = {
        url:        algoSDK.getVisualizationURL()
    ,   colNames: ['ID','Name','Type','Description', /*'Location',*/'Created','Last Modified'/*,'Version'*/]
    ,   colModel:[
                {name: 'id', searchoptions: {sopt: ['eq']}, fixed: true, width: 40, align: 'right'}
        ,       {name: 'name', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}
        ,       {name: 'type', searchoptions: searchOptions, fixed: true, width: 70, align: 'center'}
        ,       {name: 'description', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}        
//        ,       {name: 'location', searchoptions: searchOptions, width: 80, align: 'center', formatter: chooserDataSourceLocation}
        ,       {name: 'created', search: false, align: 'right', fixed: true, width: 120}
        ,       {name: 'last_modified', search: false, align: 'right', fixed: true, width: 120}        
//        ,       {name: 'version', search: false, width: 50, align: 'center'}
    ]    
    ,   pager:          '#'+$(elem).attr('id')+'_pager'
    ,   caption:        'Visualizations'
    ,   idPrefix:       'vis_'
//    ,   prmNames:       [] // Names sent to server
    ,   subGrid:        true
    ,   subGridRowExpanded: function(pID,id) { 
            var myid = id.replace(config['idPrefix'], '');
            $.ajax({
                url: algoSDK.getVisualizationURL(),
                data: {id: myid},
                dataType: "json"
            }).then(
                function(mydata){
                    if(mydata['outputParams']) {
                        console.log(mydata['outputParams']);
                        $('#'+pID).append("<pre>"+JSON.stringify(mydata['outputParams'], null, '\t')+"</pre>");
                    } else {
                        $('#'+pID).append("No additional information is available.");
                    }
                }                
            );
        }
    };
    config = $.extend(true, {}, jqGridDefaults, config);
    $.extend(true, config, options);
    console.log("DEBUG201212071310: ",elem,config);
    jQuery(elem).jqGrid(config)
        .navGrid('#'+$(elem).attr('id')+'_pager',{del:false,add:false,edit:false})
    ;
    
    this.fixId = function(id) {
        // when given ds_## returns just ##
        var myid = id.replace(config['idPrefix'], '');
        return myid;
    };
}

var chooserVisualizationLocation = function (val, options, rowObject) {
    var ret = val;
        switch(val) {
                case "S3,algorithms.io":
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
                case "amazon":
                        ret='<img class="flexi_image" src="/images/partners/amazon_web_services.png">';
                        break;
                case "rackspace":
                        ret='<img class="flexi_image" src="/images/partners/rackspace.png">';
                        break;
                default: 
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
        }
        return ret;
}

var chooserFlow = function(elem, options) {
    var algoSDK = new AlgorithmsIO_SDK();
    var searchOptions = {
//            sopt: ['eq','ne','lt','le','gt','ge','bw','bn','ew','en','cn','nc','nu','nn'],
            sopt: ['eq','ne','bw','bn','ew','en','cn','nc']
    };
    var config = {
        url:        algoSDK.getFlowURL()
    ,   colNames: ['ID','Name','Type','Description', /*'Location',*/'Created','Last Modified'/*,'Version'*/]
    ,   colModel:[
                {name: 'id', searchoptions: {sopt: ['eq']}, fixed: true, width: 40, align: 'right'}
        ,       {name: 'name', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}
        ,       {name: 'type', searchoptions: searchOptions, fixed: true, width: 70, align: 'center'}
        ,       {name: 'description', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}        
//        ,       {name: 'location', searchoptions: searchOptions, width: 80, align: 'center', formatter: chooserDataSourceLocation}
        ,       {name: 'created', search: false, align: 'right', fixed: true, width: 120}
        ,       {name: 'last_modified', search: false, align: 'right', fixed: true, width: 120}        
//        ,       {name: 'version', search: false, width: 50, align: 'center'}
    ]    
    ,   pager:          '#'+$(elem).attr('id')+'_pager'
    ,   caption:        'Workflows'
    ,   idPrefix:       'flow_'
//    ,   prmNames:       [] // Names sent to server
    ,   subGrid:        true
    ,   subGridRowExpanded: function(pID,id) { 
            var myid = id.replace(config['idPrefix'], '');
            $.ajax({
                url: algoSDK.getFlowURL(),
                data: {id: myid},
                dataType: "json"
            }).then(
                function(mydata){
                    if(mydata['outputParams']) {
                        console.log(mydata['outputParams']);
                        $('#'+pID).append("<pre>"+JSON.stringify(mydata['outputParams'], null, '\t')+"</pre>");
                    } else {
                        $('#'+pID).append("No additional information is available.");
                    }
                }                
            );
        }
    };
    config = $.extend(true, {}, jqGridDefaults, config);
    $.extend(true, config, options);
    console.log("DEBUG201212071310: ",elem,config);
    jQuery(elem).jqGrid(config)
        .navGrid('#'+$(elem).attr('id')+'_pager',{del:false,add:false,edit:false})
    ;
    
    this.fixId = function(id) {
        // when given ds_## returns just ##
        var myid = id.replace(config['idPrefix'], '');
        return myid;
    };
}

var chooserFlowLocation = function (val, options, rowObject) {
    var ret = val;
        switch(val) {
                case "S3,algorithms.io":
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
                case "amazon":
                        ret='<img class="flexi_image" src="/images/partners/amazon_web_services.png">';
                        break;
                case "rackspace":
                        ret='<img class="flexi_image" src="/images/partners/rackspace.png">';
                        break;
                default: 
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
        }
        return ret;
}

var chooserJob = function(elem, options) {
    var emptyMsgDiv = $('<div>There are currently no running jobs in the queue.</div>');
    var algoSDK = new AlgorithmsIO_SDK();
    var searchOptions = {
//            sopt: ['eq','ne','lt','le','gt','ge','bw','bn','ew','en','cn','nc','nu','nn'],
            sopt: ['eq','ne','bw','bn','ew','en','cn','nc']
    };
    var config = {
        url:        algoSDK.getJobURL()
    ,   colNames: ['ID','Name','Type','Description', /*'Location',*/'Run Time','Last Modified'/*,'Version'*/]
    ,   colModel:[
                {name: 'id', searchoptions: {sopt: ['eq']}, fixed: true, width: 40, align: 'right'}
        ,       {name: 'name', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}
        ,       {name: 'type', searchoptions: searchOptions, fixed: true, width: 70, align: 'center'}
        ,       {name: 'description', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}        
//        ,       {name: 'location', searchoptions: searchOptions, width: 80, align: 'center', formatter: chooserDataSourceLocation}
        ,       {name: 'created', search: false, align: 'right', fixed: true, width: 120, formatter: jobRunTime}
        ,       {name: 'last_modified', search: false, align: 'right', fixed: true, width: 120}        
//        ,       {name: 'version', search: false, width: 50, align: 'center'}
    ]    
    ,   pager:          '#'+$(elem).attr('id')+'_pager'
    ,   caption:        'Running Jobs'
    ,   idPrefix:       'job_'
//    ,   prmNames:       [] // Names sent to server
    ,   subGrid:        true
    ,   subGridRowExpanded: function(pID,id) { 
            var myid = id.replace(config['idPrefix'], '');
            $.ajax({
                url: algoSDK.getJobURL(),
                data: {id: myid},
                dataType: "json"
            }).then(
                function(mydata){
                    if(mydata['outputParams']) {
                        console.log(mydata['outputParams']);
                        $('#'+pID).append("<pre>"+JSON.stringify(mydata['outputParams'], null, '\t')+"</pre>");
                    } else {
                        $('#'+pID).append("No additional information is available.");
                    }
                }                
            );
        }
    ,   loadComplete: function () {
            var count = $(elem).getGridParam();
            var ts = $(elem)[0];
            if (ts.p.reccount === 0) {
                //$(elem).hide();
                emptyMsgDiv.show();
            } else {
                $(elem).show();
                emptyMsgDiv.hide();
            }
        }
    };
    config = $.extend(true, {}, jqGridDefaults, config);
    $.extend(true, config, options);
    console.log("DEBUG201212071310: ",elem,config);
    jQuery(elem).jqGrid(config)
        .navGrid('#'+$(elem).attr('id')+'_pager',{del:false,add:false,edit:false})
    ;
    emptyMsgDiv.insertAfter($(elem).parent());
    emptyMsgDiv.css('text-align', 'center');
    emptyMsgDiv.css('width', '100%');
    emptyMsgDiv.css('padding-top',$(emptyMsgDiv).parent().height()/2);

    this.fixId = function(id) {
        // when given ds_## returns just ##
        var myid = id.replace(config['idPrefix'], '');
        return myid;
    };
}

var jobRunTime = function (val, options, rowObject) {
    var mytime = val;
    var span = $("<SPAN></SPAN");
    jQuery.timeago.settings.strings.suffixAgo = "";
    $(span).attr("title", new Date(mytime).toISOString());
    $(span).timeago();
    var celDiv = $("<div></div>");
    var running = $("<DIV class='loading_icon_small'></DIV>");
    $(celDiv).append(running);
    $(celDiv).append(span);

    //JobRowOnClick(celDiv,id);
    return celDiv.html();
}
        
var chooserJobLocation = function (val, options, rowObject) {
    var ret = val;
        switch(val) {
                case "S3,algorithms.io":
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
                case "amazon":
                        ret='<img class="flexi_image" src="/images/partners/amazon_web_services.png">';
                        break;
                case "rackspace":
                        ret='<img class="flexi_image" src="/images/partners/rackspace.png">';
                        break;
                default: 
                        ret='<img class="flexi_image" src="/images/logo_isolated.png">';
                        break;
        }
        return ret;
}

var chooserResult = function(elem, options) {
    var algoSDK = new AlgorithmsIO_SDK();
    var searchOptions = {
//            sopt: ['eq','ne','lt','le','gt','ge','bw','bn','ew','en','cn','nc','nu','nn'],
            sopt: ['eq','ne','bw','bn','ew','en','cn','nc']
    };
    var config = {
        url:        algoSDK.getResultURL()
    ,   colNames: ['ID','Name','Type','Description', /*'Location',*/'Run Time','Last Modified'/*,'Version'*/]
    ,   colModel:[
                {name: 'id', searchoptions: {sopt: ['eq']}, fixed: true, width: 40, align: 'right'}
        ,       {name: 'name', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}
        ,       {name: 'type', searchoptions: searchOptions, fixed: true, width: 70, align: 'center'}
        ,       {name: 'description', searchoptions: searchOptions, cellattr: function() { return 'style="white-space: normal;"'}}        
//        ,       {name: 'location', searchoptions: searchOptions, width: 80, align: 'center', formatter: chooserDataSourceLocation}
        ,       {name: 'created', search: false, align: 'right', fixed: true, width: 120, /*formatter: jobRunTime*/}
        ,       {name: 'last_modified', search: false, align: 'right', fixed: true, width: 120}        
//        ,       {name: 'version', search: false, width: 50, align: 'center'}
    ]    
    ,   pager:          '#'+$(elem).attr('id')+'_pager'
    ,   caption:        'Running Jobs'
    ,   idPrefix:       'job_'
//    ,   prmNames:       [] // Names sent to server
    ,   subGrid:        true
    ,   subGridRowExpanded: function(pID,id) { 
            var myid = id.replace(config['idPrefix'], '');
            $.ajax({
                url: algoSDK.getResultURL(),
                data: {id: myid},
                dataType: "json"
            }).then(
                function(mydata){
                    if(mydata['outputParams']) {
                        console.log(mydata['outputParams']);
                        $('#'+pID).append("<pre>"+JSON.stringify(mydata['outputParams'], null, '\t')+"</pre>");
                    } else {
                        $('#'+pID).append("No additional information is available.");
                    }
                }                
            );
        }
    };
    config = $.extend(true, {}, jqGridDefaults, config);
    $.extend(true, config, options);
    console.log("DEBUG201212071310: ",elem,config);
    jQuery(elem).jqGrid(config)
        .navGrid('#'+$(elem).attr('id')+'_pager',{del:false,add:false,edit:false})
    ;
    
    this.fixId = function(id) {
        // when given ds_## returns just ##
        var myid = id.replace(config['idPrefix'], '');
        return myid;
    };
}

function resizeChoosers() {
            if (grid = $('.ui-jqgrid-btable:visible')) {
            grid.each(function(index) {
                gridId = $(this).attr('id');
                gridParentWidth = $('#gbox_' + gridId).parent().width();
                $('#' + gridId).setGridWidth(gridParentWidth-5);
            });
        }
}

// We need to add a click event so that the choosers are resized when visible.. Wish javascript had a "whenVisible" event...
$(window).click(function(){setTimeout(resizeChoosers,200)});
