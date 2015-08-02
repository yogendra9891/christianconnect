<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$user		= JFactory::getUser();

			 $k = 0;
			  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
   				 {
			  $row =& $this->items[$i];
	    	  $checked = JHTML::_('grid.id', $i, $row->id );
	        $link = JRoute::_( 'index.php?option=com_christianconnect&task=religion.edit&id='. (int)$row->id );

			$ordering	= ($this->state->get('list.ordering') == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_christianconnect');
			$canEdit	= $user->authorise('core.edit',			'com_christianconnect');
			$canCheckin	= $user->authorise('core.manage',		'com_christianconnect');
			$canChange	= $user->authorise('core.edit.status',	'com_christianconnect');
			?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $checked;?>
		</td>
		<td>
		 <?php if (isset($row->title)) { ?>
			<?php if ($canEdit) : ?>
			<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			<?php else : ?>
						<?php echo $row->title; ?>
					<?php endif; ?>
		<?php } ?> 
		</td>
		
		<td>
		 <?php if (isset($row->published)) { ?>
		 <?php echo JHtml::_('jgrid.published', $row->published, $i, 'religions.', $canChange, 'cb'); ?>
			 
		 <?php } ?> 
		</td>
		
		
		<td>
		<?php if (isset($row->id)) { ?>
		<?php if ($canEdit) : ?>
			<a href="<?php echo $link; ?>"><?php echo $row->id; ?></a>
			<?php else : ?>
						<?php echo $row->id; ?>
					<?php endif; ?>
		<?php } ?> 
		</td>
	</tr>
<?php
 		$k = 1 - $k; 
		} ?> 
