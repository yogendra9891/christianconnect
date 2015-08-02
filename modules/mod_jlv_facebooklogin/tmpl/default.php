<?php
/**
 * @author JLV Extension Team
 * @copyright jlvextension.com
 * @link http://jlvextension.com
 * @package JLV Facebook Login
 * @version 1.0.3
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
?>
<?php if ($type == 'logout') : ?>
<form action="" method="post" id="login-form">
<?php if ($params->get('greeting')) : ?><!--
	<div class="login-greeting">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_JLV_FACEBOOKLOGIN_HINAME', htmlspecialchars($user->get('name')));
	} else : {
		echo JText::sprintf('MOD_JLV_FACEBOOKLOGIN_HINAME', htmlspecialchars($user->get('username')));
	} endif; ?>
	</div>-->
<?php endif; ?><!--
	<div class="logout-button">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>-->
</form>
<?php else : ?>
<form action="" method="post" id="login-form" >
	<?php if ($params->get('pretext')): ?>
		<div class="pretext">
		<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<?php if($loginform==1) : ?>
		<fieldset class="userdata">
		<p id="form-login-username">
			<label for="modlgn-username"><?php echo JText::_('MOD_JLV_FACEBOOKLOGIN_VALUE_USERNAME') ?></label>
			<input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
		</p>
		<p id="form-login-password">
			<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
			<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
		</p>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<p id="form-login-remember">
			<label for="modlgn-remember"><?php echo JText::_('MOD_JLV_FACEBOOKLOGIN_REMEMBER_ME') ?></label>
			<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		</p>
		<?php endif; ?>
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	<?php endif; ?>
	<ul>
		<?php if($forgot==1) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('MOD_JLV_FACEBOOKLOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<?php endif; ?>
		<?php if($forgotuser==1) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('MOD_JLV_FACEBOOKLOGIN_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php endif; ?>
		<?php if($create==1) : ?>
			<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo JText::_('MOD_JLV_FACEBOOKLOGIN_REGISTER'); ?></a>
			</li>
			<?php endif; ?>
		<?php endif; ?>
		<li>
			<a href="#" onclick="fblogin();return false;" class="fbloginbutton">
				<?php if($facebookimage!='-1') : ?>
					<img border="0" src="<?php echo JURI::base();?>modules/mod_jlv_facebooklogin/customimages/<?php echo $facebookimage;?>" />
				<?php else : ?>
					<img border="0" src="<?php echo JURI::base();?>modules/mod_jlv_facebooklogin/customimages/Small_and_Long_200x24.png" />
				<?php endif; ?>
			</a>
			<script>
			  //your fb login function
			  function fblogin() {
				FB.login(function(response) {
					<?php if($loading==1) { ?>
						if (response.authResponse) {
								//joomla squeezebox message until the user creation/login take places.
									jQuery.noConflict();
									jQuery(document).ready(function() {
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
							
							
							/*window.location.href='<?php echo JURI::base().'?fb=login'; ?>';*/
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
	  appId      : '<?php echo $appId; ?>', // App ID
	  status     : true, // check login status
	  cookie     : true, // enable cookies to allow the server to access the session
	  xfbml      : true  // parse XFBML
	});
  };
  // Load the SDK Asynchronously
  (function(d){
	 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	 if (d.getElementById(id)) {return;}
	 js = d.createElement('script'); js.id = id; js.async = true;
	 js.src = "//connect.facebook.net/en_US/all.js";
	 ref.parentNode.insertBefore(js, ref);
   }(document));
</script>