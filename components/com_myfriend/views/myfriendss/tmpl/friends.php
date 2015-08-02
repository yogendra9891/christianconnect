<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_myfriend
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal');

$doc = JFactory::getDocument();
$search = JRequest::getVar('searchfriend');
$limit = JRequest::getVar('limit');
?>
<script type="text/javascript">
$(document).ready(function(){
    //this is for pagination.....
	   if($('.counter').text() == '')
	   {   
		   $('.counter').addClass('-chk');
	   }
	
});
</script>

<style type="text/css">
.friends
{
    margin: 0;
    padding: 0;
    list-style-type: none;
}
.friends li
{
    display: inline;
}
.liwidth{width:300px;}
.searchedfriendswrapper ul li{list-style-type:none;}
</style>
<form id="search-friend" action="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.searchfriend'); ?>" method="post" class="form-validate">
<div class="searchedfriendswrapper" style="width:300px;">
<div><h3><?php echo JText::_('COM_MYFRIEND_RESULT_FOUND');?></h3></div>
<ul class="friends">
<div class="liwidth">
 <?php 
 foreach($this->items as $result){
  ?><li><a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewprofile&layout=friendprofile&userid='.$result->userid.'&tmpl=component');?>" class="modal" rel="{handler: 'iframe'}" >
          <?php if($result->profileimage =='')
          	$img = JURI::base().'images/default-portrait-icon.jpg';
          	else
          	$img = $result->profileimage;
          ?>

 	<img src="<?php echo $img?>" height="100" width="100"/> 
 	</a>
 	<div class="friendname1">
	 	<a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewprofile&layout=friendprofile&userid='.$result->userid.'&tmpl=component');?>" class="modal" rel="{handler: 'iframe'}" >
	 	 <?php echo $result->name?>
	 	</a>
 	</div>
 </li>
 	
<?php  } ?></div></ul>
<input type="hidden" name="searchfriend" value="<?php echo $search;?>" />
<!--<input type="hidden" name="limit" value="<?php echo $limit;?>" />-->
<?php if(count($this->items)>0)
 echo $this->pagination->getListFooter(); else echo JText::_('COM_MYFRIEND_NO_RESULT_FOUND');?>
</div>
<?php echo JHtml::_('form.token'); ?>
</form>     