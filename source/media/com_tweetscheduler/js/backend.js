/*
 * Joomla! component TweetScheduler
 *
 * @author Yireo (info@yireo.com)
 * @package TweetScheduler
 * @copyright Copyright 2014
 * @link http://www.yireo.com
 */

function maximumChars(text, maxChars, charsLeft_id, messageWarning_id)  {
    
    var textArea = $(text);

    // create a custom focused property so that we only capture keystrokes when it is
    textArea.addEvents({
        focus: function() {
            this.focused = true;
        },
        blur: function() {
            this.focused = false;
        }
    });
    
    // attach a key listener
    window.addEvent('keyup', function(e) {
        if (textArea.focused) {

            var chars = textArea.value.trim().length;
            $(charsLeft_id).innerHTML = chars;

            if (chars+1 >= maxChars) {
                $(messageWarning_id).innerHTML = 'Limit reached';
            } else {
                $(messageWarning_id).innerHTML = '';
            }
        }
    });
}

function togglePostDate(element) {
    match = element.match(/post_date_(view|edit)_([0-9a-z]+)/);
    type = match[1];
    id = match[2];
    if(type && id) {
        if(type == 'view') {
            otherElement = 'post_date_edit_' + id;
        } else {
            otherElement = 'post_date_view_' + id;
        }
        jQuery('#' + element).hide();
        jQuery('#' + otherElement).show();
    }
}

function modifyPostDate(element) {
    match = element.match(/post_date_(view|edit)_([0-9a-z]+)/);
    id = match[2];
    post_date = jQuery('#post_date_' + id).val();
    url = 'index.php?option=com_tweetscheduler&task=ajaxpost&id='.id;

    jQuery.ajax({
        type: 'POST',
        url: 'index.php?option=com_tweetscheduler&task=post_date&id=' + id,
        dataType: 'json',
        data: {post_date: post_date}
    })
    .done(function(msg) {
        jQuery('#post_date_view_' + id).html(msg.post_date);
        togglePostDate(element);
    });
}

jQuery(document).ready(function() {
    jQuery('.post_date_view').dblclick(function() {
        var elementId = jQuery(this).attr('id');
        togglePostDate(elementId);
    });
});

