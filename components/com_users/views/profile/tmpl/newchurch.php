<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Import CSS
$document = JFactory::getDocument();
//this below script file is added because this is seeing in IFRame and no jquery library is there..........
$document->addScript(JURI::base().'/components/com_users/js/jquery-1.7.1.min.js', 'text/javascript');
?>

<script type="text/javascript">

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
<script type="text/javascript">
$(document).ready( function() {
$('#admin-form').submit(function() { 
	var variable = true;
	if($('#jform_category').val() == '0')
	{	alert('Please select a church category');
		return false;
	}

	if($('#jform_country').val() == '0')
	{	alert('Please select a country');
		return false;
	}
	var profileimage1 = $('#jform_profileimage1').val();
	if(profileimage1 != ''){
	var ext =  profileimage1.match(/\.(.+)$/)[1]; 
	variable = extension(ext);}

	var profileimage2 = $('#jform_profileimage2').val();
	if(profileimage2 != '' && variable){
	var ext =  profileimage2.match(/\.(.+)$/)[1]; 
	variable = extension(ext);}

	var logo = $('#jform_logo').val();
	if(logo != '' && variable){
	var ext =  logo.match(/\.(.+)$/)[1]; 
	variable = extension(ext);}
	
	return variable;
 });
});

function extension(ext)
{
	var variable = true;
	if(ext == 'jpg' || ext == 'JPG' || ext == 'png' || ext == 'PNG' || ext == 'jpeg'|| ext == 'JPEG' || ext == 'bmp'){
		variable = true;
	 }
	else
	{  
		alert('please select an image type');
		variable =  false;
	} 
     return variable;
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_users&task=profile.churchsave'); ?>" method="post" enctype="multipart/form-data"  name="adminForm" id="admin-form" class="form-validate">
	
	<div class="test">
		<fieldset class="adminform">
			<legend><h2><?php echo JText::_('COM_USERS_CHURCH_NEW'); ?></h2></legend>
			<ul class="adminformlist" style="list-style: none outside !important; padding-top: 10px !important;">

     			<li><?php echo $this->churchform->getLabel('cname'); ?>
				<?php echo $this->churchform->getInput('cname'); ?></li>
				
				<li><?php echo $this->churchform->getLabel('category'); ?>
				<?php echo $this->churchform->getInput('category'); ?></li>
				
				<li><?php echo $this->churchform->getLabel('address1'); ?>
				<?php echo $this->churchform->getInput('address1'); ?></li>
				
				<li><?php echo $this->churchform->getLabel('address2'); ?>
				<?php echo $this->churchform->getInput('address2'); ?></li>
				
				<li><?php echo $this->churchform->getLabel('city'); ?>
				<?php echo $this->churchform->getInput('city'); ?></li>
				
				<li><?php echo $this->churchform->getLabel('state'); ?>
				<?php echo $this->churchform->getInput('state'); ?></li>
				
				<li><?php echo $this->churchform->getLabel('country'); ?>
				<?php echo $this->churchform->getInput('country'); ?></li>
				
				<li><?php echo $this->churchform->getLabel('postcode'); ?>
				<?php echo $this->churchform->getInput('postcode'); ?></li>

				<li><?php echo $this->churchform->getLabel('phone'); ?>
				<?php echo $this->churchform->getInput('phone'); ?></li>

			    <li><?php echo $this->churchform->getLabel('siteurl'); ?>
				<?php echo $this->churchform->getInput('siteurl'); ?></li>

				<li><?php echo $this->churchform->getLabel('profileimage1'); ?>
				<?php echo $this->churchform->getInput('profileimage1'); ?>
				</li>

				<li><?php echo $this->churchform->getLabel('profileimage2'); ?>
				<?php echo $this->churchform->getInput('profileimage2'); ?>
				</li>

				<li><?php echo $this->churchform->getLabel('logo'); ?>
				<?php echo $this->churchform->getInput('logo'); ?>
				</li>

			</ul>
		</fieldset>
	</div>
		<div>
			<button type="submit" class="validate"><span><span><?php echo JText::_('JSUBMIT'); ?></span></span></button>
			<?php echo JHtml::_('form.token'); ?>
		</div>

<div class="clr"></div>
</form>