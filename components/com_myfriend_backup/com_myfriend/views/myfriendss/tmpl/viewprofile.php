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
$doc = JFactory::getDocument(); 
//this below script file is added because this is seeing in IFRame and no jquery library is there(new head tag is by default creating then we have to add these)..........
$doc->addScript(JURI::base().'components/com_users/js/jquery-1.7.1.min.js', 'text/javascript');
$doc->addScript(JURI::base().'components/com_myfriend/assests/jquery.fancybox.js');
$doc->addStylesheet(JURI::base().'components/com_myfriend/assests/jquery.fancybox.css');
$friend = JRequest::getVar('friend');
$sharebutton = JRequest::getVar('sharebutton');
//function for getting the current page url for sharing on FB/Twitter..........
function curPageURL() {
	$pageURL = '';
	$userid = JRequest::getVar('userid');
	$itemid = JRequest::getVar('Itemid');
	$pageURL.= JURI::base().'index.php?option=com_myfriend&task=myfriendss.viewprofile&userid='.$userid.'&sharebutton=true'; 
	return urlencode($pageURL);
}

?>
<script type="text/javascript">
$(document).ready(function(){
    $("#sendmessage").click(function() {
        var frienduserid = $('#friendid').val(); 
        sendMessage('Send Message::', function() {
        	sendMessageToFriend(frienduserid);
        });
        $("#messagesubject").bind("keyup",function(){
          $('#messagesubject').css("border-color", "none");
         });
        $("#messagebody").bind("focus",function(){ 
            $('#messagebody').css("border-color", "none");
        });
    });

 });
