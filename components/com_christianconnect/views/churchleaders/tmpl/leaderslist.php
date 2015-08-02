<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$config=JFactory::getConfig(); 
//var_dump($this->items); die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&task=churchleaders.getChurchLeader'); ?>" method="post" id="adminForm" name="adminForm">
<div>
<?php foreach($this->churchleaders as $item){ ?>
<div>
<img src="<?php echo $item->profileimage; ?>" alt="<?php echo JText::_('Profile Image'); ?>"></img>
</div>
<div>
<?php if($item->fname!=""){?>
<?php echo $item->id.' :'. $item->fname.' '.$item->lname; ?>
<?php }else{?>
<?php echo $item->email; ?>
<?php }?>
</div>
<?php }?>
</div>
<div>
<?php echo $this->leaderpagination->getPagesLinks(); ?>
</div>
<div>
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.JRequest::getVar('churchid')); ?>">Back</a>
</div>

		<input type="hidden" name="task" value="0" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="churchid" value="<?php echo JRequest::getVar('churchid'); ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>