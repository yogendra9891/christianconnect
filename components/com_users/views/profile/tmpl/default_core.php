<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

?>
<style type="text/css">
.profile .viewprofileimage{}
.extrainformation{padding-top: 10px;}
.extrainformation dd.localchurchs{padding-left: 154px !important;}
</style>
<fieldset id="users-profile-core">
	<legend>
		<?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
	</legend>
	<div class="viewprofileimage">
          <?php if($this->christiandata[0]->profileimage =='')
          	$img = JURI::base().'images/default-portrait-icon.jpg';
          	else
          	$img = $this->christiandata[0]->profileimage;
          ?>
			<img src="<?php echo $img; ?>" width="100" height="100">

</div>
<div class="extrainformation">
	<dl>

		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo $this->data->name; ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo htmlspecialchars($this->data->username); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USER_EMAIL'); ?>
		</dt>
		<dd>
			<?php echo htmlspecialchars($this->data->email1); ?>
		</dd>
       <?php if($this->christiandata[0]->city !=''){?>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USER_CITY'); ?>
		</dt>
		<dd>
			<?php echo $this->christiandata[0]->city; ?>
		</dd>
		<?php } if($this->christiandata[0]->state != ''){?>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USER_STATE'); ?>
		</dt>
		<dd>
			<?php echo $this->christiandata[0]->state; ?>
		</dd>
		<?php } if($this->christiandata[0]->country != ''){?>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USER_COUNTRY'); ?>
		</dt>
		<dd>
			<?php echo $this->countryname($this->christiandata[0]->country); ?>
		</dd>
		<?php } if($this->christiandata[0]->religion != ''){?>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USER_RELIGION'); ?>
		</dt>
		<dd>
			<?php echo $this->religionname($this->christiandata[0]->religion); ?>
		</dd>
		<?php } ?>
	<?php 
		for($i=0; $i<count($this->getUserchurch[0]->churchname); $i++) {if($i<1){?>
		<dt>
			<?php echo JText::_('COM_USERS_LOCAL_CHURCHS'); ?>
		</dt>	<?php }?>
		<dd class="localchurchs">	
		<?php echo $this->getUserchurch[0]->churchname[$i]; ?>
		</dd>
		<?php }
	  for($k=0; $k<count($this->getUserchurch[0]->otherchurchname); $k++) {if($k<1){?>
		<dt>
			<?php echo JText::_('COM_USERS_OTHER_CHURCHS'); ?>
		</dt>	<?php }?>
		<dd class="localchurchs">	
		<?php echo $this->getUserchurch[0]->otherchurchname[$k]; ?>
		</dd>
		<?php }?>
	  		
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</dt>
		<dd>
			<?php echo JHtml::_('date', $this->data->registerDate); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
		</dt>

		<?php if ($this->data->lastvisitDate != '0000-00-00 00:00:00'){?>
			<dd>
				<?php echo JHtml::_('date', $this->data->lastvisitDate); ?>
			</dd>
		<?php }
		else {?>
			<dd>
				<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</dd>
		<?php } ?>

	</dl>
	</div>
</fieldset>
