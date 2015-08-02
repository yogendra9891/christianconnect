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
$itemid = JRequest::getVar('Itemid'); 
//print_r($this->messagedata); exit;
?>
<script type="text/javascript">
$(document).ready(function(){
	$('.deletefriendmessage').click(function(){ 
		   var parentid = $(this).attr('name');
		   var messageid1 = $(this).attr('id');
	       var url='index.php?option=com_myfriend&task=myfriendss.deletemessage';	 
	 	   $.ajax({
		        url: url,
		        type: "POST",
		        data: { parentid: parentid, messageid: messageid1 },
		        dataType: "json",
		        timeout: 4000,
		        success: function(data) {	//alert(messageid1);
		            $('#frienmessagearea1_'+messageid1).hide();
		        }
		  });      
	  });
    $('.unreadfriendmessage').click(function() {
  	   var messageparentid = $(this).attr('name');
  	   var messageid2 = $(this).attr('id'); 	   
        var url='index.php?option=com_myfriend&task=myfriendss.unreadmessage';	 
  	   $.ajax({
 	        url: url,
 	        type: "POST",
 	        data: { messageparentid: messageparentid, messageid: messageid2 },
 	        dataType: "json",
 	        timeout: 4000,
 	        success: function(data) {	
              $('#all-message-form').submit();
 	        }
 	  });      
     });
    //this is for pagination.....
    if($('.counter').text() == '')
    {   
 	   $('.counter').addClass('-chk');
    }
     	
});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.allmessage&Itemid= '.$itemid); ?>" id="all-message-form" class="all-message-form" method="post">
<div class="allmessagedata">
 <div class="messages"><?php echo JText::_('COM_MYFRIEND_MESSAGE');?></div>
  <?php if(count($this->messagedata) == 0){?><h2><?php echo JText::_('COM_MYFRIEND_NOT_MESSAGE');?></h2><?php }?>
		<ul>
		<?php 
		  foreach($this->messagedata as $message)
		   { ?>
			 <li class="friendmessage" id="frienmessagearea1_<?php echo $message->messageid;?>">
	          <?php if($message->profileimage =='')
	          	$img = JURI::base().'images/default-portrait-icon.jpg';
	          	else
	          	$img = $message->profileimage;
	          ?>

		  <div class="profilenotifyimage"><img src="<?php echo $img; ?>" height=100px; width=100px;/></div>
			 
			<?php if(!empty($message->fname)) $name = $message->fname; else  $name = $message->email;?>
			 	<?php if(@$message->parentid > 0) $parentid= '&parentid='.$message->parentid; else $parentid ='';?>
			 	<div class="messagebody"> <div class="sendername"><a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewrequestprofile&userid='.$message->messagefrom.'&Itemid='.$itemid);?>"><?php echo $name;?></a></div>
				 	<a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.friendmessage&messageid='.$message->messageid.'&messagefrom='.$message->messagefrom.''.$parentid.'&Itemid='.$itemid);?>">
				 	<?php if(@$message->parentid > 0) echo JText::_('COM_MYFRIEND_MESSAGE_TO_YOU'); else echo JText::_('COM_MYFRIEND_MESSAGE_TO_YOU');?></a>
			 	</div>
			 	<input type="hidden" id="messagefrom" name="messagefrom" value="<?php echo $message->messagefrom; ?>" />
			 	<input type="hidden" id="parentid" name="parentid" value="<?php echo $message->parentid; ?>" />
			 	<?php if($message->status){ ?>
			 	<div id="unreadmessage"><a href="#" id="<?php echo $message->messageid;?>" name="<?php echo $message->parentid;?>" class="unreadfriendmessage"><span><span><?php echo JText::_('COM_MYFRIEND_MARK_UNREAD_MESSAGE');?></span></span> </a></div>
			 	<?php }?>
			 	<div id="deletemessage"><a href="javascript:void(0);" id="<?php echo $message->messageid;?>" name="<?php echo $message->parentid;?>" class="deletefriendmessage"><span><span><?php echo JText::_('COM_MYFRIEND_MESSAGE_DELETE');?></span></span></a></div>
			 	</li>
		<?php }?>
		</ul>
  <div>
	<?php if(count($this->messagedata)>0)
	echo $this->pagination->getListFooter();?>
 </div>
</div>
	<?php echo JHtml::_('form.token'); ?>
</form>