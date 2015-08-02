<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
$churchid=(int)JRequest::getVar('churchid');
//echo "<pre>";var_dump($this->friends); die;

?>
<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&view=friendlists'); ?>" method="post" name="adminForm">
<div class="fulllist">

<div>Friend</div>
<div>
<!--display friend-->
<?php if(count($this->items)!=0){?>
<?php  foreach($this->items as $friend){
	 
		?>
<div class="namelist">
<?php if($friend->fname==''){
		echo $friend->email;
		}else{
		echo $friend->fname.' '.$friend->lname;
		} ?>
</div>
<div class="imagelist"><img src="<?php echo $friend->profileimage; ?>" alt="<?php echo $friend->fname.' '.$friend->lname; ?>"></img></div>
<?php }}else{?>
No Records found
<?php }?>
</div>
<div>
<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div class="backlist">
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$churchid);?>"><?php echo JText::_('Back');?></a>
</div>

</div>
<input type="hidden" name="churchid" value="<?php echo $this->churchid;?>" />

</form>