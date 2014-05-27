<?php
/**
 * Joomla! component Tweetscheduler
 *
 * @author Yireo
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Define the actions            
$actions = array();
$actions['index.php?option=com_tweetscheduler&view=account&id='.$item->id] = JText::_('LIB_YIREO_VIEW_EDIT');
if(empty($item->consumer_key) || empty($item->consumer_secret)) {
    $actions['index.php?option=com_tweetscheduler&view=account&cid[]='.$item->id] = JText::_('Complete');
} else {
    if(empty($item->oauth_token)) {
        $actions['index.php?option=com_tweetscheduler&view=account&task=redirectAuthorize&id='.$item->id] = JText::_('Authorize');
    } else {
        $actions['index.php?option=com_tweetscheduler&view=account&task=redirectAuthorize&id='.$item->id] = JText::_('Reauthorise');
        $actions['index.php?option=com_tweetscheduler&view=account&task=test&id='.$item->id] = JText::_('Test');
    }
}
?>
<td>
    <?php if ($this->isCheckedOut($item)) { ?>
        <span class="checked_out"><?php echo $item->title; ?></span>
    <?php } else { ?>
        <a href="<?php echo $item->edit_link; ?>" title="<?php echo JText::_('LIB_YIREO_VIEW_EDIT'); ?>"><?php echo $item->title; ?></a>
    <?php } ?>
    &nbsp;[<?php echo $item->type; ?>]
</td>
<td>
    <?php foreach($actions as $url => $action) { ?>
    <a href="<?php echo $url; ?>" title="<?php echo $action; ?>"><?php echo $action; ?></a> &nbsp; | &nbsp;
    <?php } ?>
</td>
