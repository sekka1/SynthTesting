$(document).ready(function () {
    $('#dashboard').sortable({
        tolerance: 'pointer',
        receive: function (event, ui) {
            $(this).removeClass('hover');
            var droppedGroup = $(this).find('li.draggable.group');
            $(droppedGroup).removeClass('draggable').addClass('groupContainer');
            $(droppedGroup).resizable({
                grid: [110, 110],
                start: function (event, ui) {
                    $(droppedGroup).css({
                        position: "",
                        top: "",
                        left: ""
                    });
                },
                resize: function (event, ui) {
                    $(droppedGroup).css({
                        position: "",
                        top: "",
                        left: ""
                    });
                },
                stop: function (event, ui) {
                    $(droppedGroup).css({
                        position: "",
                        top: "",
                        left: ""
                    });
                }
            });
            $(droppedGroup).bind("mouseover", function () { $(this).find('.removeGroup').show(); });
            $(droppedGroup).bind("mouseout", function () { $(this).find('.removeGroup').hide(); });
            $(droppedGroup).find('.removeGroup').bind("click", function () { $(this).parent().remove(); });
            var widgetList = $(droppedGroup).find('ul.widgets');
            $(widgetList).sortable({
                tolerance: 'pointer',
                connectWith: '.widgets',
                receive: function (event, ui) {
                    console.log("IN RECEIVE");
                    console.log(ui);
                    console.log(ui.item);
                    console.log(ui.item.attr("id"));
                    var oldID = ui.item.attr("id");
                    $(this).removeClass('hover');
                    var droppedWidget = $(this).find('li.draggable.widget');
                    //$(droppedWidget).removeClass('draggable');
                    $(droppedWidget).removeClass('draggable').addClass('widgetFull'); // Resize the widget
                    $(droppedWidget).attr("id","dashboardItem_"+oldID);
                    $(droppedWidget).resizable({
                        grid: [110, 110],
                        minWidth: 100,
                        minHeight: 100,
                        start: function (event, ui) {
                            $(droppedWidget).css({
                                position: "",
                                top: "",
                                left: ""
                            });
                        },
                        resize: function (event, ui) {
                            $(droppedWidget).css({
                                position: "",
                                top: "",
                                left: ""
                            });
                        },
                        stop: function (event, ui) {
                            $(droppedWidget).css({
                                position: "",
                                top: "",
                                left: ""
                            });
                        }
                    });
                    $(droppedWidget).bind("mouseover", function () { $(this).find('.removeWidget').show(); });
                    $(droppedWidget).bind("mouseout", function () { $(this).find('.removeWidget').hide(); });
                    $(droppedWidget).find('.removeWidget').bind("click", function () { $(this).parent().remove(); });
                },
                over: function (event, ui) {
                    $(this).addClass('hover');
                },
                out: function (event, ui) {
                    $(this).removeClass('hover');
                },
                update: function (event, ui) {
                    var myOrder = $(this).sortable('toArray').toString();
                    console.log(myOrder);
                },
                stop: function (event, ui) {
                    console.log("this");
                    console.log(this);
                    console.log("UI="+ui);
                    console.log(ui);
                    //var myOrder = $(this).sortable('toArray').toString();
                    var myOrder = $(this).sortable('toArray');
                    console.log(myOrder);
                    console.log("HERE"+$.toJSON($('#dashboard')));
                    console.log($.param($(this)));
                    test();
                },
            });
        },
        over: function (event, ui) { $(this).addClass('hover'); },
        out: function (event, ui) { $(this).removeClass('hover'); }
    }).disableSelection();
    $('.draggable.group').draggable({
        connectToSortable: '#dashboard',
        helper: 'clone',
        revert: 'invalid'
    });
    $('.draggable.widget').draggable({
        start: function(even, ui) {
            console.log("drag start");
            console.log(this);
            console.log($(this).attr("id"));
            console.log(ui);
            $(ui).attr("id","MEME");
        },
        connectToSortable: '.widgets',
        helper: 'clone',
        revert: 'invalid',
        zIndex: 1
    });
});

function test() {
    console.log(Object.toJSON($('#dashboard')));
  a = elementToObject('#dashboard');
  console.log(Object.toJSON(a));
  
    var myObject = {
  a: {
    one: 1, 
    two: 2, 
    three: 3
  }, 
  b: [1,2,3]
};
var recursiveEncoded = $('#dashboard').serialize();
alert(recursiveEncoded);
}

function elementToObject(element, o) {
    var el = $(element);
    var o = {
       tagName: el.tagName
    };
    var i = 0;
    if (el.atributes == undefined) {
        console.log(el);
    }
    for (i ; i < el.attributes.length; i++) {
        o[el.attributes[i].name] = el.attributes[i].value;
    }

    var children = el.childElements();
    if (children.length) {
      o.children = [];
      i = 0;
      for (i ; i < children.length; i++) {
        child = $(children[i]);
        o.children[i] = elementToObject(child, o.children) ;
      }
    }
    return o;
  }
/*
  exemple:
  a = elementToObject(document.body);
  Object.toJSON(a);
*/