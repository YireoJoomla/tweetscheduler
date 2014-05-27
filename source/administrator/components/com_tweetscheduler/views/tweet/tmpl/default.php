<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$max_chars = 140;
$focus = 'after';
if($this->item->category_id > 0 && empty($this->item->message)) {
    foreach($this->lists['categories'] as $category) {
        if($category->value == $this->item->category_id) {
            if($category->params->get('url_position') == 'before') {
                $focus = 'before';
                $this->item->message = $category->url.' ';
            } else {
                $this->item->message = ' '.$category->url;
            }
            break;
        }
    }
}
?>
<script type="text/javascript">
window.addEvent('domready', function() {
    maximumChars('message', <?php echo $max_chars; ?>, 'charsLeft', 'messageWarning');
});

function randomizeTime() {
    var current_value = $('post_date').value;
    var current_timestamp = new Date(current_value);

    var current_month = current_timestamp.getMonth() + 1; 
    if(current_month < 10) current_month = '0' + current_month;

    var current_day = current_timestamp.getDate(); 
    if(current_day < 10) current_day = '0' + current_day;

    var current_date = current_timestamp.getFullYear() + '-' + current_month + '-' + current_day;

    var random_hour = Math.floor(Math.random()*24);
    <?php if($this->params->get('randomtime_officehours') == 1) : ?>
    if(random_hour < 8) random_hour = random_hour + 8;
    if(random_hour > 18) random_hour = random_hour - 6;
    <?php endif; ?>
    if(random_hour < 10) random_hour = '0' + random_hour;

    var random_minute = Math.floor(Math.random()*60);
    if(random_minute < 10) random_minute = '0' + random_minute;

    var randomDate = current_date + ' ' + random_hour + ':' + random_minute + ':00';
    $('post_date').value = randomDate;
}

var current_category_id = <?php echo (int)$this->item->category_id ?>;
var category_urls = new Array(); 
<?php foreach($this->lists['categories'] as $category) { ?>
category_urls["<?php echo $category->value; ?>"] = '<?php echo $category->url; ?>';
<?php } ?>

jQuery(window).load(function() {
    jQuery('#category_id').change(function() {
        var category_id = jQuery('#category_id').val();
        if(category_id > 0) {
            var category_url = category_urls[category_id];
            var current_url = category_urls[current_category_id];
            var messageText = jQuery('#message').val();
            if(current_url) {
                messageText = messageText.replace(current_url, category_url);
            } else {
                if(messageText) {
                    messageText = messageText + ' ' + category_url;
                } else {
                    messageText = category_url;
                }
            }
            jQuery('#message').val(messageText); 
        }
        current_category_id = category_id;
    });

    message = jQuery('#message');
    message.focus();
    <?php if($focus == 'before') : ?>
    var messageTmp = message.val();
    message.val('');
    message.val(messageTmp);
    <?php endif; ?>
});

</script>

<form method="post" name="adminForm" id="adminForm">

<?php if(JRequest::getInt('modal') == 1): ?>
<jdoc:include type="message" />
<button onclick="Joomla.submitbutton('save');"><?php echo JText::_('JSUBMIT'); ?></button>
<input type="hidden" name="modal" value="1" />
<?php endif; ?>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tbody>
<tr>
<td width="50%" valign="top">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_TWEETSCHEDULER_FIELDSET_CONTENT'); ?></legend>
        <table class="admintable">
        <tbody>
        <tr>
            <td width="100" align="right" class="key"><?php echo JText::_('COM_TWEETSCHEDULER_FIELDNAME_ID'); ?>:</td>
            <td class="value">#<?php echo $this->item->id; ?></td>
        </tr>
        <tr>
            <td width="100" align="right" class="key"><?php echo JText::_('COM_TWEETSCHEDULER_FIELDNAME_CATEGORY_ID'); ?>:</td>
            <td class="value">
                <?php echo $this->lists['category_id']; ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key"><?php echo JText::_('COM_TWEETSCHEDULER_FIELDNAME_MESSAGE'); ?>:</td>
            <td class="value">
                <textarea name="message" id="message" rows="5" cols="60"><?php echo $this->item->message; ?></textarea><br/>
                <span id="messageInfo" class="hint"><span id="charsLeft"><?php echo strlen($this->item->message); ?></span>/<?php echo $max_chars; ?> chars <span id="messageWarning"></span></span>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="title"><?php echo JText::_('COM_TWEETSCHEDULER_FIELDNAME_TITLE'); ?>:</label>
            </td>
            <td class="value">
                <input type="text" name="title" size="55" value="<?php echo $this->item->title; ?>"/>
            </td>
        </tr>
        </tbody>
        </table>
    </fieldset>
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_TWEETSCHEDULER_FIELDSET_POSTING'); ?></legend>
        <table class="admintable">
        <tbody>
        <tr>
            <td width="100" align="right" class="key"><?php echo JText::_('COM_TWEETSCHEDULER_FIELDNAME_ACCOUNT_ID'); ?>:</td>
            <td class="value"><?php echo $this->lists['account_id']; ?></td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="post_date"><?php echo JText::_('COM_TWEETSCHEDULER_FIELDNAME_POST_DATE'); ?>:</label>
            </td>
            <td class="value">
                <?php echo $this->lists['post_date']; ?>
                <input type="button" onClick="randomizeTime();" value="<?php echo JText::_('COM_TWEETSCHEDULER_RANDOM_TIME'); ?>" />
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="post_state"><?php echo JText::_('COM_TWEETSCHEDULER_FIELDNAME_POST_STATE'); ?>:</label>
            </td>
            <td class="value">
                <?php echo $this->lists['post_state']; ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="published"><?php echo JText::_('JPUBLISHED'); ?>:</label>
            </td>
            <td class="value">    
                <?php echo $this->lists['published']; ?>
            </td>
        </tr>
        <?php if(!empty($this->item->post_error)): ?>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo JText::_('COM_TWEETSCHEDULER_API_ERROR'); ?>:
            </td>
            <td class="value">    
                <?php echo implode('<br/>', explode('|', $this->item->post_error)); ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if(!empty($this->item->post_id)): ?>
        <tr>
            <td width="100" align="right" class="key">
                <?php echo JText::_('COM_TWEETSCHEDULER_API_ID'); ?>:
            </td>
            <td class="value">    
                <?php echo $this->item->post_id; ?>
            </td>
        </tr>
        <?php endif; ?>
        </tbody>
        </table>
    </fieldset>
</td>
<td width="50%" valign="top">
    <?php echo $this->loadTemplate('fieldset', array('fieldset' => 'params')); ?>
</td>
</tr>
</tbody>
</table>

<?php echo YireoHelper::getFormEnd($this->item->id); ?>
<input type="hidden" name="post_error" value="" />

</form>

<?php if(!empty($this->item->post_error)) { ?>
<strong><?php echo JText::_('COM_TWEETSCHEDULER_LATEST_ERROR'); ?></strong>: <?php echo $this->item->post_error; ?>
<?php } ?>
