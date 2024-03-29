/*
 * jQuery File Upload Plugin JS Example 6.7
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload();

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

/*        
    if (window.location.hostname === 'blueimp.github.com') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            process: [
                {
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                },
                {
                    action: 'save'
                }
            ]
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<span class="alert alert-error"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else if (window.location.hostname === 'localhost') {
*/        
        // Demo settings:
        $('#fileupload').fileupload('option', {
	    //dropZone: $('#div_datasource_web_upload'),
	    dropZone: $(document),
            maxChunkSize: 2048000,
	    headers: {"authKey": "541b393f52b097d3e589ea63ccdfd49e"},
	    beforeSend: function(xhr) {
			xhr.setRequestHeader("authKey", "541b393f52b097d3e589ea63ccdfd49e");
		},
            always: function (e, data) { console.log("DEBUG201208241644: Refreshing DataSource flexigrid"); $('#datasource_list').trigger('reloadGrid');} //$('#datasource_list').flexReload(); }, // Added to refresh the import_list showing the recently uploaded files - MRR20120529
 
            //url: '//jquery-file-upload.appspot.com/',
            //maxFileSize: 5000000,
            //acceptFileTypes: /(\.|\/)(csv|xml|avi|gif|jpe?g|png)$/i,
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                //url: '//jquery-file-upload.appspot.com/',
                //url: 'http://demo.v1.api.algorithms.io/jssdk/?authToken=541b393f52b097d3e589ea63ccdfd49e',
                //url: 'https://96.255.34.90:8443/',
                url: '/',
                type: 'HEAD'
            }).fail(function () {
                $('<span class="alert alert-error"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
/*
    }  else {
        // Load existing files:
        $('#fileupload').each(function () {
            var that = this;
            $.getJSON(this.action, function (result) {
                if (result && result.length) {
                    $(that).fileupload('option', 'done')
                        .call(that, null, {result: result});
                }
            });
        });
    } 
*/
});
