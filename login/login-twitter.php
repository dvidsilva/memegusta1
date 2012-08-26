<?php
require("twitter/twitteroauth.php");
require 'config/twconfig.php';
session_start();

$twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);
// Requesting authentication tokens, the parameter is the URL we will be redirected to
$request_token = $twitteroauth->getRequestToken('http://memegusta.com.co/m/login/getTwitterData.php');

// Saving them into the session

$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

// If everything goes well..
if ($twitteroauth->http_code == 200) {
    // Let's generate the URL and redirect
    $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
		  $_SESSION['id'] = $userdata['id'];
		  $_SESSION['oauth_id'] = $uid;
		  $_SESSION['username'] = $userdata['username'];
		  $_SESSION['email'] = $email;
		  $_SESSION['oauth_provider'] = $userdata['oauth_provider'];
		  $_SESSION['id'] = $userdata['id'];
		  $_SESSION['username'] = $userdata['username'];
		  $_SESSION['active'] = 'Y';			
		  $_SESSION['fname'] = $userdata['fname'];			
		  $_SESSION['img_url'] = $userdata['img_url'];
		  $_SESSION['usr_type'] = $userdata['usr_type_id'];
		  $_SESSION['status'] = $userdata['status'];
		  $_SESSION['age'] = '18';
    header('Location: home.php');
} else {
    // It's a bad idea to kill the script, but we've got to know when there's an error.
    die('Something wrong happened.');
}
?>
