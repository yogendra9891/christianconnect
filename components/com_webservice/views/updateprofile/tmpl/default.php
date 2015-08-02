<?php
/**
 * @version     1.0.0
 * @package     com_craigslist
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      yogendra singh <yogendra.singh@daffodilsw.com> - http://
 */

//echo "dds"; exit;
// no direct access
defined('_JEXEC') or die;
?>
<form action="webservice.php?action=UpdateProfile" name="userform" method="post" enctype='multipart/form-data' id="userform">
image:<input name="profilePic" type="file" size="30" ></br>
profiledata:<input type="text" name="data" size="60" value='{"sessionId":"4go2ghqj3fe5s61hj991h3aei2","userProfileObject":{}}'></br>
<!--name:<input type="text" name="name" size="20" value="amit">-->
<!--contactno:<input type="text" name="contactno" size="20" value="123456789">-->
<input type="submit" name="submit">
</form>
