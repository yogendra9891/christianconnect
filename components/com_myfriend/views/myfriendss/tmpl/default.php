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
//$doc->addScript(JURI::base().'components/com_myfriend/assests/jquery.reveal.js');
//$doc->addStylesheet(JURI::base().'components/com_myfriend/assests/reveal.css');
//fancy box js and css files is added in template index file...
?>
<script type="text/javascript">/*<![CDATA[*/
$(document).ready(function(){
	$('#search-friend').submit(function(){
		if($('#searchfriend').val() == ''){ alert('please enter some name.');
		return false;}
	});
    $(".removefriend").click(function() {
        var userid = $(this).attr('name'); 
        fancyConfirm('Are you sure to unfriend this user?', function() {
        	removefriend(userid);
        });
    });
    //this is for pagination.....
   if($('.counter').text() == '')
   {   
	   $('.counter').addClass('-chk');
   }
});
function removefriend(userid) { 
    var url='index.php?option=com_myfriend&task=myfriendss.removefriendhip';	 
	   $.ajax({
	        url: url,
	        type: "POST",
	        data: { userid: userid },
	        dataType: "json",
	        timeout: 4000,
	        success: function(data) {	
	        	$('#friends').submit();
	        }
	  });      
}

function fancyConfirm(msg,callbackYes) {
    var ret;
    jQuery.fancybox({
        'modal' : true,
        'content' : "<div style=\"margin:1px;width:240px;\"><strong>"+msg+"</strong><div style=\"text-align:right;margin-top:10px;\"><button id=\"fancyconfirm_cancel\" style=\"margin:3px;padding:0px;\"><span><span>Cancel</span</span></button><button id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\"><span><span>Ok</span></span></button></div></div>",
        'beforeShow' : function() {
            jQuery("#fancyconfirm_cancel").click(function() {
                $.fancybox.close();
            });
            jQuery("#fancyConfirm_ok").click(function() {
                $.fancybox.close();
                callbackYes();
            });
        }
    });
}
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
.searchedfriendswrapper{width:450px;}
.searchedfriendswrapper ul li{list-style-type:none; display: inline; width:100px !important; float:left; padding: 20px !important;}

</style> 
<form id="search-friend" action="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.searchfriend'); ?>" method="post" class="form-validation">
	<label>Search Friend::</label>
	<input type="text" name="searchfriend" id="searchfriend">&nbsp;
	<input type="submit" value="submit" class="button"></input>
</form>
                                        
<form id="friends" action="<?php echo JRoute::_('index.php?option=com_myfriend&view=myfriendss'); ?>" method="post" class="form-validate">
	<div class="searchedfriendswrapper">
		<div><h3>Friends::</h3></div>
		<ul class="friends">
			<?php 
			 foreach($this->items as $result){
			  ?><li><a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewprofile&layout=friendprofile&userid='.$result->userid.'&friend=true&tmpl=component');?>" class="modal" rel="{handler: 'iframe'}" >
		         
		          <?php if($result->profileimage =='')
		          	$img = JURI::base().'images/default-portrait-icon.jpg';
		          	else
		          	$img = $result->profileimage;
		          ?>
				 	<img src="<?php echo $img?>" height="100" width="100"/> </a>
				 	<div class="friendname">
				 	 <a href="<?php echo JRoute::_('index.php?option=com_myfriend&task=myfriendss.viewprofile&layout=friendprofile&userid='.$result->userid.'&friend=true&tmpl=component');?>" class="modal" rel="{handler: 'iframe'}" >
				 		<?php echo $result->name?>
				 	 </a>
				 	</div>
				 	
				 	<div class="buttonnmewrapper">
				 	 <a id="removefriend" href="javascript:void(0);" class="removefriend" name="<?php echo $result->userid;?>"><?php echo JText::_('COM_MYFRIEND_REMOVE_FRIENDSHIP');?></a>
				 	</div>
				 	<input type="hidden" value=<?php echo $result->userid;?> id="removefriendid" />
			 	</li>
			<?php  } ?>
		</ul>
	</div>
  <div>
	<?php if(count($this->items)>0)
	echo $this->paginations->getListFooter(); else echo JText::_('COM_MYFRIEND_NO_RESULT_FOUND');?>
 </div>
	<?php echo JHtml::_('form.token'); ?>
</form>                                          