<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$config=JFactory::getConfig(); 
echo "hello"; 
//var_dump($this->items); die;
?>

<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&view=churchleaders'); ?>" method="post" id="adminForm" name="adminForm">
<table height="50%" width="50%" align="center">
 <?php echo $this->addToolbar();?>
<?php foreach($this->items as $item){ ?>
<tr><td>
<input type="checkbox" name="cid[]" value="<?php echo $item->id;?>">
</td>
<td>
<img src="<?php echo $item->profileimage; ?>" alt="<?php echo JText::_('Profile Image'); ?>"></img>
</td>
<td>
<?php if(trim($item->fname)==''){?>
 <?php echo $item->email; ?><br>
<?php }else{?>
<?php echo $item->fname.' '.$item->lname; ?><br>
<?php }?>
<?php if($item->leaderid!="0"){echo "Church Leader";?><br>
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=churchleaders.removeLeaders&leaderid='.$item->leaderid.'&churchid='.JRequest::getVar('churchid'));?>">Remove Leader</a>
<?php }?>
</td></tr>
<?php }?>
<tr><td>
<?php echo $this->pagination->getPagesLinks(); ?>
</td></tr>
<tr><td>
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$item->leaderid.'&churchid='.JRequest::getVar('churchid'));?>">Back</a>
</td></tr>
</table>
		<input type="hidden" name="task" value="0" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="churchid" value="<?php echo JRequest::getVar('churchid'); ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>