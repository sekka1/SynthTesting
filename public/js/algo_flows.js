/*
 * Created by Mark Rorabaugh
 * Created on 2012-06-10
 *
 * Note: This is not currently safe to have multiple flows on a page. Some functions look for objects with specific IDs
 */

/*
(function( $ ) {
	

$.fn.flowGrid = function (options) {
	var tmpFlowGrid = new flowGrid($(this), options);
	$.fn.extend(tmpFlowGrid);
	//$.extend($(this), tmpFlowGrid);
	return tmpFlowGrid;
}


})( jQuery );
*/

function flowGrid (jQueryObj, options) {
	this.jQueryElement = jQueryObj; // We use this to talk back to the element that called us
	this.selectedItem = null; // The item currently selected
	this.gridWidth = 10; // The number of cells wide this flow grid is
	this.gridHeight = 3; // The number of cells tall this flow grid is
        this.myuuidCache = new uuidCache();
        this.flowid = null; // Assume no flowid
        
	this.gridArray = new Array(3);
	
	var defaults = {
		selectedItem: null, 
		gridWidth:	10,
		gridHeight:	3,
		debug: 		false, // disabled by default
		autorefresh: 	1, // Updates are automatically refreshed
		menuIcons:	['flowDBIcon','flowStreamIcon','flowFilterIcon','flowMapperIcon','flowAlgorithmIcon','flowInterfaceIcon','flowMergeIcon'],
                mydata:         {
                    name: "No Name",
                    description: ""
                }

	};

	//var settings = $.extend({},defaults,options);
	//console.log(this);
        var settings = $.extend(true, this,defaults,options);
        $.extend(true, this.mydata, defaults["mydata"], options["mydata"]);

        console.log("DEBUG201208251035: Options=");
        console.log(options);
        console.log(defaults);
	console.log(this);

	var debug = function (text) {
		if(settings["debug"]==1) {
			console.log(text);
		} else if(settings["debug"]==2)	{
			alert(text);
		} else {
			// Debugging disabled
		}
	}

	var error = function (text) {
		console.log(text);
		alert(text);
	}

        // getter and setter for the flowid
        this.getFlowId = function() {
            return this.flowid;
        }
        this.setFlowId = function(flowid) {
            return this.flowid = flowid;
        }
	// Override our append so we call jQuery on the orignal object passed in
	this.append = function (obj) {
		this.jQueryElement.append(obj); // This is jQuery $(<passedinobject>).append()
	}

	this.gridWidth = function (val) {
		if(val) {
			this.gridWidth=val	
		}
		return this.gridWidth;
	}
	this.gridHeight = function (val) {
		if(val) {
			this.gridWidth=val	
		}
		return this.gridWidth;
	}

	this.setup = function() {
		//debug("begin setup");
		//console.log(this);
                this.append(this.getMenuTitle());
                var scratchContainer = this.getScratchContainer();
		scratchContainer.append(this.getMenu());
		scratchContainer.append(this.getScratch());
                this.append(scratchContainer);
                if(this.JSON) {
                    this.addFromJSON(this.JSON);
                } else {
                    this.addItem('flowPlaceHolder');
                }
		//debug("end setup");
	}

        this.getScratchContainer = function() {
            var scratchContainer = $("<DIV id='scratchContainer' class='flowGridScratchContainer'></DIV>");
            return scratchContainer;
        }
        
	this.getScratch = function() {
		var scratch = $("<DIV id='scratch' class='flowGridScratch'></DIV>");
                this.scratch = scratch;
		return this.scratch;
	}

        this.getMenuTitle = function() {
            var editButton = '<div class="button-little-edit"><a href="javascript: void(0);" style="bottom: 4px;">EDIT</a></div>';
            var menuTitle = $("<DIV id='flowMenuTitle' class='flowMenuTitle'><h1><span id='flow_title'>"+this.mydata["name"]+"</span>"+editButton+"</h1></DIV>");
            var myFlowGrid = this;
            menuTitle.click(function() { showFlowProperties(myFlowGrid);});

            return menuTitle;
        }
        
	this.getMenu = function () {
		//debug('addMenu called');
		var itemOptions = {
			myGrid: this
		};

		if(this.gridMenu) {
			error("ERROR201206100832: A menu already exists, so we cannot create another one"+this.menu);
			return this.gridMenu;
		}
				//$(this).append("<DIV class='flowIcon flowFilterIcon'></DIV>");

                

		var menu = $("<DIV id='flowMenuHolder' class='flowMenu'></DIV>");
		//menu.append("<DIV id='flowMenuTitle' class='flowMenuTitle' style='height: 100px;'><h1>Title Goes Here</h1></DIV>");

		for (i in settings['menuIcons']) {
			var iconName = settings['menuIcons'][i];
			var fn = window[iconName];
			if(typeof fn == "function") { 
				var tempFlowIcon = new fn(itemOptions);
				var htmlObj = tempFlowIcon.menuIcon();
				menu.append(htmlObj);
			} else { 
				error("ERROR201206121108: There is no icon "+iconName); 
			}
		}

		this.gridMenu = menu;

		return this.gridMenu;
	}

	this.addItem = function (type) {
		var itemOptions = {
			myGrid: this
		};
		var addObject = null;
		switch (type) {

			case "flowPlaceHolder":
				// Shouldn't really be called except internally
				var placeholder = new flowPlaceholder(itemOptions);
				this.scratch.append(placeholder.toHTML());
				placeholder.select();
				//debug("selectedItem on next line VVVV");
				//console.log(this.selectedItem);
			break;

			default:
				var iconName = type.replace("Menu","");
				var fn = window[iconName];
				if(typeof fn == "function") { 
					var tempFlowIcon = new fn(itemOptions);
					this.scratch.append(tempFlowIcon.toHTML());
					this.selectedItem.insertBefore(tempFlowIcon);

                                        if(jQuery.isFunction(tempFlowIcon.onAdd)) {
                                            // We cann the "onAdd" method for the item just added
                                            tempFlowIcon.onAdd();
                                        }
                                            
				} else { 
					error("ERROR201206121108: There is no icon "+iconName); 
				}

		}
		
                this.addClickEvents();


		//debug('addItem called '+type);
		var gItem= new flowGridItem();
		return this.gridMenu;
	}
        
        this.addClickEvents = function() {
		// Here we add the click event to select which icon his highlighted (and show the edit/delete optiosn)
		$('.flowIcon').click(function(e) {
			$('[class~="flowIcon"]').removeClass('selected');
			$('[class~="flowEditButton"]').addClass('hidden');
			$('[class~="flowEditMenuButtons"]').addClass('hidden');
			$(this).data("flowGridItem").select();
		});            
        }

        this.convertUuidsToObjects = function() {
            $('.flowIcon').each(function(e) {
               var flowIcon = $(this).data("flowGridItem");
               flowIcon.convertUuidsToObjects();
               console.log(flowIcon);               
            });
        }
        
        this.addFromJSON = function(json) {
            // Given json, deserialize and put everything on the scratch
            var myGrid = this;
            var items = jQuery.parseJSON(json);
            var myuuidCache = this.myuuidCache;
            console.log(items);
            jQuery.each(items, function (index) {
                var itemOptions = {
                    myGrid:     myGrid,
                    myGridPosX: items[index]["gridX"],
                    myGridPosY: items[index]["gridY"]
                };
                $.extend(itemOptions, items[index]);
                var objName = "flow"+items[index]["type"]+"Icon";
                if(items[index]["type"] == "Placeholder") { objName="flowPlaceholder"; }
                var fn = window[objName];
                if(typeof fn == "function") { 
                    var tempFlowIcon = new fn(itemOptions);
                    console.log(myuuidCache);
                    myGrid.scratch.append(tempFlowIcon.toHTML());
                    tempFlowIcon.refresh();
                    myuuidCache.add(tempFlowIcon, tempFlowIcon.getUUID());
                    tempFlowIcon.setData(items[index]);
                } else {
                    alert("ERROR201208171051: "+fn+" is not a method I know how to call");
                }
            });
            this.addClickEvents();
            console.log(this.myuuidCache);
            this.convertUuidsToObjects();
        }

        this.getHiddenWorkflow = function() {
            var hidden_workflow = this.mydata["hidden_workflow"];
            if(typeof hidden_workflow == "string") {
                hidden_workflow = jQuery.parseJSON(hidden_workflow);
            }
            return JSON.stringify(hidden_workflow, null, '\t');        
        }

        this.setHiddenWorkflow = function(hidden_workflow) {
            if(typeof hidden_workflow == "string") {
                hidden_workflow = jQuery.parseJSON(hidden_workflow);
            }
            console.log("DEBUG201211292150: Setting hidden_workflow=",hidden_workflow); 
            this.mydata["hidden_workflow"]=hidden_workflow;  
        }
        
        this.toJSON = function () {
            var items = [];
            var json = "";
            $('.flowIcon').each(
                function(index, e) { 
                    console.log("index="+index);
                    var flowItem = $(e).data("flowGridItem");
                    items.push(flowItem.serialize());
                }
            ); 
            json = JSON.stringify(items);
            return json;
        }
        
        this.save = function() {
            var json = this.toJSON();
            this.saveCallback(this, json);
        }
	this.test = function () {
		alert('test');
	}
}

