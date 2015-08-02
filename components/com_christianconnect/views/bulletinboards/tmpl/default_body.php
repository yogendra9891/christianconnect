<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$user		= JFactory::getUser();

			 $k = 0;
			  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
   				 {
			  $row =& $this->items[$i];
	    	  $checked = JHTML::_('grid.id', $i, $row->id );
	        $link = JRoute::_( 'index.php?option=com_christianconnect&task=bulletinboard.edit&id='. (int)$row->id );

			$ordering	= ($this->state->get('list.ordering') == 'a.ordering');
			$canDo		= ChristianconnectHelper::getActions();
			$canChange	= $canDo->get('core.edit.status');
			?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $checked;?>
		</td>
		<td>
		 <?php if (isset($row->title)) { ?>
			<?php /* if ($canDo->get('core.create') || $canDo->get('core.edit'))  : */?>
			<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			<?php /* else : ?>
						<?php echo $row->title; ?>
					<?php endif; */?>
		<?php } ?> 
		</td>
		
		<td>
		 <?php if (isset($row->start_date)) { ?>
		 <?php echo JHtml::_('date',$row->start_date, JText::_('DATE_FORMAT_LC4')); ?>
		 <?php } ?> 
		</td>
		
		<td>
		 <?php if (isset($row->end_date)) { ?>
		 <?php echo JHtml::_('date',$row->end_date, JText::_('DATE_FORMAT_LC4')); ?>
		<?php } ?> 
		</td>
		
		<td>
		 <?php if (isset($row->published)) { ?>
		 <?php echo JHtml::_('jgrid.published', $row->published, $i, 'bulletinboards', $canChange, 'cb'); ?>
		 <?php } ?> 
		</td>
		
		
		<td>
		<?php if (isset($row->id)) { ?>
		<?php /*if ($canDo->get('core.create') || $canDo->get('core.edit')) :*/ ?>
			<a href="<?php echo $link; ?>"><?php echo $row->id; ?></a>
			<?php /* else : ?>
						<?php echo $row->id; ?>
					<?php endif; */?>
		<?php } ?> 
		</td>
	</tr>
<?php
 		$k = 1 - $k; 
		} ?> 
