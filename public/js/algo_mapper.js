



/***************************   ************************/
    $(function() {

	//document.onselectstart = function () { return false; };				
	//jsPlumb.SVG
        //jsPlumb.setRenderMode(jsPlumb.CANVAS);
        
    

    });


function start_mapper(sourceArgs, destArgs) {
        var sdk = new AlgorithmsIO_SDK();
        
        setJSPlumbDefaults();
        var myMapper = $('#div_mapper').data('flowItem');
        var sourceParamsURL=""; // params from the left datasource/algorithm
        var targetParamsURL=""; // after transformation the outgoing params for the right datasource/algorithm
        
        if(myMapper.previous() && (myMapper.previous().iconType == "DB")) {
            var sourceID = myMapper.previous().getData("datasource_id");
            //FIXME - Hardcoded URL MRR20120818 // Is this actually in use??? Looks like we are using the sdk directly below.
            sourceParamsURL= sdk.getAPIURL()+"/datasources/"+sourceID+"?authToken=541b393f52b097d3e589ea63ccdfd49e";
        } else if(myMapper.previous() && (myMapper.previous().iconType == "Algorithm")) {
            var sourceID = myMapper.previous().getData("algorithm_id");
            //FIXME - Hardcoded URL MRR20120818
            sourceParamsURL= sdk.getAPIURL()+"/algorithms/"+sourceID+"?authToken=541b393f52b097d3e589ea63ccdfd49e";            
        } else {
            console.log("ERROR201208181410: A mapper must have a datasource or algorithm as a source.");
            alert("ERROR201208181410: A mapper must have a datasource or algorithm as a source.");
            console.log(myMapper.previous());
        }
        
        if(myMapper.next() && myMapper.next().iconType == "Algorithm") {
            var targetID = myMapper.next().getData("algorithm_id");
            //FIXME - Hardcoded URL MRR20120818
            targetParamsURL= sdk.getAPIURL()+"/algorithms/"+targetID+"?authToken=541b393f52b097d3e589ea63ccdfd49e";
        } else if(myMapper.next() && myMapper.next().iconType == "Interface") {
            var targetID = myMapper.next().getData("visualization_id");
            targetParamsURL= sdk.getAPIURL()+"/visualizations/"+targetID+"?authToken=541b393f52b097d3e589ea63ccdfd49e";
        } else {
            alert("A mapper must have a datasource or algorithm as a target.");
        }
        
        /*
        var loadRight = function() { return $.getJSON('/ide/inputparams').pipe(function(data, status, xhr) {
                console.log("In loadRight");
                addMapperFieldsFromJSON(data, "right");
                return data;
                //fixMapperEndPoints();
            }
            );
        }
        */
        var loadRight = function() {
            console.log("DEBUG201208280910: In loadRight");
            console.log("targetID="+targetID);
            if(myMapper.next() && myMapper.next().iconType == "Algorithm") {
                return sdk.getAlgorithm(targetID).pipe(function(data, status, xhr) {  
                    console.log("DEBUG201208280914: data=***************************************");
                    console.log(data);
                    addMapperFieldsFromJSON(data["inputParams"], "right");
                });
            } else if(myMapper.next() && myMapper.next().iconType == "Interface") {
                return sdk.getVisualization(targetID).pipe(function(data, status, xhr) {  
                    console.log("DEBUG201208280914: data=***************************************");
                    console.log(data);
                    addMapperFieldsFromJSON(data["inputParams"], "right");
                });                
            } else {
                alert("ERROR201211181421: The mapper does not know how to deal with type "+myMapper.next().iconType);
            }
        };       
        
        var loadLeft = function() {
            console.log("DEBUG201208181713: In loadLeft");
            console.log("sourceID="+sourceID);
            if(myMapper.previous() && (myMapper.previous().iconType == "DB")) {
                return sdk.getDataSource(sourceID).pipe(function(data, status, xhr) {
                    console.log("DEBUG201208181740: data=");
                    console.log(data);
                    addMapperFieldsFromJSON(data["outputParams"], "left");
                });
            } else if(myMapper.previous() && (myMapper.previous().iconType == "Algorithm")) {
                 return sdk.getAlgorithm(sourceID).pipe(function(data, status, xhr) {
                    console.log("DEBUG201208181740: data=");
                    console.log(data);
                    addMapperFieldsFromJSON(data["outputParams"], "left");
                });               
            } else {
                alert("The mapper does not know how to process the type "+myMapper.previous().iconType);
            }
        };
        
        /*
        var loadLeft = $.getJSON(sourceParamsURL, function(data) {
                console.log("DEBUG201208181448: data");
                console.log(data['outputParams']);
                addMapperFieldsFromJSON(data['outputParams'], "left");
                //fixMapperEndPoints();
            }
        );
        */
        var fixDots = function() { console.log("DEBUG201208212100: In fixDots"); fixMapperEndPoints(); }
        var loadFinished = function() {
            
            jsPlumb.select().detach();
            var mapperEndpointDef = getTestMapper();
            if(myMapper.getData("mappings")) {
                var connections = myMapper.getData("mappings");
                for (var i = 0; i < connections.length; i++) {
                    var connection = {
                        source: connections[i]["source"],
                        target: connections[i]["target"],
                        //anchors:["RightMiddle", "LeftMiddle" ],                        
                    };
                    //connection = $.extend({},mapperEndpointDef, connection);
                    console.log("DEBUG201208171603: connection");
                    console.log(connection);
                    jsPlumb.connect(connection, mapperEndpointDef);
                    console.log(mapperEndpointDef);
                }
                console.log("Finished adding existing mappings");
            } else {
                jsPlumb.select().detach();
            }
            
            jsPlumb.bind("jsPlumbConnection", function(connObj) {
                    console.log("DEBUG:201208171354: In jsPlumbConnection");
                    var myMapper = $('#div_mapper').data('flowItem');
                    console.log(myMapper.getData());
                    var connections = jsPlumb.getAllConnections();
                    console.log(connections);
                    connections = connections["field_mappings"];
                    //connections = connections[0]; // First item should be "green dot" but we might rename that - MRR20120817
                    var myMappings = [];
                    for (var i = 0; i < connections.length; i++) {
                        var sourceid;
                        var targetid;
                        
                        myMappings[i]={};
                        sourceid = connections[i].sourceId;
                        targetid = connections[i].targetId;
                        if(sourceid.match("mapper_target_field")) {
                            //The mapping is reversed 
                            sourceid = connections[i].targetId;
                            targetid = connections[i].sourceId;
                        }
                        myMappings[i]["source"] = sourceid;
                        myMappings[i]["target"] = targetid; 
                    }
                    console.log(myMappings);
                    myMapper.setData({
                       mappings: myMappings, 
                    });
                    console.log(myMapper.getData());
            });
        }       
        
        var loadLeftPromise = loadLeft();
        var loadRightPromise = loadRight();
        
        console.log("loadLeftPromise=");
        console.log(loadLeftPromise);
        $.when(loadLeftPromise, loadRightPromise).then(fixDots).then(loadFinished);
}