function sendMessage(msg,callbackYes) {
    var ret;
    jQuery.fancybox({
        'modal' : true,
        'content' : "<div style=\"margin:1px;width:240px;\"><div id=\"sendmessage1\"><h3>"+msg+"</h3></div><div style=\"text-align:right;margin-top:10px;\"><div class=\"successfullmessage\" id=\"successfullmessage\" style=\"display:none;\"><strong>Message succesfully sent.</strong></div><div id=\"inputwrapper\"><div class=\"messagesubject\"><label>Subject::</label><input type=\"text\" id=\"messagesubject\" size=\"13\"></div><div class=\"messagebody\"><label>Message::</label><textarea name=\"messagebody\" id=\"messagebody\" rows=\"4\" clos=\"3\"></textarea></div><button id=\"fancyconfirm_cancel\" style=\"margin:3px;padding:0px;\" class=\"button\" type=\"button\" ><span><span>Cancel</span></span></button><button id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\" class=\"button\" type=\"button\" ><span><span>Send</span></span></button></div></div></div>",
        'beforeShow' : function() {
            jQuery("#fancyconfirm_cancel").click(function() {
                $.fancybox.close();
            });
            jQuery("#fancyConfirm_ok").click(function() {
                if($('#messagesubject').val() == '') {
	                    $('#messagesubject').css("border-color","red"); 
	                    $('#messagesubject').focus(); 
	                    removeborder('messagesubject');
	                    return;
                    }
                if($('#messagebody').val() == '') {
	                    $('#messagebody').css("border-color","red");
	                    $('#messagebody').focus();
	                    removeborder('messagebody');
	                    return;
                    }
                $('#sendmessage1').hide();
                $('#inputwrapper').hide();
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
function removeborder(id){
    $("#"+id).bind("keyup",function(){ 
        $("#"+id).css("border-color", "");
       });
}
function sendMessageToFriend(userid) { 
	var messagebody = $('#messagebody').val();
	var messagesubject = $('#messagesubject').val();
    var url='index.php?option=com_myfriend&task=myfriendss.sendMessage';	 
	   $.ajax({
	        url: url,
	        type: "POST",
	        data: { userid: userid, messagesubject: messagesubject, messagebody: messagebody  },
	        dataType: "json",
	        timeout: 4000,
	        success: function(data) {	
	        	//$('#friends').submit();
	        }
	  });    
}

function facebookShare(sharelink){
	var sharelink =decodeURIComponent(sharelink);
	if(sharelink.charAt(0) === '/'){
		sharelink = sharelink.substr(1);
		} 
	sharelink = encodeURIComponent(sharelink);
    var facebooklink =encodeURIComponent('http://www.facebook.com/share.php?u=');
    url = decodeURIComponent(facebooklink);
    url = url+sharelink; 
    var width = 575;
	var height = 400;
	var left = 200;
	var top = 200;
	var windowFeatures = "width=" + width + ",height=" + height + ",status,resizable,left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
	window.open( url, "myWindow", windowFeatures );
}
function twitterPost(sharelink){
	var sharelink =decodeURIComponent(sharelink);
	if(sharelink.charAt(0) === '/'){
		sharelink = sharelink.substr(1);
		} 
	sharelink = encodeURIComponent(sharelink);
    var width  = 575,
        height = 400,
        left   = 200,
        top    = 200,
        url    = 'http://twitter.com/home?status='+sharelink,
        opts   = 'status=1' +
                 ',width='  + width  +
                 ',height=' + height +
                 ',top='    + top    +
                 ',left='   + left;

    window.open(url, 'twitte', opts);
}
</script>
<style type="text/css">
.profilefriend .frineddataclass label{width:200px !important; float:left; padding-top:10px;}
.profilefriend .frineddataclass span{ width:200px !important; float:left; padding-top:10px;}
.friendvisiblearea .sharesocial{}
</style>
<div class="profilefriend">
<div class="friendvisiblearea">
<div class="friendprofileimage">
          <?php if($this->items['profileimage'] =='')
          	$img = JURI::base().'images/default-portrait-icon.jpg';
          	else
          	$img = $this->items['profileimage'];
          ?>

<img src="<?php echo $img;?>" height="100" width="100"> 
<!-- checking if its a already friend, we are calling this also from view default view and also from friends view , sharebutton variable is in use when we shareed a friend profile.if a user open from FB/TWITTER the add a friend link will not see-->
  <?php if(empty($friend) && empty($sharebutton)) {?><span id="addfriend">
	<a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.checkfriendship&userid='.$this->userid.'&tmpl=component');?>"><?php echo JText::_('COM_FRIEND_ADD_FRIEND')?></a></span><?php }?> </div>
<!-- if a user is seeing friend profile then user can share his/her profile....... -->
<?php if(!empty($friend)) { 
    $outputValue .= '<a href="" title="Facebook Share!" class="facebookshare" onclick="facebookShare(/'. curPageURL() .'/); return;"  title="Click to send this page to Facebook!" ><img src="components/com_myfriend/assests/facebook.png" /></a>';
    $outputValue .= '<a href="" onclick="twitterPost(/'. curPageURL() .'/); return;"  title="Click to send this page to Twitter!" rel="nofollow"><img src="components/com_myfriend/assests/twitter.png" /></a>';
?>
 <div class="sharesocial">
<?php echo $outputValue;?>
 </div>
<!-- for sending message to friend... -->
<div class="sendmessage" id="sendmessage"><a href="#" class="sendmessagebutton"><span><?php echo JText::_('COM_MYFRIEND_SEND_MESSAGE')?></span></a></div> 
<?php }?>
</div>
<?php if(!empty($this->items['fname'])) {?>
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_NAME')?></label>
		<span><?php echo $this->items['fname'];?></span>
	</div> 
<?php }  if(!empty($this->items['email'])) {?>
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_EMAIL')?></label>
		<span><?php echo $this->items['email'];?></span>
	</div>
<?php }  if(!empty($this->items['gender'])) {?>
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_GENDER')?></label>
		<span><?php echo $this->items['gender'];?></span>
	</div>
<?php }  if(!empty($this->items['dob'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_DOB')?></label>
		<span><?php echo $this->items['dob'];?></span>
	</div>
<?php }  if(!empty($this->items['address1'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_ADDRESS')?></label>
		<span><?php echo $this->items['address1'];?></span>
	</div>
<?php }  if(!empty($this->items['state'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_STATE')?></label>
		<span><?php echo $this->items['state'];?></span>
	</div>
<?php }  if(!empty($this->items['postcode'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_POSTCODE')?></label>
		<span><?php echo $this->items['postcode'];?></span>
	</div>
<?php }  if(!empty($this->items['country'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_COUNTRY')?></label>
		<span><?php echo $this->findcountry($this->items['country']);?></span>
	</div>
<?php }  if(!empty($this->items['religion'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_RELIGION')?></label>
		<span><?php echo $this->findReligion($this->items['religion']);?></span>
	</div>
<?php }  if($this->items['localchurchname'][0] !='') {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_LOCALCHURCH')?></label>
		<span><?php foreach($this->items['localchurchname'] as $localchurch){ echo $localchurch;?><br><?php }?></span>
	</div>
<?php }  if($this->items['otherchurchname'][0] !='') {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_OTHERCHURCH')?></label>
		<span><?php foreach($this->items['otherchurchname'] as $otherchurch){ echo $otherchurch;?><br><?php }?></span>
	</div>
<?php }  if(!empty($this->items['interest'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_INTEREST')?></label>
		<span><?php echo $this->items['interest'];?></span>
	</div>
<?php }  if(!empty($this->items['favouritebiblequote'])) {?>	
	<div class="frineddataclass"><label><?php echo JText::_('COM_FRIEND_BIBLEQUOTE')?></label>
		<span><?php echo $this->items['favouritebiblequote'];?></span>
	</div>
<?php } ?>
<input type="hidden" name="userid" id="friendid" value="<?php echo $this->userid;?>" />
</div>