function flowDBIcon(options) {
	$.extend(this, new flowGridItem(options));
	this.iconType = "DB";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";
	this.iconTitle = "Data Source";

	this.edit = function() {
            $('.selectedRow').removeClass("selectedRow");
            var datasource_id = this.getData('datasource_id');
            if(datasource_id) {
                $('#row'+datasource_id).addClass("selectedRow");;
            }
            this.onAdd();
	}
        
        this.onAdd = function() {
            $('#div_datasources_list').data({flowItem: this});
            showWizardPopup($('#div_datasources_list'));
        }	
        
}

function flowStreamIcon(options) {
	$.extend(this, new flowGridItem(options));
	this.iconType = "Stream";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";
	this.iconTitle = "Data Stream";

	this.edit = function() {
		
	}	

        this.onAdd = function() {
            $('#div_datastreams_list').data({flowItem: this});
            showWizardPopup($('#div_datastreams_list'));
        }	
}

function flowFilterIcon(options) {
	$.extend(this, new flowGridItem(options));
	this.iconType = "Filter";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";
	this.iconTitle = "Filter";

        this.onAdd = function() {
            $('#div_filter').data({flowItem: this});
            showWizardPopup($('#div_filter'));
        }	
}

function flowMapperIcon(options) {
	$.extend(this, new flowGridItem(options));
	this.iconType = "Mapper";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";
	this.iconTitle = "Mapper";

	this.edit = function() {
		this.onAdd();
	}
        
        this.onAdd = function() {
            $('#div_mapper').data({flowItem: this});
            showWizardPopup($('#div_mapper'), function() {
               start_mapper();
            });
        }	
}