var mapperFieldCounter = 0; // Used to autocreate ID

function setJSPlumbDefaults() {
    jsPlumb.Defaults.PaintStyle = {
	lineWidth:13,
	//strokeStyle: 'rgba(200,0,0,0.5)'
        strokeStyle: '#316b31',
    }

    jsPlumb.Defaults.DragOptions = { cursor: "crosshair" };

    //jsPlumb.Defaults.Endpoints = [ [ "Dot", 7 ], [ "Dot", 11 ] ];
    jsPlumb.Defaults.Endpoint = ["Dot", { radius:25 }];

    //jsPlumb.Defaults.EndpointStyles = [{ fillStyle:"#316b31" }, { fillStyle:"#316b31" }];
}
function getTestMapper() {

    var mapperEndpointDef = {       
        /*
            endpoint:       ["Dot", { radius:5 }],
            anchors:        ["RightMiddle", "LeftMiddle" ],
            //paintStyle:     { strokeStyle:"#f0f" },
            EndPointStyle:  { fillStyle:"#316b31", lineWidth:8 },
            scope:          "green dot",
            connectorStyle: { strokeStyle:"#316b31", lineWidth:8 },
            connector:      ["Bezier", { curviness:63 } ],
        */
            //endpoint:["Dot", { radius:15 }],
            scope:          "field_mappings",
            anchors:        ["RightMiddle", "LeftMiddle" ],
            isSource:       true,
            isTarget:       true,
            dropOptions: {
                    tolerance:'touch',
                    hoverClass:'dropHover',
                    activeClass:'dragActive'
             },
    };
    return mapperEndpointDef;
}

function getMapperEndpointDefinition() {
 
        var mapperEndpointDef = {
            scope:          "field_mappings",
            isSource:       true,
            isTarget:       true,
            endpoint:["Dot", { radius:25 }],
            dropOptions: {
                    tolerance:'touch',
                    hoverClass:'dropHover',
                    activeClass:'dragActive'
             },
            /*
            //endpoint:["Dot", { radius:15 }],
            endpoint:       ["Dot", { radius:25 }],
            //anchor:         "RightMiddle",
            anchors:        ["RightMiddle", "LeftMiddle" ],
            //paintStyle:     { strokeStyle:"#316b31" },
            EndPointStyle:  { fillStyle:"#316b31", lineWidth:8 },
            */
            //anchors:        ["RightMiddle", "LeftMiddle"],
            /*
            scope:          "green dot",
            connectorStyle: { strokeStyle:"#316b31", lineWidth:8 },
            connector:      ["Bezier", { curviness:63 } ],
            //maxConnections: 3,
            dropOptions: {
				tolerance:'touch',
				hoverClass:'dropHover',
				activeClass:'dragActive'
			 },
            beforeDetach:function(conn) { 
                    return confirm("Detach connection?"); 
            },
            onMaxConnections:function(info) {
                    alert("Cannot drop connection " + info.connection.id + " : maxConnections has been reached on Endpoint " + info.endpoint.id);
            }
            */
    };
    return mapperEndpointDef;
}

