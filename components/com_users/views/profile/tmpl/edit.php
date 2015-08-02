<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load( 'plg_user_profile', JPATH_ADMINISTRATOR );
$doc = JFactory::getDocument();

?>
<script type="text/javascript">/*<![CDATA[*/

function readURL(input)
{     $('#clear').css('display', 'block');
      if (input.files && input.files[0])
              {
                    var reader = new FileReader();
                   reader.onload = function (e)
                                          {
                                                $('#blah')
                                                .attr('src',e.target.result)
                                                .css('display', 'block')
                                                .width(100)
                                                .height(100);
                                          };
                   reader.readAsDataURL(input.files[0]);
                   }
}
function clearimage(imagename)
{
	$('#blah').attr('src','');
	$('#blah').css('display', 'none');	
	$('#filename').val('');
	$('#clear').css('display', 'none');	
	$('#jform_profileimage').val(imagename);
}
$(document).ready(function(){
	
$('#member-profile').submit(function() { 
	var variable = true;
	var dob = $('#jform_dob').val(); 
	var isValid = dob.match(/^\d\d\d\d?\-\d\d?\-\d\d$/);
	if($('#jform_country').val() == '0')
	{	alert('Please select a country');
		return false;
	}
	if(!isValid)
	{  
		alert('Please enter dob yyyy-mm-dd');
		return false;
	}
	else
	{
		var today = new Date(); 
		var DOB = Date.parse(dob); 
		var age = 18;
		today.setFullYear(today.getFullYear() - age); 
		if ((today - DOB) < 0){
			alert('sorry, you are a teenager.');
	    	return false;
		}
	}  
	
    var ext = $('#filename').val().match(/\.(.+)$/)[1]; 
	if(ext == 'jpg' || ext == 'JPG' || ext == 'png' || ext == 'PNG' || ext == 'jpeg'|| ext == 'JPEG' || ext == 'bmp' || ext == 'gif' || ext == 'GIF'){
		variable = true;
     }
	else
	{   alert('please select an image type');
		variable =  false;
		
	} 

	return variable;
});
$('#otherreligion').click(function(){
	$('#religionother').css('display', 'block');
	
  });
/*
 * code for adding a new field in local church..
 */
var scntDiv = $('#localchurchs_parent');
var i = $('#localchurchs_parent p').size() + 1;

$('#addScnt').live('click', function() {
        $('<p><label>Local Church</label><input type="text" class="church" id="p_scnt'+i+'" size="30" name="p_scnt[]" value="" /></p>').appendTo(scntDiv);
        i++;
		$('.church').focus(function(e3){
			var  u = $(this).attr("id"); //function calling of the srcipt.js file...in js folder..
			searchlocalchurch(u);
	    });
        return false;
});
/*
$('.remScnt').live('click', function() { 
		var removetagid = $(this).attr('id');
		var localchurchstring = $('#localchurch').val(); 
        if( i > 2 ) {
                $(this).parents('p').remove();
                i--;
        }
        var stt = localchurchstring.toString();
        var splitstring = localchurchstring.split(','); 
        var newid = new  array();
        for(var j=0; j< splitstring.length; j++)
        {
            if(splitstring[j] != removetagid) newid= splitstring[j];
        }
        $('#localchurch').val(newid);
        return false;
}); */
/*
 * code end for adding a new field..
 */
/*
 * code for adding a new field in other church..
 */
var scntotherDiv = $('#otherchurchs_parent');
var k = $('#otherchurchs_parent p').size() + 1;

$('#otheraddScnt').live('click', function() {
        $('<p><label>Other Church</label><input type="text" class="otherchurch" id="p_other_'+k+'" size="30" name="p_other[]" value="" /></p>').appendTo(scntotherDiv);
        k++;
		$('.otherchurch').focus(function(e4){
			var  u = $(this).attr("id"); //function calling of the srcipt.js file...in js folder..
			searchotherchurch(u);
	    });
        return false;
});

});
</script>
<style type="text/css">

.test ul li{
	padding-top:10px !important;
}
.test ul li label{
	width: 150px;
	float: left;
}
</style>
<div class="profile-edit<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<?php 
	// check for the profile image getting may be user login from FB/TW or simple user..if FB/TW coming from FB/TW..
