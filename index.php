<?php

require "vendor/autoload.php";

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSDKException;
use Facebook\GraphLocation;
use Facebook\GraphUser;

$APPID = 'Yout App ID';
$SECRET = 'Your App Secret';

session_start();

FacebookSession::setDefaultApplication($APPID, $SECRET);

// facebook.local will be your website url and will be used to redirect.

$helper = new FacebookRedirectLoginHelper("http://facebook.local/");

try {
    if(!isset($_SESSION['access_token'])){
        $session = $helper->getSessionFromRedirect();
        $_SESSION['access_token'] = $session;
        
    }
    if(isset($_GET['code'])){
        header("location:index.php");
    }
    $session = $_SESSION['access_token'];
        
} catch (FacebookRequestException $ex) {
      die("<h1>Error : Facebook returns an error.</h1>");
} catch (\Exception $ex) {
      die("<h1>Error : When validation fails or other local issues</h1>");
}

if (isset($session)) {
    
    $request = new FacebookRequest($session, 'GET', '/me');
    $response = $request->execute();
    $me = $response->getGraphObject(GraphUser::className());
    echo 'Welcome, '.$me->getProperty('name').'<br/>';
    echo 'About You -  '.$me->getProperty('bio');
   
   // Upload a photo to users wall with custome message

    try{
     $response = (new FacebookRequest(
      $session, 'POST', '/me/photos', array(
        'source' => new CURLFile('sc.png', 'image/png'),
        'message' => 'Your Custom Message'
      )
    ))->execute()->getGraphObject();


    echo "Posted with id: " . $response->getProperty('id');

  } catch(FacebookRequestException $e) {

    echo "Exception occured, code: " . $e->getCode();
    echo " with message: " . $e->getMessage();

  }   
    echo '<br/> <a href="logout.php">Logout</a>';
    
} else {
    echo '<a href="' . $helper->getLoginUrl(array('scope'=> 'user_photos,public_profile,read_friendlists,user_activities,user_friends,user_about_me,publish_actions')) . '">Login with Facebook</a>';
}
?>