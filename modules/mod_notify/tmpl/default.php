<?php
/**
 * @author Notification Team
 * @copyright daffodilsw.com
 * @version 1.0.3
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
$doc = JFactory::getDocument();
$doc->addScript(JURI::base().'modules/mod_notify/assests/jquery.reveal.js');
$doc->addStylesheet(JURI::base().'modules/mod_notify/assests/reveal.css'); 
$itemid = JRequest::getVar('Itemid'); 
?>
<script type="text/javascript">/*<![CDATA[*/
$(document).ready(function(){
	$('.requestaccept').click(function(){ 
		   var requestid = $(this).attr('name');
		   var connectfrom = $('#connectfrom').val();
	       var url='index.php?option=com_myfriend&task=myfriendss.acceptrequest';	 
	    	   $.ajax({
		        url: url,
		        type: "POST",
		        data: { requestid: requestid, connectfrom: connectfrom },
		        dataType: "json",
		        timeout: 4000,
		        success: function(data) {	
		            $('#frienshiparea_'+requestid).hide();
		            var count = parseInt($('#requestcount').text());
		            $('#requestcount').text(count-1);	
		            if(count<2){ $('#requestcount').hide();}
		            $('#friendrequestername').text(' '+$('#finalacceptrequest_'+requestid).text());
		            var userid = $('#requesteduserid_'+requestid).text();
		            var itemid = <?php echo $itemid; ?>;
		            var friendurl = 'index.php?option=com_myfriend&task=myfriendss.viewrequestprofile&userid='+userid+'&Itemid='+itemid;	
		            $('#friendrequestername').attr('href', friendurl);   
		            $('#finalacceptrequest').show();         
		        }
		  });      
	});

	$('.requestreject').click(function(){ 
	   var rejectid = $(this).attr('name');
       var url='index.php?option=com_myfriend&task=myfriendss.denyrequest';	 
 	   $.ajax({
	        url: url,
	        type: "POST",
	        data: { rejectid: rejectid },
	        dataType: "json",
	        timeout: 4000,
	        success: function(data) {	
	            $('#frienshiparea_'+rejectid).hide();
	            var count = parseInt($('#requestcount').text());
	            $('#requestcount').text(count-1);
	            if(count<2){ $('#requestcount').hide();}
	            $('#finalacceptrequest').hide();
	        }
	  });      
  });
});
 </script> 
 <style type="text/css">
 .friendrequest{margin-bottom: 10px;}
 </style>
<div class="notificationwrapper">

	<a id='requestshow' data-reveal-id="myModal" href="#"><?php echo JText::_('MOD_NOTIFY_REQUEST');?>
	<?php 
	 if($requestcount = count($friendrequest)){ ?><span href="#" class="requestcount" id="requestcount">
	<?php  echo $requestcount; ?></span><?php 
	}?></a>
	<a id='messageshow' data-reveal-id="message-myModal" href="#"><?php echo JText::_('MOD_NOTIFY_MESSAGES');?>
	<?php 
	 if($messagescount = count($friendmessages)){ ?><span href="#" class="messagecount" id="messagecount">
	<?php  echo $messagescount; ?></span><?php 
	}?></a>
