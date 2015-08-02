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
$itemid = JRequest::getVar('Itemid'); //print_r($this->messagedata); die;
?>
<script type="text/javascript">
$(document).ready(function(){
    $("#messagereply").click(function() { 
    	sendReply('Write Reply::', function() {
    		sendReplyToFriend1();
        });
    });	
    $("#messagebodypopup").bind("focus",function(){ 
        $('#messagebodypopup').css("border-color", "none");
    });
   
    $("#mesasge_unread").click(function() {
 	   var messageparentid = $('#messageparentid').val();
 	   var messageid = $('#messageid').val(); 	   
       var url='index.php?option=com_myfriend&task=myfriendss.unreadmessage';	 
 	   $.ajax({
	        url: url,
	        type: "POST",
	        data: { messageparentid: messageparentid, messageid: messageid },
	        dataType: "json",
	        timeout: 4000,
	        success: function(data) {	
             $('#friend-message-form').submit();
	        }
	  });      
    });	
});

	function sendReply(msg,callbackYes) {
	    var ret;
	    jQuery.fancybox({
	        'modal' : true,
	        'content' : "<div style=\"margin:1px;width:240px;\"><div id=\"sendmessage2\"><h3>"+msg+"</h3></div><div style=\"text-align:right;margin-top:10px;\"><div class=\"successfullmessage\" id=\"successfullmessage\" style=\"display:none;\"><strong>Message succesfully sent.</strong></div><div id=\"inputwrapper1\"><div class=\"messagebody\"><label>Message::</label><textarea name=\"messagebody\" id=\"messagebodypopup\" rows=\"4\" clos=\"3\"></textarea></div><button id=\"fancyconfirm_cancel\" style=\"margin:3px;padding:0px;\" type=\"button\" ><span><span>Cancel</span></span></button><button id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\" type=\"button\"><span><span>Send</span></span></button></div></div></div>",
	        'beforeShow' : function() {
	            jQuery("#fancyconfirm_cancel").click(function() {
	                $.fancybox.close();
	            });
	            jQuery("#fancyConfirm_ok").click(function() { 
	                if($('#messagebodypopup').val() == '') {
	                    $('#messagebodypopup').css("border-color", "red");
	                    $('#messagebodypopup').focus();
	                    removeborder1('messagebodypopup');
	                    return;
                    }
	                $('#sendmessage2').hide();
	                $('#inputwrapper1').hide();
	                $('#successfullmessage').fadeOut('fast');
	                $('#successfullmessage').fadeIn(2000);                
	                window.setTimeout(function(){
	                	$.fancybox.close(); //execute fancy box close function
	                },3000);
	                callbackYes();
	            });
	        }
	    });
	}
	function removeborder1(id){ 
	    $("#"+id).bind("keyup",function(){ 
	        $("#"+id).css("border-color", "");
	       });
	}

	function sendReplyToFriend1() { 
		var messagebody = $('#messagebodypopup').val(); 
		var replyto = $('#replyto').val();
		var messageparentid = $('#messageparentid').val();
	    var url='index.php?option=com_myfriend&task=myfriendss.messagereply';	 
		   $.ajax({
		        url: url,
		        type: "POST",
		        data: { parentid: messageparentid, replyto: replyto, body: messagebody  },
		        dataType: "json",
		        timeout: 4000,
		        success: function(data) {	
	//	        	$('#fadeoutdiv').fadeOut().next().delay(5000).fadeIn();
		        }
		  });    
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.allmessage&Itemid= '.$itemid); ?>" name="friend-message-form" id="friend-message-form" method="post">
<div class="inboxmessages"><a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.allmessage&Itemid='.$itemid);?>"><?php echo JText::_('COM_FRIEND_INBOX_MESSAGE'); ?></a></div>
<div id="friendmessagearea" class="friendmessagearea_message">
<!--      <div id="fadeoutdiv" style="display:none;">Message sent</div>-->
	          <?php if($this->messagedata->profileimage =='')
	          	$profileimg = JURI::base().'images/default-portrait-icon.jpg';
	          	else
	          	$profileimg = $this->messagedata->profileimage;
	          ?>
  <div class="profilenotifyimage-message"><img src="<?php echo $profileimg; ?>" height=100px; width=100px;/></div>
  <div class="messageinfo">
  <div class="friendemailname"><?php if(!empty($this->messagedata->fname)) echo $this->messagedata->fname; else echo $this->messagedata->email;?></div>
  <?php if(!empty($this->messagedata->subject)) {?><div id="messagesubject" class="messagesubject_message"><?php echo $this->messagedata->subject; ?></div> <?php }?>
  <div id="messagebody" class="messagebody_message"><?php echo $this->messagedata->body; ?></div>
  <div class="message-posted-on"><?php echo $this->messagedata->postedon; ?></div><div class="markreplyunread"><a href="#" id="mesasge_unread"><span><span><?php echo JText::_('COM_MYFRIEND_MARK_UNREAD_MESSAGE');?></span></span></a></div>
  <input type="hidden" id="replyto" value="<?php echo $this->messagedata->messagefrom;?>" />
  <?php if(@$this->messagedata->parentid >0) { $messageparentid = $this->messagedata->parentid;} else $messageparentid = $this->messagedata->messageid; ?>
  <input type="hidden" id="messageparentid" value="<?php echo $messageparentid;?>" />  
  <input type="hidden" id="messageid" value="<?php echo $this->messagedata->messageid;?>" /> 
  <div class="messagereply" id="messagereply"><a href="#"><span><span><?php echo JText::_('COM_FRIEND_REPLY_MESSAGE'); ?></span></span></a></div>
  </div>
</div>
 
 <?php echo JHtml::_('form.token'); ?>
</form>
