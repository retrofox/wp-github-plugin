<?php
header('Content-Type: application/jsonrequest');

/**
 * Github constants
 */

define('gh_host', 'http://www.github.com/');
define('gh_api_host', 'https://api.github.com/');
define('gh_plugin_path', $siteurl.'/wp-content/plugins/wp-github-plugin');

/**
 * retieve data from github
 */

function getGithubData ($url) {
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  return $response;
}

/**
 * getContributors function
 */

function getContributors ($user, $repo) {
  $url = gh_api_host."repos/".$user."/".$repo."/contributors";

  $contributors = json_decode(getGithubData($url, true));

  $response = array();

  foreach ($contributors as $k => $user) {
    $fullUser = getGithubData($user->url);
    array_push($response, array(
        'user' => $user
      , 'full' => json_decode($fullUser)
    ));
  };

  return $response;
}

/**
 * getGuthubData
 * retrieve data through github API
 */

function getData ($user, $repo, $type) {

  switch ($type) {
    case "contributors":
      return json_encode(getContributors($user, $repo));
    break;
  }
}

$user = $_GET["user"];
$repo = $_GET["repo"];
$type = $_GET["type"];

echo getData($user, $repo, $type);
