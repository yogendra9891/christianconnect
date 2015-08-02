<?php
/**
 * JLV Facebook Login Module
 * @version 2.5.6
 * @author Le Xuan Thanh
 * @website http://joomla.name.vn
 * @Copyright (C) 2010 - 2012 joomla.name.vn. All Rights Reserved.
 * @license GNU General Public License version 2, see LICENSE.txt or http://www.gnu.org/licenses/gpl-2.0.html
 * fanpage: https://www.facebook.com/jlvextension
 * youtube: http://www.youtube.com/jlvextension
 * twitter: https://twitter.com/jlvextension
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
?>



<?php if ($type == 'logout') : ?>
<form style="clear: both; " action="" method="post" id="login-form">
<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting"style="float: left; line-height: 30px; padding:0 !important; margin: 0 10px 0 0;">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_JLVFACEBOOKLOGIN_HINAME', htmlspecialchars($user->get('name')));
	} else : {
		echo JText::sprintf('MOD_JLVFACEBOOKLOGIN_HINAME', htmlspecialchars($user->get('username')));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="logout-button"style="float: left; padding:0 !important; margin: 0;">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php else : ?>
<form style="clear: both; " action="" method="post" id="login-form" >
	<?php if ($params->get('pretext')): ?>
		<div class="pretext">
		<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<?php if($loginform==1) : ?>
		<fieldset class="userdata" style="float: left; padding:0 !important; margin: 0;">
		<p id="form-login-username" style="float: left; padding:0 !important; margin-right: 5px;">
			<label for="modlgn-username"><?php echo JText::_('MOD_JLVFACEBOOKLOGIN_VALUE_USERNAME') ?></label>
			<input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
		</p>
		<p id="form-login-password" style="float: left; padding:0 !important; margin-right: 5px;">
			<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
			<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
		</p>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<p id="form-login-remember" style="float: left; padding:0 !important;">
			<label for="modlgn-remember"><?php echo JText::_('MOD_JLVFACEBOOKLOGIN_REMEMBER_ME') ?></label>
			<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		</p>
		<?php endif; ?>
		<input type="submit" name="Submit" style="float: left; margin-left: 10px;margin-right: 10px; " class="button" value="<?php echo JText::_('JLOGIN') ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	<?php endif; ?>
	<ul style="float: left; height: auto; line-height: 30px; list-style-type:none;">
		<?php if($forgot==1) : ?>
		<li style="float: left; margin-right: 10px;">
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('MOD_JLVFACEBOOKLOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<?php endif; ?>
		<?php if($forgotuser==1) : ?>
		<li style="float: left; margin-right: 10px;">
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('MOD_JLVFACEBOOKLOGIN_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php endif; ?>
		<?php if($create==1) : ?>
			<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<li style="float: left; margin-right: 10px;">
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo JText::_('MOD_JLVFACEBOOKLOGIN_REGISTER'); ?></a>
			</li>
			<?php endif; ?>
		<?php endif; ?>
		<li style="float: left">
			<a style="float: left" href="#" onclick="fblogin();return false;" class="fbloginbutton">
				<?php if($facebookimage!='-1') : ?>
					<img border="0" src="<?php echo JURI::base();?>modules/mod_jlv_facebooklogin/customimages/<?php echo $facebookimage;?>" />
				<?php else : ?>
					<img border="0" src="<?php echo JURI::base();?>modules/mod_jlv_facebooklogin/customimages/Small_and_Long_200x24.png" />
				<?php endif; ?>
			</a>
			<script>
			  function fblogin() {
				FB.login(function(response) {
					<?php if($loading==1) { ?>
						if (response.authResponse) {
								window.addEvent('domready', function(){
									if( $('facebook-login') ){
										SqueezeBox.initialize();
										SqueezeBox.open( $('facebook-login'), {
											handler: 'adopt',
											shadow: true,
											overlayOpacity: 0.5,
											size: {x: <?php echo $modalwidth; ?>, y: <?php echo $modalheight; ?>},
											onOpen: function(){
												$('facebook-login').setStyle('display', 'block');
											}
										});
									}
								});
							window.location.href=document.URL+'?fb=login&fb=login';
						}
					<?php } ?>
				}, {scope:'email'});
			  }
			</script>
		</li>
	</ul>
	<?php if ($params->get('posttext')): ?>
		<div style="clear:both;" class="clrfloat"></div>
		<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
	<?php if($loading==1) { ?>
	<div id="facebook-login" style="display:none; text-align:center">
		<?php echo $loading_msg; ?>
	</div>
	<?php } ?>
</form>

<?php endif; ?>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
	FB.init({
	  appId      : '<?php echo $appId; ?>',
	  status     : true,
	  cookie     : true,
	  xfbml      : true
	});
  };
  (function(d){
	 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	 if (d.getElementById(id)) {return;}
	 js = d.createElement('script'); js.id = id; js.async = true;
	 js.src = "//connect.facebook.net/en_US/all.js";
	 ref.parentNode.insertBefore(js, ref);
   }(document));
</script>
