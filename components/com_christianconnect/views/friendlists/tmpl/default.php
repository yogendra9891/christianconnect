<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
$churchid=(int)JRequest::getVar('churchid');
//echo "<pre>";var_dump($this->friends); die;

?>
<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&view=friendlists'); ?>" method="post" name="adminForm">
<div class="searchedfriendswrapper">
		<div><h3>Friends::</h3></div>
<ul class="friends">
<!--display friend-->
<?php if(count($this->items)!=0){?>
<?php  foreach($this->items as $friend){
	 
		?>
<li>
<img src="<?php echo $friend->profileimage; ?>" alt="<?php echo $friend->fname.' '.$friend->lname; ?>"></img>
<span class="frndname">
<?php if($friend->fname==''){
		echo $friend->email;
		}else{
		echo $friend->fname.' '.$friend->lname;
		} ?>
</span>
</li>
<?php }}else{?>
<li>No Records found</li>
<?php }?>
</ul>
<div>
<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div class="backlist">
<a href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$churchid);?>"><?php echo JText::_('Back');?></a>
</div>

</div>
<input type="hidden" name="churchid" value="<?php echo $this->churchid;?>" />

</form>