//	if($this->getUsertype[0]->type == "FB" || $this->getUsertype[0]->type == "TW")
//	{
//		$src = $this->christiandata[0]->profileimage; 
//	}
//	else
//	{
//		$src = JURI::root().'components/com_users/images/profileimage/thumbs/'.$this->christiandata[0]->profileimage;
//	}
?>
<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
<div class="test">
			<ul class="adminformlist" style="list-style: none outside !important; padding-top: 10px !important;">

				<li>
	          <?php if($this->christiandata[0]->profileimage =='')
	          	$img = JURI::base().'images/default-portrait-icon.jpg';
	          	else
	          	$img = $this->christiandata[0]->profileimage;
	          ?>

				<div id="profileimage_button"><img src="<?php echo $img; ?>" width="100" height="100">
					</div>
				</li>
				<li><label>Change Profile Image</label><input type='file' id="filename" onchange="readURL(this);" name="changeprofileimage"/>
				
				</li>
				<li><img id="blah" src="#" style="display:none;"/> <span><input type="button" value="clear" id ="clear" onclick="clearimage('<?php echo $this->christiandata[0]->profileimage ?>');" style="display:none;"></span></li>
				<li><?php echo $this->form->getInput('profileimage','',$this->christiandata[0]->profileimage); ?></li>
				
				<li><?php echo $this->form->getInput('id','',$this->data->id); ?></li>
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name','',$this->data->name); ?>&nbsp;&nbsp;&nbsp;<span> <?php //echo $this->dropdown();?></span></li>

				<li><?php echo $this->form->getLabel('email1'); ?>
				<?php echo $this->form->getInput('email1','',$this->data->email1); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->email, 'email');?></span></li>
				
				<li><?php echo $this->form->getLabel('email2'); ?>
				<?php echo $this->form->getInput('email2','',$this->data->email2); ?></li>

				<li><?php echo $this->form->getLabel('fname'); ?>
				<?php echo $this->form->getInput('fname','',$this->christiandata[0]->fname); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->fname, 'fname');?></span></li>

				<li><?php echo $this->form->getLabel('lname'); ?>
				<?php echo $this->form->getInput('lname','',$this->christiandata[0]->lname); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->lname, 'lname');?></span></li>

				<li><?php echo $this->form->getLabel('dob'); ?>
				<?php echo $this->form->getInput('dob','',$this->christiandata[0]->dob); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->dob, 'dob');?></span></li>

				<li><?php echo $this->form->getLabel('gender'); ?>
				<?php echo $this->form->getInput('gender','',$this->christiandata[0]->gender); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->gender, 'gender');?></span></li>

				<li><?php echo $this->form->getLabel('address1'); ?>
				<?php echo $this->form->getInput('address1','',$this->christiandata[0]->address1); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->address1, 'address1');?></span></li>

				<li><?php echo $this->form->getLabel('address2'); ?>
				<?php echo $this->form->getInput('address2','',$this->christiandata[0]->address2); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->address2, 'address2');?></span></li>

				<li><?php echo $this->form->getLabel('city'); ?>
				<?php echo $this->form->getInput('city','',$this->christiandata[0]->city); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->city, 'city');?></span></li>

				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state','',$this->christiandata[0]->state); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->state, 'state');?></span></li>

				<li><?php echo $this->form->getLabel('country'); ?>
				<?php echo $this->form->getInput('country','',$this->christiandata[0]->country); ?>&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->country, 'country');?></span></li>

				<li><?php echo $this->form->getLabel('postcode'); ?>
				<?php echo $this->form->getInput('postcode','',$this->christiandata[0]->postcode); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->postcode, 'postcode');?></span></li>

				<li><?php echo $this->form->getLabel('religion'); ?>
				<?php echo $this->form->getInput('religion','',$this->religionname($this->christiandata[0]->religion)); ?>&nbsp;&nbsp;
				<span><input type="button" id="otherreligion" value="Others"></span>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->religion, 'religion');?></span></li>
				<li id="religionother" style="display:none;"><?php echo $this->form->getLabel('otherreligion'); ?>
				<?php echo $this->form->getInput('otherreligion',''); ?></li>

