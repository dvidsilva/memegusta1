<?php

require 'dbconfig.php';

class User {

    function checkUser($uid, $oauth_provider, $username,$email,$twitter_otoken,$twitter_otoken_secret) 
	{
        $query = mysql_query("SELECT * FROM `usr` WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'") or die(mysql_error());
        $result = mysql_fetch_array($query);
        if (!empty($result)) {
            # User is already present
        } else {
		$q = "INSERT INTO `usr` (oauth_provider, oauth_uid,username, fname,email,twitter_oauth_token,twitter_oauth_token_secret,img_url) 
			VALUES ('$oauth_provider', $uid, '$username','$username','$email','','','memegusta.1.png')";    
		#echo $q; die();
		#user not present. Insert a new Record
		$query = mysql_query($q) or die(mysql_error());
		
            $query = mysql_query("SELECT * FROM `usr` WHERE oauth_uid = '$uid' and oauth_provider = '$oauth_provider'");
            $result = mysql_fetch_array($query);
            return $result;
        }
        return $result;
    }

    

}

?>
