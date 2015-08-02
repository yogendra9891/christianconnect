<?php
/**
 * Social Login
 *
 * @version     1.0
 * @author        Yogendra, Singh, Joomline
 * @copyright    © 2012. All rights reserved.
 * @license     GNU/GPL v.3 or later.
 */

// No direct access to this file
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
$(document).ready(function(){
	var sloginid = <?php echo $this->sloginid; ?>;
	$('#getverifiedcode').click(function(){
		var email = $('#email_verification').val();
		$('#emailverify').val(email);
		if(email.length)
		{		
			 var url='index.php?option=com_slogin&task=generateEmailCode';
	    	   $.ajax({
			        url: url,
			        type: "POST",
			        data: { email: email, sloginid: sloginid },
			        dataType: "json",
			        timeout: 4000,
			        success: function(data) {	
			        }
			        });
	    	   $('#codesent').css({'display': 'block', 'color': 'red'});
		}else{alert('please enter email');}
	});
	$('#anotherverifiedcode').click(function(){
		var email = $('#email_verification').val();
		if(email.length)
		{		
			 var url='index.php?option=com_slogin&task=generateEmailCode';
	    	   $.ajax({
			        url: url,
			        type: "POST",
			        data: { email: email, sloginid: sloginid },
			        dataType: "json",
			        timeout: 4000,
			        success: function(data) {	
			        },
			        error: function() {
			            
			        }
			  });
			
		}
	});
$('#email-verifiedform').submit(function() { 
        if(($('#emailverify').val() != $('#email_verification').val())){alert('You have changed the email'); return false;}
		if(!($('#verification_code').val().length))
		{  alert('please enter code');
			return false;
		}
	if(!$(this).attr('validated'))
    {
	var url1='index.php?option=com_slogin&task=codeverification&format=raw';
		 var slogin = $('#slogin').val();
		 var verifiedcode = $('#verification_code').val();
		 var result = false;
		 var email = $('#email_verification').val();
	if(verifiedcode.length != 0){ 
 	   $.ajax({
		        url: url1,
		        type: "POST",
		        data: { email: email, sloginid: sloginid, code: verifiedcode },
		        dataType: "json",
		        timeout: 4000,
		        success: function(data) { 
			        	if(data){
				        		//$('#email-verifiedform').attr('validated',true);
				        		//$('#email-verifiedform').submit();
			        		  emailcheck(email);
				        	 }
			        	else{ alert('you have entered wrong verification code');
			        	}
		        }
		  }); }
	    return false;  
    }
	  return true;
	});
});

function emailcheck(email){
	if(email.length != 0){
		var url2='index.php?option=com_slogin&task=emailcheckunique&format=raw';
	 	   $.ajax({
		        url: url2,
		        type: "POST",
		        data: { email: email},
		        dataType: "json",
		        timeout: 4000,
		        success: function(data) { 
			        	if(data){ alert('you have already registered with this email, please login with those credentials.');
				        	 }
			        	else{ 
			        		$('#email-verifiedform').attr('validated',true);
			        		$('#email-verifiedform').submit();
			        	}
		        }
		  });
		} 
}
</script>
<style type="text/css">
 .verifiedbox label{width:100px; float:left;}
</style>
<div class="email_verification_form">

<label id="codesent" style="display:none;"><?php echo JText::_('COM_SLOGIN_CODE_SENT'); echo ' '.JText::_('COM_SLOGIN_CHECK_MAIL');?> </label>
<div id="againverified" style="display:none;"><?php echo JText::_('COM_SLOGIN_AGAIN_CODE_SENT');?>
<a href="#" id="anotherverifiedcode"><?php echo JText::_('COM_SLOGIN_VERIFICATION_CODE_FIND');?></a></div>
</div>

<form name="email-verifiedform" id="email-verifiedform" action="<?php echo JRoute::_('index.php?option=com_slogin&task=emailverification&oauth_verifier='.$this->oauth_verifier); ?>" method="post">
	<label><h1><?php echo JText::_('COM_SLOGIN_EMAIL_ENTER_AND_CODE');?></h2></label>
	<div class="verifiedbox" id="verifiedbox">
	<label><?php echo JText::_('COM_SLOGIN_EMAIL_ENTER');?></label>
	<input type="text" name="email_verification" id="email_verification"/>
	<a href="#" id="getverifiedcode"><?php echo JText::_('COM_SLOGIN_VERIFICATION_CODE_FIND');?></a>
	<br><br>	
	<label><?php echo JText::_('COM_SLOGIN__VERIFICATION_CODE');?></label><input type="text" name="verification_code" id ="verification_code" />&nbsp;&nbsp;

	<input type="hidden" name="slogin" id="slogin" value="<?php echo $this->sloginid;?>"></input>
	<input type="hidden" name="oauth_verifier" id="oauth_verifier" value="<?php echo $this->oauth_verifier;?>">
	<input type="hidden" name="emailverify" id="emailverify" value="">
	<input type="submit" value="Submit"></input>
	
	</div>
	 <?php echo JHtml::_('form.token'); ?>
</form>