<!-- div for showing friend request... -->
	<div id="myModal" class="reveal-modal">
		<?php if(count($friendrequest) == 0){?><h3><?php echo JText::_('MOD_NOTIFY_REQUEST_NOTPENDING');?></h3><?php }?>
		<ul>
				<?php // if(!empty($request->fname)) $name = $request->fname; else  $name = $request->email;?>
			 	<div class="finalacceptrequest" id="finalacceptrequest" style="display:none;"><h2><?php echo JText::_('MOD_NOTIFY_YOU_ARE_NOW_FRIEND'); ?><a href="#" id="friendrequestername" class="friendrequestername"><?php echo ' '.$name;?></a></h2></div>
		
			<?php 
			 foreach($friendrequest as $request)
			 { 
			 	if(!empty($request->fname)) $name = $request->fname; else  $name = $request->email;?>
			    <div id="finalacceptrequest_<?php echo $request->requestid;?>" style="display:none;"><?php echo $name;?></div>
			    <div id="requesteduserid_<?php echo $request->requestid;?>" style="display:none;"><?php echo $request->connectfrom;?></div>
			    <li class="friendrequest" id="frienshiparea_<?php echo $request->requestid;?>">
			          <?php if($request->profileimage =='')
			          	$img = JURI::base().'images/default-portrait-icon.jpg';
			          	else
			          	$img = $request->profileimage;
			          ?>
				 	<div class="profilenotifyimage"><img src="<?php echo $img; ?>" height=100px; width=100px;/></div>
				 	<div class="friendinfo">
					 	<div class="friendname">
					 		<a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewrequestprofile&userid='.$request->connectfrom.'&requestid='.$request->requestid.'&Itemid='.$itemid);?>">
					 			<?php if(!empty($request->fname)) echo $request->fname; else echo $request->email;?>
					 		</a>
					 	</div>
					 	<div class="acceptremove">
					 	 <a href="#" class="requestaccept" id="accept_<?php echo $request->requestid;?>" name="<?php echo $request->requestid;?>">
					 	 	<span><span><?php echo JText::_('MOD_NOTIFY_ACCEPT');?> </span></span>
					 	 </a>
					 	 <a href="#" class="requestreject" id="reject_<?php echo $request->requestid;?>" name="<?php echo $request->requestid;?>">
					 	   <span><span><?php echo JText::_('MOD_NOTIFY_REJECT');?> </span></span>
					 	 </a> </div>
				 	</div>
			 	</li>
			 	<input type="hidden" id="connectfrom" name="connectfrom" value="<?php echo $request->connectfrom; ?>" />
		   <?php }?>
	   </ul>
   </div>
 <!-- div end for showing friend request... -->  

 <!-- div start for showing messages... -->  
 <div id="message-myModal" class="reveal-modal">
  <?php if(count($friendmessages) == 0){?><h2><?php echo JText::_('MOD_NOTIFY_MESSAGE_NOTPENDING');?></h2><?php }?>
		<ul>
		<?php 
		  foreach($friendmessages as $message)
		   { ?>
			 <li class="friendmessage" id="frienmessagearea_<?php echo $message->messageid;?>">
			          <?php if($message->profileimage =='')
			          	$img1 = JURI::base().'images/default-portrait-icon.jpg';
			          	else
			          	$img1 = $message->profileimage;
			          ?>

			  <div class="profilenotifyimage"><img src="<?php echo $img1; ?>" height=100px; width=100px;/></div>
			 
			<?php if(!empty($message->fname)) $name = $message->fname; else  $name = $message->email;?>
			 	<?php if(@$message->threadid > 0) $parentid= '&parentid='.$message->threadid; else $parentid ='';?>
			 	<div class="messagebody"> <div class="sendername"><a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewrequestprofile&userid='.$message->messagefrom.'&Itemid='.$itemid);?>"><?php echo $name;?></a></div>
				 	<a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.friendmessage&messageid='.$message->messageid.'&messagefrom='.$message->messagefrom.''.$parentid.'&Itemid='.$itemid);?>">
				 	<?php if(@$message->threadid == 0) echo JText::_('MOD_NOTIFY_MESSAGE_REPLY'); else echo JText::_('MOD_NOTIFY_MESSAGE_PRIVATE');?></a>
			 	</div>
			 	<input type="hidden" id="messagefrom" name="messagefrom" value="<?php echo $message->messagefrom; ?>" />
			 	<input type="hidden" id="parentid" name="parentid" value="<?php echo $message->threadid; ?>" />
			</li>
		<?php }?>
		</ul>
		<div id="showallmessages" class="showallmessages">
		 <a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.allmessage&Itemid='.$itemid);?>"><?php echo JText::_('MOD_NOTIFY_ALL_MESSAGES');?></a>
		</div>
 </div>
  <!-- div end for showing messages... -->  
</div>