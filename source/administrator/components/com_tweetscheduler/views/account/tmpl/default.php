<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<form method="post" name="adminForm" id="adminForm">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tbody>
<tr>
<td width="50%" valign="top">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Account Details' ); ?></legend>
        <table class="admintable">
        <tbody>
        <tr>
            <td width="40%" align="right" class="key"><?php echo JText::_( 'ID' ); ?>:</td>
            <td class="value">#<?php echo $this->item->id; ?></td>
        </tr>
        <tr>
            <td align="right" class="key"><?php echo JText::_( 'Title' ); ?>:</td>
            <td class="value"><input type="text" name="title" value="<?php echo $this->item->title; ?>"/></td>
        </tr>
        <tr>
            <td align="right" class="key"><?php echo JText::_( 'Type' ); ?>:</td>
            <td class="value"><?php echo $this->lists['type']; ?></td>
        </tr>
        <tr>
            <td align="right" class="key"><?php echo JText::_( 'Consumer key' ); ?>:</td>
            <td class="value"><input type="text" name="consumer_key" value="<?php echo $this->item->consumer_key; ?>" size="60" /></td>
        </tr>
        <tr>
            <td align="right" class="key"><?php echo JText::_( 'Consumer secret' ); ?>:</td>
            <td class="value"><input type="text" name="consumer_secret" value="<?php echo $this->item->consumer_secret; ?>" size="60" /></td>
        </tr>
        <tr>
            <td align="right" class="key"><?php echo JText::_( 'OAuth token' ); ?>:</td>
            <td class="value"><input type="text" name="oauth_token" value="<?php echo $this->item->oauth_token; ?>" size="60" /></td>
        </tr>
        <tr>
            <td align="right" class="key"><?php echo JText::_( 'OAuth token secret' ); ?>:</td>
            <td class="value"><input type="text" name="oauth_token_secret" value="<?php echo $this->item->oauth_token_secret; ?>" size="60" /></td>
        </tr>
        <tr>
            <td align="right" class="key">
                <label for="published"><?php echo JText::_( 'Published' ); ?>:</label>
            </td>
            <td class="value">
                <?php echo $this->lists['published']; ?>
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" class="key">
                <label for="ordering"><?php echo JText::_( 'Ordering' ); ?>:</label>
            </td>
            <td class="value">
                <?php echo $this->lists['ordering']; ?>
            </td>
        </tr>
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

<input type="hidden" name="option" value="com_tweetscheduler" />
<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php if(!empty($this->pages)) : ?>
<script>
var pages = <?php echo json_encode($this->pages); ?>;
jQuery(document).ready(function() {
    jQuery.each(pages, function(key, val) {
        jQuery('#paramspage')
            .append(jQuery('<option>', {value : key})
            .text(val));
    });
    jQuery('#paramspage').val(jQuery('#page_current').val());
});
</script>
<?php endif; ?>

