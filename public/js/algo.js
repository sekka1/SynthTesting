/*
 * Created by Mark Rorabaugh
 * Created on 2012-05-23
 *
 */

function blackOut() {
    var mydiv = $('<DIV id="blackout"></DIV>');
    if($('.body-outer').length) {
        $('.body-outer').prepend(mydiv);
    } else {
        $('body').prepend(mydiv);
    }
    $(mydiv).hide();
    $(mydiv).fadeIn(2000);
    //var mydiv2 = $('<DIV id="diedie"></DIV>');
    //alert('here');
}

function removeBlackOut() {
    var removeme = function() {
        $('#blackout').remove();
    }
    $.when($('#blackout').fadeOut(2000)).then(removeme);
}
// Center an object on the x/y axis
// TODO: Consider just calling centerx and centery - MRR20120523
jQuery.fn.center = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    var owidth = this.width();
    var oheight = this.height();
    this.css({
        "position": "absolute",
        "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
        "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
    // Next, since we might have changed the position to absolute, we reset the width and height to the original vals
    this.width(owidth);
    this.height(oheight);
    return this;
}

// Center an object on the x axis
jQuery.fn.centerx = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    var owidth = this.width();
    var oheight = this.height();
    this.css({
        "position": "absolute",
        "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
    // Next, since we might have changed the position to absolute, we reset the width and height to the original vals
    this.width(owidth);
    this.height(oheight);
    return this;
}

// Center an object on the y axis
jQuery.fn.centery = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    var owidth = this.width();
    var oheight = this.height();
    this.css({
        "position": "absolute",
        "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
    });
    // Next, since we might have changed the position to absolute, we reset the width and height to the original vals
    this.width(owidth);
    this.height(oheight);
return this;
}

jQuery.fn.copyDimensions = function(obj) {
    this.width(obj.width());
    this.height(obj.height());
}

jQuery.fn.copyPosition = function (obj) {
    this.offset(obj.offset());
}

function setup_radio_buttons() {
	$(".switch label:not(.checked)").click(function(){
	    var label = $(this);
	    var input = $('#' + label.attr('for'));
	    
	    if (!input.prop('checked')){
		label.closest('.switch').find("label").removeClass('checked');                        
		label.addClass('checked'); 
		input.prop('checked', true);
	    }
	});

	$(".switch input[checked=checked]").each(function(){
	    $("label[for=" + $(this).attr('id') + "]").addClass('checked');
	});
}

function algorithms_setup() {

}
$().ready(setup_radio_buttons);
/*
 *  Created by Mark Rorabaugh on 20120523
 *  Usage: var wizard = new wizard("#startslide", {options});
 *  Where #startslide is the id of a div for the first slide. Currently all slides must have class="wizardBox".
 *
 *  Sample slide div: 
 *  <div id="#div_wizardBox_Slide2" class="wizardSlide" data-wizard-nextSlide="#div_wizardBox_Slide3" data-wizard-previousSlide="#div_wizardBox_slide1"></div>
 */  
function wizard(startSlide, options) {
	this.startSlide = startSlide;
	this.slideCurrent = startSlide;
	this.wizardtop = "100px";

	var defaults = {
		placeholder: "#div_wizardBox_placeholder",
		slideEdgeSize: 40,
		debug: 0, // disabled by default
		autorefresh: 1, // Refresh the screen if the slides are changed (default), otherwise you must call slide_rightEdge()/slide_leftEdge() and hide_slides() on yhour own
		skipsetup: false, // Setting this true will not automatically run setup when a wizard object is created
		wizardtop: "100px",
	};

	var settings = $.extend({},defaults,options);
	var slideEdgeSize = settings["slideEdgeSize"]; // Number of pixels to show at the left and right edge of the screen
	var placeholder = settings["placeholder"]; // A placeholder object to get demensions from 
						   // TODO: This should optionally be the first slide where we just duplicate a div to reserve the space MRR20120526

	var debug = function (text) {
		if(settings["debug"]==1) {
			console.log(text);
		} else if(settings["debug"]==2)	{
			alert(text);
		} else {
			// Debugging disabled
		}
	}

	this.nextSlide = function (slide, options) {
		if (slide) {
			// Set the current slides attribute data-wizard-slideNExt to slide
			// This determines what the next slide will be 
			$(this.slideCurrent).attr('data-wizard-nextSlide', slide);
			if (settings["autorefresh"]) {
				this.slide_rightEdge(slide);
				this.hide_slides();
			}
		}
		return $(this.slideCurrent).attr('data-wizard-nextSlide');
	}

	this.previousSlide = function (slide, options) {
		if (slide) {
			// Set the previous slides attribute data-wizard-previousSlide to slide
			// This determines what the previous slide will be 
			$(this.slideCurrent).attr('data-wizard-previousSlide', slide);
			if (settings["autorefresh"]) {
				this.slide_leftEdge(slide);
				this.hide_slides();
			}
		}
		return $(this.slideCurrent).attr('data-wizard-previousSlide');
	}


	this.hide_slides = function (all) {
		var slideCurrent = this.slideCurrent;
		// Hide all the divs with a class of "wizardBox"
		$('div[class~="wizardBox"]').hide(); //matches on the word wizardBox
		if(!all) {
			$(slideCurrent).show();
			$(this.previousSlide()).show();
			$(this.nextSlide()).show();
		}
		//debug("DEBUG201205261625: "+this.nextSlide());
	}

	this.fadeAll = function () {
		var slideCurrent = this.slideCurrent;
		$('div[class~="wizardBox"]').fadeOut('slow'); //matches on the word wizardBox
		this.slideCurrent = this.startSlide;
		$(this.slideCurrent).centerx();
		$(this.slideCurrent).css('opacity', '1');
	}


	this.slide_setup = function () {
		//var slideCurrent = this.slideCurrent;
		var slideCurrent = this.startSlide;
		$(slideCurrent).copyDimensions($('#div_wizardBox_placeholder'));
		$(slideCurrent).copyPosition($('#div_wizardBox_placeholder'));
		this.placeholderoffset = $('#div_wizardBox_placeholder').offset();
		this.wizardtop = this.placeholderoffset.top;
		$(slideCurrent).centerx();
		this.slide_rightEdge(this.nextSlide());
		this.hide_slides(true); // Hide all of them
		//$(slideCurrent).css("opacity","0.1");
		//$(slideCurrent).fadeIn('slow');
	}

	this.start = function () {
                $(this.startSlide).css('opacity', '1');
		$(this.startSlide).fadeIn('slow');
		this.slide_rightEdge(this.nextSlide());
	}
	this.slide_rightEdge = function (slide) {
		var slideCurrent = this.slideCurrent;
		var docwidth = $(window).width();
		var slideCurrentPos = $(slideCurrent).offset();
		var slideWidth = $(slideCurrent).outerWidth();

		$(slide).css('visibility', 'visible');
		$(slide).show();
		$(slide).attr('position','absolute');
		$(slide).copyDimensions($(slideCurrent));
		//$(slide).css('top',slideCurrentPos.top);
		$(slide).css('top',this.wizardtop);
		$(slide).css('left',docwidth+slideWidth); //Start just off the screen and slide into position
		//$(slide).width(slideWidth);
		$(slide).css('opacity', '0.25');
		$(slide).animate({
			left: docwidth-slideEdgeSize,
			opacity: 0.25,	
		},1000);
		console.log("slidetop="+$(slide).css('top') + " currentslide="+slideCurrentPos.top);
	}

	this.slide_leftEdge = function (slide) {
		var slideCurrent = this.slideCurrent;
		var docwidth = $(window).width();
		var slideCurrentPos = $(slideCurrent).offset();
		var slideWidth = $(slideCurrent).outerWidth();
		var slideDistance = ($(slideCurrent).outerWidth() - slideEdgeSize)+$(slideCurrent).offset().left;

		$(slide).css('visibility', 'visible');
		$(slide).show();
		$(slide).css('top',slideCurrentPos.top);
		$(slide).css('left',-(slideWidth+slideDistance)); //Start just off the screen and slide into position
		//$(slide).width(slideWidth);
		$(slide).copyDimensions($(slideCurrent));
		$(slide).attr('position','absolute');
		$(slide).css('opacity', '0.25');
		$(slide).animate({
			left: -(slideWidth-slideEdgeSize),
			opacity: 0.25,	
		},1000);
		debug("DEBUG201205261250: left="+(-(slideWidth-slideEdgeSize)));
	}

	this.slide_previous = function () {
		var slideCurrent = this.slideCurrent;
		
		var docwidth = $(window).width();
		var slideDistance = ($(slideCurrent).outerWidth() - slideEdgeSize)+$(slideCurrent).offset().left;

		if (this.nextSlide()) {
			// Slide the right-most slide off the screen
			$(this.nextSlide()).animate({
				left: docwidth+slideDistance,
				opacity: 0.25,
			},1000);
		}

		if (slideCurrent) {
			// Move the current slide from the middle to the right edge
			$(slideCurrent).animate({
				left: docwidth-slideEdgeSize,
				opacity: 0.25,	
			},1000);
		}

		var newCurrent = this.previousSlide();
		debug("DEBUG201205261312: previous slide="+newCurrent);
		if (newCurrent) {
			// Move left slide into current (middle) position
			$(newCurrent).animate({
				left: ((($(window).width() - $(newCurrent).outerWidth()) / 2) + $(window).scrollLeft()),
				opacity: 1,
			},1000);
		}

		var newPrevious = $(newCurrent).attr('data-wizard-previousSlide');
		if (newPrevious) {
			// Move the other previous slide onto the left edge
			this.slide_leftEdge(newPrevious);
		}

		this.slideCurrent = newCurrent;
		this.hide_slides();
	}
	this.previous = this.slide_previous;

	this.slide_next = function () {
		var slideCurrent = this.slideCurrent;

		this.hide_slides(); // FIXME: Why is this first here but last in slide_previous?
		//var docwidth = $(window).width();
		var slideDistance = ($(slideCurrent).outerWidth() - slideEdgeSize)+$(slideCurrent).offset().left;
		debug("DEBUG201205261244: slideDistance="+slideDistance);

		var oldPrevious = this.previousSlide();
		if (oldPrevious) {
			debug("DEBUG201205261300: newleft="+(($(oldPrevious).offset().left)-slideDistance));
			// Move the left slide off the screen
			$(oldPrevious).animate({
				left: (($(oldPrevious).offset().left)-slideDistance),
				//left: -($(oldPrevious).outerWidth()),
				//left: ($(oldPrevious).offset().left-slideEdgeSize),
				opacity: .25,
			},1000);
		}

		if (slideCurrent) {
			// slide the current slide to the left edge
			$(slideCurrent).animate({
				left: -($(slideCurrent).outerWidth()-slideEdgeSize),
				opacity: .25,
			},1000);
		}	

		var newCurrent = this.nextSlide();
		if (newCurrent) {
			// slide the new current slide into the center
			$(newCurrent).animate({
				left: ((($(window).width() - $(newCurrent).outerWidth()) / 2) + $(window).scrollLeft()),
				opacity: 1,
			},1000);
		}

		var newNext = $(newCurrent).attr('data-wizard-nextSlide');
		if (newNext) {
			this.slide_rightEdge(newNext);
		}

		this.slideCurrent = newCurrent;
		//this.hide_slides();
	}
	this.next = this.slide_next;
		

	if(settings["skipsetup"]==false) {
		this.slide_setup();
	}
}

/*
 * Functions to convert JSON input/output params into a format JSTree can handle
 */
    function paramsToJSTree(params) {
        //Converts a params object (input/output) into the JSTree format
        if(typeof params == "string") {
            // If params is a string, convert it to an object
            params = jQuery.parseJSON(params);
        }
        var myparams = jQuery.extend(true, {}, params); // Copy the params
        return _paramsToJSTree(myparams);        
    }

    function _paramsToJSTree(params) {
        var res = [];
        $.each(params, function(k,v) {
           if(k == "children") {
               // Should we also check for "fields"?? - TODO: FIXME: MRR-20121024
               var children = paramsToJSTree(params[k]["children"]);
               res.push({
                    "data":         k+" ["+params[k]["datatype"]+"]"
               ,    "metadata":     params[k]
               ,    "children":     children
               })
           } else {
               res.push({
                   "data":      k+" ["+params[k]["datatype"]+"]"
               ,   "metadata":  params[k]
               });
           }
        });
        return res;
    }


