<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_christianconnect/assets/css/christianconnect.css');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('admin-form'))) {
			Joomla.submitform(task, document.getElementById('admin-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="admin-form" class="form-validate">
	
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_CHRISTIANCONNECT_CATEGORY_NEW') : JText::sprintf('COM_CHRISTIANCONNECT_CATEGORY_EDIT', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>
				
				<li><?php echo $this->form->getLabel('map_pin'); ?>
				<?php echo $this->form->getInput('map_pin'); ?></li>
				
				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
				
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				</ul>
		</fieldset>

	
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</div>

<div class="clr"></div>
</form>