function addMapperFieldsFromJSON(json, side) {
    // For now, we traverse and find all the leaf nodes, ignoring the parents
    console.log("DEBUG201208281040: In addMapperFieldsFromJSON side="+side);
    console.log(json);
    $.each(json, function(key,val) {
        console.log("In each "+key);
        if(val['children']) {
            console.log("We have children");
            // We have children
            myreturn = addMapperFieldsFromJSON(val['children'], side);
        } else {
            // We have no children
            if(side == "left") {
                addLeftMapperField({
                            //id: "left_"+mapperFieldCounter++, //"left_"+val['data'], //mapperFieldCounter++,
                            fieldName: key,
                            fieldType: val['datatype'],
                            description: val['description'],
                            transformation: "No Transformation",
                            sample: val['sample'],
                });
            } else if(side == "right") {
                addRightMapperField({
                            //id: "left_"+mapperFieldCounter++, //"right_"+val['data'], //mapperFieldCounter++,
                            fieldName: key,
                            fieldType: val['datatype'],
                            description: val['description'],
                            transformation: "No Transformation",
                            sample: val["sample"],
                });
            }
        }
    });
}

// This pops all the relative position fields into absolute and the adds the endpoints
function fixMapperEndPoints() {
    var mapperEndpoint = getMapperEndpointDefinition();
    //$.extend(jsPlumb.Defaults, mapperEndpoint);
    // Update our connections after each one is made
    
    // We build an array of the positions, because we lose them one we pop them out into absolute positions
    console.log("DEBUG201208181836: In fixMapperEndPoints");
    
    var left_positions = {};
    $('.mapper_field_left').each(
        function(index) {
            left_positions[index] = $(this).offset();
        }
    );
    // Now we convert them to absolute positions   
    $('.mapper_field_left').each(
        function(index) {
            console.log("**************************");
            if($(this).attr("id")) {
                console.log("DEBUG201208181838: Working on "+$(this).attr("id"));
                $(this).css("position", "absolute"); // Change to absolute so Z-index matches
                //$(this).css("border", "1px solid red");
                $(this).offset(left_positions[index]);
                jsPlumb.addEndpoint($(this).attr("id"), {anchor:"RightMiddle"}, mapperEndpoint);
            }
        }
    );

    // We build an array of the positions, because we lose them one we pop them out into absolute positions
    var right_positions = {};
    $('.mapper_field_right').each(
        function(index) {
            right_positions[index] = $(this).offset();
        }
    );
    // Now we convert them to absolute positions   
    $('.mapper_field_right').each(
        function(index) {
            if($(this).attr("id")) {
                $(this).css("position", "absolute"); // Change to absolute so Z-index matches
                $(this).offset(right_positions[index]);
                jsPlumb.addEndpoint($(this).attr("id"), {anchor:"LeftMiddle"}, mapperEndpoint);
//                jsPlumb.addEndpoint($(this).attr("id"), {anchor:"LeftMiddle"}, mapperEndpoint);
                //jsPlumb.makeTarget($(this).attr("id"), {anchor:"LeftMiddle"}, mapperEndpoint);

            }
        }
    );
    jsPlumb.repaintEverything();    
}

function addLeftMapperField(options) {
    addMapperField($("#mapper_scratch_left"), "left", options);
}

function addRightMapperField(options) {
    addMapperField($("#mapper_scratch_right"), "right", options);
}

function addMapperField(container, fieldClass, options) {
//        console.log(options);
    var default_options = {
        id: 0,
        fieldName: "Field Name",
        fieldType: "Field Type",
        description: "Field Description",
        transformation: "No Transformation",
        sample: "No Sample Data",
    };
    var f = $.extend(default_options, options);
    //var mid = "mapper_field_"+f["id"];
    var mid ="";
    if (fieldClass == "left") {
        mid = "mapper_source_field_"+f["fieldName"];
    } else {
        mid = "mapper_target_field_"+f["fieldName"];
    }
//    console.log(mid);
    mid = mid.replace(/[^A-Za-z0-9_]/g,"_");
//    console.log("endid="+mid);
    var mFieldContainer = $("<DIV class='mapper_field_"+fieldClass+"' id='"+mid+"'></DIV>");
    var mFieldName = $("<DIV class='mapper_field_name'>"+f["fieldName"]+"</DIV>");
    var mFieldType = $("<DIV class='mapper_field_type'>"+f["fieldType"]+"</DIV>");
    var mFieldTran = $("<DIV class='mapper_field_transformation'>"+f["transformation"]+"</DIV>");
    var mFieldSamp = $("<DIV class='mapper_field_sample'>"+f["sample"]+"</DIV>");
    
    mFieldContainer.append(mFieldName).append(mFieldType).append(mFieldTran).append(mFieldSamp);
    
    $(container).append(mFieldContainer);

    
    
}
