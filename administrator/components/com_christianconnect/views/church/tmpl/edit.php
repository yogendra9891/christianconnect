<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_christianconnect/assets/css/christianconnect.css');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'church.cancel' || document.formvalidator.isValid(document.id('admin-form'))) {
			Joomla.submitform(task, document.getElementById('admin-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&layout=edit&id='.(int) $this->item->id); ?>" method="post" enctype="multipart/form-data"  name="adminForm" id="admin-form" class="form-validate">
	
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_CHRISTIANCONNECT_CHURCH_NEW') : JText::sprintf('COM_CHRISTIANCONNECT_CHRUCH_EDIT', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('cname'); ?>
				<?php echo $this->form->getInput('cname'); ?></li>
				
				<li><?php echo $this->form->getLabel('address1'); ?>
				<?php echo $this->form->getInput('address1'); ?></li>
				
				<li><?php echo $this->form->getLabel('address2'); ?>
				<?php echo $this->form->getInput('address2'); ?></li>
				
				<li><?php echo $this->form->getLabel('city'); ?>
				<?php echo $this->form->getInput('city'); ?></li>
				
				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>
				
				<li><?php echo $this->form->getLabel('country'); ?>
				<?php echo $this->form->getInput('country'); ?></li>
				
				<li><?php echo $this->form->getLabel('postcode'); ?>
				<?php echo $this->form->getInput('postcode'); ?></li>

				<li><?php echo $this->form->getLabel('phone'); ?>
				<?php echo $this->form->getInput('phone'); ?></li>

			    <li><?php echo $this->form->getLabel('siteurl'); ?>
				<?php echo $this->form->getInput('siteurl'); ?></li>

				<li><?php echo $this->form->getLabel('category'); ?>
				<?php echo $this->form->getInput('category'); ?></li>
				
				<li><?php echo $this->form->getLabel('subscription_price'); ?>
				<?php echo $this->form->getInput('subscription_price'); ?></li>
				
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
