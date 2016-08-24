<?php
/*
 * Joomla! component Tweetscheduler
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
	function checkUrl(url) {
		if (url == '') {
			alert('Please enter an URL');
			return false;
		}

		if (!url.match(/^(http|https|ftp):\/\//)) {
			alert('This is not a valid URL:' + url);
			return false;
		}

		if (url.match(/^http:\/\/(tinyurl\.com|bit\.ly|goo\.gl)/)) {
			return false;
		}

		return true;
	}

	<?php foreach($this->shorteners as $shortenerCode => $shortener) : ?>
	function parse <?php echo ucfirst($shortenerCode); ?>(old_value, element_id) {
		if (checkUrl(old_value) == false) return;
		url = 'index.php?option=com_tweetscheduler&view=category&format=ajax&shortener=<?php echo $shortenerCode; ?>&url=' + old_value;
		getAjax(url, element_id, 'input');
	}
	<?php endforeach; ?>
</script>
<form method="post" name="adminForm" id="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tbody>
		<tr>
			<td width="50%" valign="top">
				<fieldset class="adminform">
					<legend><?php echo JText::_('Details'); ?></legend>
					<table class="admintable">
						<tbody>
						<tr>
							<td width="100" align="right" class="key">
								<?php echo JText::_('ID'); ?>:
							</td>
							<td class="value">
								#<?php echo $this->item->id; ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="title"><?php echo JText::_('Title'); ?>:</label>
							</td>
							<td class="value">
								<input type="text" name="title" value="<?php echo $this->item->title; ?>"/>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="url"><?php echo JText::_('URL'); ?>:</label>
							</td>
							<td class="value">
								<input type="text" name="url" value="<?php echo $this->item->url; ?>" id="url"
								       size="60"/><br/>
								<?php foreach ($this->shorteners as $shortenerCode => $shortener) : ?>
									<input type="button" class="button"
									       onClick="parse<?php echo ucfirst($shortenerCode); ?>($('url').value, 'url');"
									       value="<?php echo $shortener->getTitle(); ?>"/>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="published"><?php echo JText::_('Published'); ?>:</label>
							</td>
							<td class="value">
								<?php echo $this->lists['published']; ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="ordering"><?php echo JText::_('Ordering'); ?>:</label>
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

	<input type="hidden" name="option" value="com_tweetscheduler"/>
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>
</form>
