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
<th width="300" class="title">
    <?php echo JHtml::_('grid.sort', 'LIB_YIREO_TABLE_FIELDNAME_TITLE', 'account.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="200" class="title">
    <?php echo JText::_('LIB_YIREO_ACTIONS'); ?>
</th>
