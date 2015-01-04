<?php
/**
 * Joomla! Form Field - Components
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import classes
jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');

/**
 * Form Field-class for selecting a page
 */
class JFormFieldPage extends JFormField
{
    /*
     * Form field type
     */
    public $type = 'Page';

    /*
     * Method to construct the HTML of this element
     *
     * @param null
     * @return string
     */
    protected function getInput()
    {
        $name = $this->name;
        $value = $this->value;
        $db = JFactory::getDBO();

        $options = array();
        $options[] = JHTML::_('select.option',  null, JText::_('JDEFAULT'), 'value', 'text');

        $attribs = 'class="inputbox"';
        $html = '<input type="hidden" id="page_current" value="'.$value.'" />';
        $html .= JHTML::_('select.genericlist',  $options, $name, $attribs, 'value', 'text', $value, $name);
        return $html;
    }
}
