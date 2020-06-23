<?php

session_start(); //session start

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/report/lib/database.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/report/lib/libraries/Google/autoload.php');

//handle logout request
if (isset($_GET['logout'])) {
  unset($_SESSION['access_token']);
  unset($_SESSION['omics_user']);
  header('Location: https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=' . REDIRECT_URI_OMICS);
}

/************************************************
  Make an API request on behalf of a user using 
  a valid OAuth 2.0 token for the user. 
  Client id and secret below were defined in the
  config file. Get it from Google project console
  if don't have one.
 ************************************************/

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI_OMICS);
$client->addScope("email");
$client->addScope("profile");

/************************************************
  When we create the service here, we pass the
  client to it. The client then queries the service
  for the required scopes, and uses that when
  generating the authentication URL later.
 ************************************************/

$service = new Google_Service_Oauth2($client);

/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
**************************************************/

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  header('Location: ' . filter_var(REDIRECT_URI_OMICS, FILTER_SANITIZE_URL));
  exit;
}

/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  if($client->isAccessTokenExpired()) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
  }
} else {
  $authUrl = $client->createAuthUrl();
}

/************************************************
  Request the user info and check if it is in the 
  valid users white list defined in the config.php
**************************************************/

if (!isset($authUrl)){
  $user = $service->userinfo->get(); //get user info
  if (userWhitelist::where('email', '=', $user['email'])->get()->count()) {
    $_SESSION['omics_user'] = $user['email']; //create session
  } else {
    unset($_SESSION['omics_user']);
  }
}

require_once('template.php');

?>