<!--  start work for local church.. -->
				<li><label><?php echo JText::_('COM_USERS_LOCAL_CHURCH');?></label>
				<div id="localchurchs_parent">
				    <p>
				      <input type="text" class="church" id="p_scnt_1" size="30" name="p_scnt[]" value=""/><a href="#" id="addScnt"><span>Add Another</span></a>
				      <a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.newchurchform&layout=newchurch&tmpl=component');?>" class="modal" rel="{handler: 'iframe'}"><span><?php echo JText::_('COM_USERS_LOCAL_CHURCH_NEW');?></span></a>
				      &nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->localchurch, 'localchurch');?></span>
				    </p>
				    <span id="firstchurch" style="display:none;"><?php echo JText::_('COM_USERS_LOCAL_CHURCH_FIRST_FILL');?></span>
				    <?php 
				     for($i=0; $i<count($this->getUserchurch[0]->churchname); $i++)
				     { if($i==0) {?><script> $('#p_scnt_1').val("<?php echo @$this->getUserchurch[0]->churchname[$i];?>");</script> <?php }else{ if(!empty($this->getUserchurch[0]->churchname[$i])) {?>
						<p><label><?php echo JText::_('COM_USERS_LOCAL_CHURCH');?></label><input type="text" class="church" id="p_scnt_<?php echo $i+1;?>" size="30" name="p_scnt[]" value="<?php echo @$this->getUserchurch[0]->churchname[$i]; ?>" /></p>
				     <?php      	
				     } } }
				    ?>
				</div>
				
				</li>
				<li><input type="hidden" name="localchurch" id="localchurch" value="<?php echo $this->getUserchurch[0]->localchurch;?>" />
		    	<?php echo $this->form->getInput('userchurchid','',$this->getUserchurch[0]->id); ?>
				</li>
<!-- end work for local church.. -->

<!--  start work for other churchs.. -->
				<li><label><?php echo JText::_('COM_USERS_OTHER_CHURCH');?></label>
				<div id="otherchurchs_parent">
				    <p>
				      <input type="text" class="otherchurch" id="p_other_1" size="30" name="p_other[]" value=""/><a href="#" id="otheraddScnt"><span><?php echo JText::_('COM_USERS_OTHER_ANOTHER_CHURCH');?></span></a>
				      &nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->otherchurch, 'otherchurch');?></span>
				    </p>
				    <span id="firstotherchurch" style="display:none;"><?php echo JText::_('COM_USERS_OTHER_CHURCH_FIRST_FILL');?></span>
				    <?php 
				     for($k=0; $k<count($this->getUserchurch[0]->otherchurchname); $k++)
				     { if($k==0) {?><script> $('#p_other_1').val('<?php echo @$this->getUserchurch[0]->otherchurchname[$k];?>');</script> <?php }else{ if(!empty($this->getUserchurch[0]->otherchurchname[$k])) {?>
						<p><label><?php echo JText::_('COM_USERS_OTHER_CHURCH');?></label><input type="text" class="otherchurch" id="p_other_<?php echo $k+1;?>" size="30" name="p_other[]" value="<?php echo $this->getUserchurch[0]->otherchurchname[$k]; ?>" /></p>
				     <?php      	
				     } } }
				    ?>
				</div>
				
				</li>
				<li><input type="hidden" name="otherchurch" id="otherchurch" value="<?php echo $this->getUserchurch[0]->otherchurch;?>" />
				</li>
<!-- end work for other churchs.. -->
			
				<li><?php echo $this->form->getLabel('interest'); ?>
				<?php echo $this->form->getInput('interest','',$this->christiandata[0]->interest); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->interest, 'interest');?></span></li>

				<li><?php echo $this->form->getLabel('favouritebiblequote'); ?>
				<?php echo $this->form->getInput('favouritebiblequote','',$this->christiandata[0]->favouritebiblequote); ?>&nbsp;&nbsp;&nbsp;<span> <?php echo $this->dropdown($this->accessibilty[0]->favouritebiblequote, 'favouritebiblequote');?></span></li>

				
				<li><?php echo $this->form->getInput('userprofileid','',$this->christiandata[0]->id); ?></li>
				<li><?php echo $this->form->getInput('userprofileaccessid','',$this->accessibilty[0]->id); ?></li>
				<li><?php echo $this->form->getInput('religionid','',$this->christiandata[0]->religion); ?></li>
				
</div>
		<div class="submitarea">
		<button type="submit" class="validate formprofileclass"><span><span><?php echo JText::_('JSUBMIT'); ?></span></span></button>
			<?php //echo JText::_('COM_USERS_OR'); ?>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>" title="<?php echo JText::_('JCANCEL'); ?>" class="submitcancel"><span><?php echo JText::_('JCANCEL'); ?></span></a>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
