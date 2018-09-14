/* global media_script */

var $ = jQuery.noConflict();
$(document).ready(function () {
    $("#plupload-browse-button").ajaxStart(function () {
    }).ajaxStop(function () {
        var count = 0;
        $.each($('#media-items').children(), function () {
            var self = $(this);
            if (self.hasClass('media-item')) {
                if (self.find('button').length == 0) {
                    var url = self.find('a').attr('href');
                    var post_id = getParameterByName('post', url);
                    var data = {
                        'action': 'get_attachment_url',
                        'post_id': post_id
                    };
                    $.post(media_script.ajax_url, data, function (response) {
                        self.find('a').after("<button type='button' data-clipboard-text='" + response + "' data-post_id='" + post_id + "' id='copy-" + count + "' class='copy-attachment button' style='float:right;margin-top: 5px;margin-right: 5px;'>Copy</button>");
                        self.find('img').after("<span class='copy-done' id='copy-message-" + count + "' style='margin-top: 8px; float: right; position: absolute; margin-left: 44%;display:none;color:#3CA5CE;font-weight: bold;'>copied to the clipboard</span>");
                    });
                }
                ;
            }
            ;
            count++;
        });
        setTimeout(function () {
            $('.copy-attachment').click();
        }, 100);
        $('.copy-attachment').click(function () {
            var id = $(this).attr('id');
            var client = new ZeroClipboard(document.getElementById(id));
            client.on("ready", function (readyEvent) {
                // alert( "ZeroClipboard SWF is ready!" );
                client.on("aftercopy", function (event) {
                    $('#copy-message-' + id.split('-')[1]).fadeIn(1000);
                    setTimeout(function () {
                        $('#copy-message-' + id.split('-')[1]).fadeOut(1000);
                    }, 2000);
                });
            });
        });
    });
});

function getParameterByName(name, url) {
    if (!url)
        url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
    if (!results)
        return null;
    if (!results[2])
        return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