function flowAlgorithmIcon(options) {
	$.extend(this, new flowGridItem(options));
	this.iconType = "Algorithm";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";
	this.iconTitle = "Algorithm";
        
	this.edit = function() {
            $('.selectedRow').removeClass("selectedRow");
            var algorithm_id = this.getData('algorithm_id');
            if(algorithm_id) {
                $('#row'+algorithm_id).addClass("selectedRow");
            }            
            this.onAdd();
	}
        
        this.onAdd = function() {
            $('#div_algorithms_list').data({flowItem: this});
            //$('#div_algorithms_list').fadeIn('slow');
            showWizardPopup($('#div_algorithms_list'), function() {
               //start_mapper();
            });
        }	
}

function flowInterfaceIcon(options) {
	$.extend(this, new flowGridItem(options));
	this.iconType = "Interface";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";
	this.iconTitle = "Visualization";
	
        this.edit = function() {
            $('.selectedRow').removeClass("selectedRow");
            var vis_id = this.getData('visualization_id');
            if(vis_id) {
                console.log("DEBUG201211180945: visualization_id="+vis_id);
                $('#row'+vis_id).addClass("selectedRow");
            }            
            this.onAdd();            
        }
        
        this.onAdd = function() {
            $('#div_visualizations_list').data({flowItem: this});
            $('#div_visualizations_list').fadeIn('slow');
        }
}

function flowMergeIcon(options) {
	$.extend(this, new flowGridItem(options));
	this.iconType = "Merge";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";
	this.iconTitle = "Merge";
	
}

