<?php
/*
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";

/************************************************
  We create the client and set the simple API
  access key. If you comment out the call to
  setDeveloperKey, the request may still succeed
  using the anonymous quota.
 ************************************************/

$client = new Google_Client();

// OAuth 2.0 settings
//
// Go to the Google API Console, open your application's
// credentials page, and copy the client ID, client secret,
// redirect URI, and API key. Then paste them into the
// following code.
$client->setClientId('1042305458362-f821opoani97teknq4a8h9gtj49v96h3.apps.googleusercontent.com');
$client->setClientSecret('oFGBWkXTT9d7xyJbDeztrCEJ');
$client->setRedirectUri('postmessage'); /// <-------
$client->setAccessType("offline");

$client->addScope('https://www.googleapis.com/auth/calendar email profile');

if (isset($_GET['oauth'])) {
    // Start auth flow by redirecting to Google's auth server
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else if (isset($_GET['code'])) {
    // Receive auth code from Google, exchange it for an access token, and
    // redirect to your base URL
    echo json_encode($client->fetchAccessTokenWithAuthCode($_GET['code']));
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
//    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
} else if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    // You have an access token; use it to call the People API
    $client->setAccessToken($_SESSION['access_token']);
    $people_service = new Google_Service_PeopleService($client);
    // TODO: Use service object to request People data
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/?oauth';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>