<?php
/**
 * Joomla! Yireo Library
 *
 * @author Yireo
 * @package YireoLib
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com/
 * @version 0.4.3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<th class="title">
    <?php echo JHtml::_('grid.sort',  'COM_TWEETSCHEDULER_FIELDNAME_MESSAGE', 'tweet.message', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="10">
    <?php echo JText::_('COM_TWEETSCHEDULER_CHARACTERS'); ?>
</th>
<th width="110" class="title">
    <?php echo JHtml::_('grid.sort',  'COM_TWEETSCHEDULER_FIELDNAME_CATEGORY_ID', 'tweet.category_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="100" class="title">
    <?php echo JHtml::_('grid.sort',  'COM_TWEETSCHEDULER_FIELDNAME_ACCOUNT_ID', 'tweet.account_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="160" class="title">
    <?php echo JHtml::_('grid.sort',  'COM_TWEETSCHEDULER_FIELDNAME_POST_DATE', 'tweet.post_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="80" class="title">
    <?php echo JHtml::_('grid.sort',  'COM_TWEETSCHEDULER_FIELDNAME_POST_STATE', 'tweet.post_state', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="100" class="title">
    <?php echo JText::_('LIB_YIREO_ACTIONS'); ?>
</th>
