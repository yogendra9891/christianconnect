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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'bulletinboard.cancel' || document.formvalidator.isValid(document.id('bulletin_form'))) {
			Joomla.submitform(task, document.getElementById('bulletin_form'));
		}
	}
</script>
 <form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_christianconnect&layout=edit&id='.(int) $this->item->id); ?>" method="post" id="bulletin_form" name="bulletin_form">

		  <fieldset>
                <dl>
                <dd></dd>
                <dt><strong>
				<?php echo empty($this->item->id) ? JText::_('COM_CHRISTIANCONNECT_BULLETIN_NEW') : JText::sprintf('COM_CHRISTIANCONNECT_BULLETIN_EDIT', $this->item->id); ?>
				</strong></dt>
				<dd></dd>
				
				<dt><?php echo $this->form->getLabel('title'); ?></dt>
				<dd><?php echo $this->form->getInput('title'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('description'); ?></dt>
				<dd><?php echo $this->form->getInput('description'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('start_date'); ?></dt>
				<dd><?php echo $this->form->getInput('start_date'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('end_date'); ?></dt>
				<dd><?php echo $this->form->getInput('end_date'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('published'); ?></dt>
				<dd><?php echo $this->form->getInput('published'); ?></dd>
				
				<dd></dd>
				<dt><?php echo $this->form->getLabel('id'); ?></dt>
				<dd><?php echo $this->form->getInput('id'); ?></dd>
				<dd></dd>
		</fieldset>

	
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</div>

<div class="clr"></div>
</form>
