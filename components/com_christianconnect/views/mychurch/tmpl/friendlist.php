<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
//echo "<pre>";var_dump($this->friends); die;

?>
<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getFriendList'); ?>" method="post" name="adminForm">
<table>

<tr><td>Friend</td></tr>
<tr><td>
<!--display friend-->
<?php if(count($this->friends)!=0){?>
<?php  foreach($this->friends as $friend){
	 
		?>
<tr><td>Name</td><td><?php echo $friend->id.' :'. $friend->fname.' '.$friend->lname; ?></td></tr>
<tr><td>Image</td><td><img src="<?php echo $friend->profileimage; ?>" alt="<?php echo $friend->fname.' '.$friend->lname; ?>"></img></td></tr>
<?php }}else{?>
No Records found
<?php }?>
</td></tr>
<tr><td colspan="2">
<?php   echo $this->friendpagination->getListFooter(); ?>
<?php echo $this->friendpagination->getPagesLinks(); ?>
</td></tr>
</table>

</form>