<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<tr>
			<th width="5%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>	
				
				<th width="50%">
				<?php echo JHtml::_('grid.sort', 'COM_CHRISTIANCONNECT_CHURCHS_CNAME', 'a.cname', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th width="30%">
				<?php echo JHtml::_('grid.sort', 'COM_CHRISTIANCONNECT_CHURCHS_COUNTRY', 'a.country', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th class="nowrap" width="1%">
					<?php echo JText::_('STATUS'); ?>
				</th>
                
 				<th width="5%">
				<?php echo JHtml::_('grid.sort', 'ID', 'a.id', $this->state->get('list.direction'),$this->state->get('list.ordering')); ?>
				</th>	
				
</tr>