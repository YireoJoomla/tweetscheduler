<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Do not automatically generate all columns
$auto_columns = false;

// Define the actions            
$actions = array();
$actions['index.php?option=com_tweetscheduler&view=tweet&task=send&id='.$item->id] = JText::_('COM_TWEETSCHEDULER_POST_TWEET');
if($item->post_state == 1) {
    $actions[$item->edit_link] = JText::_('COM_TWEETSCHEDULER_RESCHEDULE_TWEET');
}
?>
<td>
    <div style="height:34px; overflow:hidden;">
    <?php if ($this->isCheckedOut($item)) { ?>
        <span class="checked_out"><?php echo $item->message; ?></span>
    <?php } else { ?>
        <a href="<?php echo $item->edit_link; ?>" title="<?php echo JText::_('LIB_YIREO_VIEW_EDIT'); ?>"><?php echo $item->message; ?></a>
    <?php } ?>
    </div>
</td>
<td>
    <?php echo strlen($item->message); ?>
</td>
<td class="small">
    <?php echo $item->category_name; ?>
</td>
<td>
    <?php if(!empty($item->accounts)) : ?>
        <?php foreach($item->accounts as $account) : ?>
            <?php echo $account->title; ?> 
            <?php 
            if($account->type == 'twitter') echo '<i class="fa fa-twitter fa-1g"></i>&nbsp;';
            if($account->type == 'facebook') echo '<i class="fa fa-facebook fa-1g"></i>&nbsp;';
            if($account->type == 'linkedin') echo '<i class="fa fa-linkedin fa-1g"></i>&nbsp;';
            ?>
            <br/>
        <?php endforeach; ?>
    <?php else: ?>
        <?php echo JText::_('LIB_YIREO_VIEW_LIST_NO_ITEMS'); ?> 
    <?php endif; ?>
</td>
<td class="small">
    <div class="post_date_view" id="post_date_view_<?php echo $item->id; ?>">
        <?php echo TweetschedulerHelper::formatDatetime($item->post_date); ?>
        (<?php echo TweetschedulerHelper::getRelativeTime($item->raw_post_date, $item->utc); ?>)
    </div>
    <div class="post_date_edit" id="post_date_edit_<?php echo $item->id; ?>">
        <?php echo JHTML::_('calendar', $item->post_date, 'post_date['.$item->id.']', 'post_date_'.$item->id, 
            '%Y-%m-%d %H:%M:%S', array('class' => 'inputbox')); ?>
        <button class="btn" onclick="modifyPostDate('post_date_edit_<?php echo $item->id; ?>');return false;">
            <?php echo JText::_('JAPPLY'); ?>
        </button>
        <button class="btn" onclick="togglePostDate('post_date_edit_<?php echo $item->id; ?>');return false;">
            <?php echo JText::_('JCANCEL'); ?>
        </button>
    </div>
</td>
<td class="small">
    <?php switch($item->post_state) {
        case 2:
            echo JText::_('LIB_YIREO_BLOCKED');
            break;
        case 1:
            echo JText::_('LIB_YIREO_POSTED');
            break;
        case 0:
        default:
            echo JText::_('LIB_YIREO_PENDING');
            break;
    } ?>
</td>
<td>
    <?php foreach($actions as $url => $action) { ?>
    <a href="<?php echo $url; ?>" title="<?php echo $action; ?>"><?php echo $action; ?></a>
    <?php } ?>
</td>