function flowPlaceholder(options) {
	$.extend(this, new flowGridItem(options));
	//this.iconClass = "flowPlaceholder";
	//this.iconType = "flowPlaceholder";
 	this.iconType = "Placeholder";
	this.iconClass = "flow"+this.iconType+"Icon";
	this.iconMenuID = "flowMenu"+this.iconType+"Icon";       
	this.iconTitle = "Placeholder";

	this.toHTML = function () {
		var newDiv = $("<DIV class='flowIcon flowPlaceholder'><DIV class='flowIconTitle'>End</DIV></DIV>");
		newDiv.data("flowGridItem", this);

		this.myDiv = newDiv;
		return newDiv;
	}
        /*
         * Do we want to store the placeholder or not?
        this.serialize = function () {
            return "";
        }
        this.toJSON = function () {
            return "";
        }
        */
}



function flowGridItem(options) {
	this.selected = false; // Am I selected?
	this.myGridWidth = 1; // The number of cells wide this item takes
	this.myGridHeight = 1; // The number of cells tall this item takes
	this.myGridPosX = 0;
	this.myGridPosY = 0;
        //this.myuuid = generateUUID();
	this.myDiv = null; // This holds the div for the icon we are in
        this.myuuidCache = new uuidCache();
        this.myData = {}; // Stores our item specific data (like datasource/algorithm_id/etc)

	//this.iconType = "blah";
	
	var defaults = {
		selected: 	false, 
		myGridWidth:	1,
		myGridHeight:	1,
		debug: 		true, // disabled by default
		autoRefresh: 	1, // Updates are automatically refreshed
		myGrid:		null //Pointer to our parent grid
	};

	var settings = $.extend(this,defaults,options);
	$.extend(this,defaults,options);

        if(this.uuid) { this.myuuid=this.uuid; }

	var debug = function (text) {
		if(settings['debug']==true) {
			console.log(text);
		} else if(settings['debug']==2)	{
			alert(text);
		} else {
			//console.log(this);
			// Debugging disabled
		}
	}

	var error = function (text) {
		console.log(text);
		alert(text);
	}

        // Get item specific data (datasource_id, etc)
        this.getData = function(field) {
            if(typeof(field)=="string") {
                return this.myData[field];
            } else {
                return this.myData;
            }
        }
        // Set item specific data (datasource_id, etc)
        this.setData = function(object) {
            $.extend(this.myData, object);
        }
        
	this.myGridWidth = function (val) {
		if(val) {
			this.gridWidth=val	
		}
		return this.gridWidth;
	}
	this.myGridHeight = function (val) {
		if(val) {
			this.gridWidth=val	
		}
		return this.gridWidth;
	}

	this.toHTML = function () {
		var deleteButton = $("<DIV class='flowEditButton delete hidden'>&nbsp;</DIV>").data("flowGridItem", this);
		deleteButton.click(function(){ var flowItem = $(this).data("flowGridItem"); flowItem.remove();});

		var insertButton = $("<DIV class='flowEditButton insert'>Insert</DIV>").data("flowGridItem", this);
		insertButton.click(function(){ alert("insert "+$(this).data("flowGridItem").iconTitle); });
		insertButton = "";

		var editButton = $("<DIV class='flowEditButton edit hidden'>Edit</DIV>").data("flowGridItem", this);
                var me = this;
		editButton.click(function(){
                    me.edit();
                    //alert("edit "+$(this).data("flowGridItem").iconTitle); 
                });

		var editMenu = $("<DIV class='flowEditMenuButtons hidden'></DIV>").append(insertButton, editButton);

		var newdiv = $("<DIV class='flowIcon "+this.iconClass+"'><DIV class='flowIconTitle'>"+this.iconTitle+"</DIV><DIV class='flowArrowRight'></DIV></DIV>");
		newdiv.append(editMenu);
		newdiv.append(deleteButton);
		newdiv.data("flowGridItem", this);

		this.myDiv = newdiv;
		return newdiv;
	}

	this.menuIcon = function () {
		var menuIconShell = $("<DIV id='"+this.iconMenuID+"' class='flowMenuIconShell'></DIV>");
		var menuIcon = $("<DIV id='"+this.iconMenuID+"' class='flowMenuIcon "+this.iconClass+"'></DIV>");
		var iconTitle = $("<DIV class='flowMenuIconTitle'>"+this.iconTitle+"</DIV>");
		menuIconShell.append(menuIcon);
		menuIconShell.append(iconTitle);
		//console.log(menuIconShell);

		var flowGrid = this.myGrid;
		menuIconShell.click(function(e){
			flowGrid.addItem($(this).attr('id'));
		});
	
		return menuIconShell;
	}

	this.isSelected = function () {
		if($(this.myDiv).is('.selected')) {
			return true;
		}
		return false;
	}

	this.select = function () {
			$(this.myDiv).addClass('selected');
			$(this.myDiv).children('[class~="flowEditButton"]').removeClass('hidden');
			$(this.myDiv).children('[class~="flowEditMenuButtons"]').removeClass('hidden');
			$(this.myDiv).children('[class~="flowEditMenuButtons"]').children().removeClass('hidden');
			//console.log(this.myGrid);
			this.myGrid.selectedItem = this;
	}

	this.next = function (obj) {
		if(obj) {
			this.nextObj = obj;
		}
                debug("DEBUG201208151840: In next()");
		return this.nextObj;
	}

	this.previous = function (obj) {
		if(obj) {
			this.prevObj = obj;
		}
                debug("DEBUG201208151840: In previous()");
		return this.prevObj;
	}
        this.clearPrevious = function() {
            // Erase our previous
            this.prevObj = null;
        }

	this.shiftRight = function() {
		if(this.next()) {
			// Recursive-like Call our nextObj neighbor and tell them to shiftRight also
			this.next().shiftRight();
		}
		this.myGridPosX++;
		debug("myGridPosX++="+this.myGridPosX);		
		if(this.autoRefresh) { this.refresh(); }
	}

	this.shiftLeft = function() {
		if(this.myGridPosX<0) {
			error("ERROR201206111658: "+this.me+" cannot shift left any more");
			this.myGridPosX=0;
			return this.myGridPosX;
		}
		if(this.next()) {
			// Recursive-like iteration overour nextObj neighbors and tell them to shiftLeft also
			this.next().shiftLeft();
		}
		this.myGridPosX--;		
		if(this.autoRefresh) { this.refresh(); }
		return this.myGridPosX;		
	}

	this.gridPos = function (posX, posY) {
		if((posX>=0) && (posY>=0)) {
			this.myGridPosX = posX;
			this.myGridPosY = posY;
		}

		if(this.autoRefresh) { this.refresh(); }

		return (this.myGridPosX, this.myGridPosY);	
	}

        this.isFirst = function () {
            // Checks to see if we are the first left-most item (position 0,0)
            // Primarly used to start a serialization
            if ((this.myGridPosX == 0) && (this.myGridPosY == 0)) {
                return true;
            }
            return false;
        }
        
	this.insertBefore = function (newObj) {
		debug("called insertBefore");
		newObj.gridPos(this.myGridPosX, this.myGridPosY);
		debug(newObj);
		this.shiftRight();
		if(this.previous()) { 
			this.previous().next(newObj); // Set our previous object to point to the newObj 
		}
		newObj.previous(this.previous()); // Set the newObj to have our old previous pointer
		newObj.next(this); // Set the newObj to have us as the next pointer
		this.previous(newObj); // Set our previous to be the newObj
		newObj.refresh();
	}
	
	this.remove = function () {
		// Remove ourselves
		if(this.next()) {
			var nextObj = this.next();
                        if(this.previous()) {
                            nextObj.previous(this.previous()); // Tie our next neighbor to our previous
                        } else {
                            nextObj.clearPrevious(); // We didn't have a previous, so neither should our neighbor
                        }
			nextObj.select(); // We select our next kin
			nextObj.shiftLeft();
		}
		if(this.previous()) {
			var prevObj = this.previous();
			prevObj.next(this.next()); // Tie our previous neighbor to our next
			if(!this.next()) { prevObj.select(); } // We select our previous kin
		} 

		$(this.myDiv).remove();
		$(this).remove(); // Suicide 
	}

	this.refresh = function () {
		$(this.myDiv).css("left",(this.myGridPosX*$(this.myDiv).outerWidth()));
		$(this.myDiv).css("top",(this.myGridPosY*$(this.myDiv).outerHeight()));
		debug(this.iconTitle+" gridx="+this.myGridPosX+" x="+$(this.myDiv).css("left"));
	}
        
        this._serialize = function(extraData) {
            extraData = typeof extraData !== 'undefined' ? extraData : {};
            myUUID=this.getUUID(); // Make sure we have a UUID
            nextUUID="";
            prevUUID="";
            if(this.next()) { nextUUID=this.next().getUUID(); }
            if(this.previous()) { prevUUID=this.previous().getUUID(); }
            serial = {
                type:   this.iconType,
                gridX:  this.myGridPosX,
                gridY:  this.myGridPosY,
                uuid:   this.myuuid,
                nextUUID:   nextUUID,
                previousUUID: prevUUID
            };
            $.extend(extraData, serial);
            return extraData;
        }
        
        this.serialize = function(extraData) {
            extraData = typeof extraData !== 'undefined' ? extraData : {};
            var serial = this.getData();
            $.extend(extraData, serial);
            return this._serialize(extraData);            
        }
        
        this.toJSON = function() {
            return (JSON.stringify(this.serialize()));
        }
        
        this.getUUID = function() { 
            if(this.myuuid) {
                return this.myuuid;
            } else {
                debug("DEBUG201208151821: "+this.iconTitle+" does not have a UUID");
                this.myuuid = this.myuuidCache.add(this);
            }
            return this.myuuid; 
        }
        
        this.convertUuidsToObjects = function() {
            var c = this.myuuidCache;
            var obj;
            if(this.nextUUID) {
                obj = c.get(this.nextUUID);
                if(obj) {
                    this.next(obj);
                } else {
                    alert("ERROR201208151427: The UUID "+this.nextUUID+" could not be found.");
                }
            } else {
                console.log("DEBUG201208151743: "+this.iconTitle+" has no nextUUID");
            }
            if(this.previousUUID) {
                obj = c.get(this.previousUUID);
                if(obj) {
                    this.previous(obj);
                } else {
                    alert("ERROR201208151428: The UUID "+this.previousUUID+" could not be found.");
                }
            } else {
                console.log("DEBUG201208151744: "+this.iconTitle+" has no previousUUID");
            }
        }
}

