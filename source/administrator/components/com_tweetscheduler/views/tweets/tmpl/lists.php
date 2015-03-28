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

JHTML::_('behavior.modal');
?>
<div style="float:left">
<!--
<div class="post_date_view" id="post_date_view_start">
    <button href="#" onclick="togglePostDate('post_date_view_start');return false;" class="btn">
        <?php echo JText::_('COM_TWEETSCHEDULER_SELECT_TIME'); ?>
    </button>
</div>
-->
<div class="post_date_edit" id="post_date_edit_start" style="border: 2px solid #ddd; padding:10px;">
    <?php echo $this->lists['start_post_date_filter'] = JHTML::_('calendar', '', 'start_post_date', 'start_post_date', '%Y-%m-%d', array('class' => 'inputbox')); ?><br/>
    <button class="btn" onclick="modifyPostDate('post_date_edit_start');return false;">
        <?php echo JText::_('JAPPLY'); ?>
    </button><br/>
    <button class="btn" onclick="togglePostDate('post_date_edit_start');return false;">
        <?php echo JText::_('JCANCEL'); ?>
    </button><br/>
</div>
</div>

<?php echo $this->lists['category_filter']; ?>
<?php echo $this->lists['account_filter']; ?>
<?php echo $this->lists['state_filter']; ?>
<?php echo $this->lists['post_state_filter']; ?>
