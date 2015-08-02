<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<tr>
				<th width="5%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>	
				
				<th width="25%">
				<?php echo JHtml::_('grid.sort', 'TITLE', 'a.title', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th width="25%">
				<?php echo JHtml::_('grid.sort', 'START_DATE', 'a.start_date', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th width="25%">
				<?php echo JHtml::_('grid.sort', 'END_DATE', 'a.end_date', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th width="10%">
					<?php echo JText::_( 'STATUS' ); ?>
				</th>
                
 				<th width="10%">
				<?php echo JHtml::_('grid.sort', 'ID', 'a.id', $this->state->get('list.direction'),$this->state->get('list.ordering')); ?>
				</th>	
                
		
	
</tr>