function generateUUID() {
    // RFC41212v4 Borrowed from http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {var r = Math.random()*16|0,v=c=='x'?r:r&0x3|0x8;return v.toString(16);});
}

function uuidCache() {
    if ( arguments.callee._singletonInstance )
        return arguments.callee._singletonInstance;
    arguments.callee._singletonInstance = this;
    
    this._cache = [];
    
    this.add = function(object, uuid) {
            if(uuid) {
                console.log("DEBUG201208151749: Adding UUID "+uuid+" with object "+object.iconTitle);
                console.log(object);
                this._cache[uuid] = object;
                return uuid;
            } else {
            var tuuid = generateUUID();
            this._cache[tuuid] = object;
            return tuuid; 
            }
    }
    
    this.get = function(uuid) {
        if(this._cache[uuid]) {
            return this._cache[uuid];
        }
        console.log("ERROR201208151748: Could not find an object with UUID "+uuid);
        return null;
    }
}

/************************** UI Helpers **********************************/

function showFlowProperties(myFlowGrid) {
    console.log(myFlowGrid.mydata);
     $('#form_flow_name').val(myFlowGrid.mydata["name"]);
     $('#form_flow_description').val(myFlowGrid.mydata["description"]);
     $('#form_flow_details').val(myFlowGrid.mydata["details"]);     
     $('#form_flow_hidden_workflow').val(myFlowGrid.getHiddenWorkflow());
     showWizardPopup($('#div_flowProperties'));
}

function validateFlowProperties() {
    $('#button_properties_close').addClass('disabled');
    if($('#form_flow_name').val().length > 10) {
        // Should really do ajax to validate name hasn't been taken? - MRR20120820
        if($('#form_flow_description').val().length > 20) {
            $('#button_properties_close').removeClass('disabled');
        }
    } 
}

function closeFlowProperties(myFlowGrid) {
    myFlowGrid.mydata["name"] = $('#form_flow_name').val();
    myFlowGrid.mydata["description"] = $('#form_flow_description').val();
    myFlowGrid.mydata["details"] = $('#form_flow_details').val();
    //myFlowGrid.mydata["hidden_workflow"] = $('#form_flow_hidden_workflow').val();
    myFlowGrid.setHiddenWorkflow($('#form_flow_hidden_workflow').val());
    $('#flow_title').html(myFlowGrid.mydata["name"]);
    restoreFlowGridDiv($('#div_flowProperties'));
}
