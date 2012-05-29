<?php
header('Content-Type: application/jsonrequest');

/**
 * Github constants
 */

define('gh_host', 'http://www.github.com/');
define('gh_api_host', 'https://api.github.com/');
define('gh_plugin_path', $siteurl.'/wp-content/plugins/wp-github-plugin');

define('wp_uploads', getcwd().'/../../uploads/');
define('wp_tmp_folder', getcwd().'/../../uploads/wp-github-plugin/');

/**
 * getGithubHeaders
 */

function getGithubHeaders ($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_HEADER, TRUE); 
  curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $head = curl_exec($ch);

  // convert headers to array
  $head = split("\n", $head);
  $headers = array();

  foreach($head as $k => $val) {
    if (strlen($val) > 0) {
      $pair = split(':', $val);
      if (count($pair) > 1) {
        $headers[$pair[0]] = $pair[1];
      } else {
        $headers[$k] = $val;
      }
    }
  }

  curl_close($ch);
  return $headers;
}

/**
 * getGithubEtag
 */

function getGithubETag ($url) {
  $headers = getGithubHeaders($url);
  return $headers['ETag'];
}

/**
 * retieve data from github
 */

function getGithubData ($url, $id) {
  // retrieve data form Github
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  return $response;
}

function loadLocalETag ($filename) {
  $handler = fopen($filename, "r");
  $eTag = fread($handler, filesize($filename));
  fclose($handler);

  return $eTag;
}

function saveLocalETag ($id, $etag) {
  // save eTag local file
  $etagFile = wp_tmp_folder.$id.'-etag.txt';
  $fh = fopen($etagFile, 'w');
  fwrite($fh, $etag);
  fclose($fh);
}

function loadLocalFile ($id) {
  $localFile = wp_tmp_folder.$id.'.txt';
  $handler = fopen($localFile, "r");
  $local = fread($handler, filesize($localFile));
  fclose($handler);

  return $local;
}

function saveLocalFile ($id, $data) {
  // save local file
  $localFile = wp_tmp_folder.$id.'.txt';
  $fh = fopen($localFile, 'w');
  fwrite($fh, $data);
  fclose($fh);
}

function updateData ($id, $ghETag) {
  $etagFilename = $id.'-etag.txt';
  $localETagFile = wp_tmp_folder.$etagFilename;

  if (file_exists($localETagFile)) {
    $eTag = loadLocalETag($localETagFile);

    if ($eTag == $ghETag) {
      return false;
    } else {
      saveLocalETag($id, $ghETag);
      return true;
    }
  } else {
    saveLocalETag($id, $ghETag);
    return true;
  }

}

/**
 * getContributors function
 */

function getContributors ($user, $repo) {
  $url = gh_api_host."repos/".$user."/".$repo."/contributors";
  $id = 'gh.'.$user.'.'.$repo.'.contributors';

  $githubETag = getGithubEtag($url, true);

  // eTAG control
  if (updateData($id, $githubETag)) {
    // get remote data from github
    $data = getGithubData($url, $id);

    // process contributors data
    $contributors = json_decode($data);
    $response = array();

    foreach ($contributors as $k => $user) {
      $fullUser = isset($user->url) ? getGithubData($user->url) : array();
      array_push($response, array(
          'user' => $user
        , 'full' => json_decode($fullUser)
      ));
    };

    $response = json_encode($response);
    saveLocalFile($id, $response);

    return $response;

  } else {
    return loadLocalFile($id);
  }
}

/**
 * getGuthubData
 * retrieve data through github API
 */

function getData ($type, $params) {

  switch ($type) {
    case "contributors":
      return getContributors($params['user'], $params['repo']);
    break;
  }
}

$type = $_GET["type"];

$params = array (
    'user' => $_GET["user"]
  , 'repo' => $_GET["repo"]
);

echo getData($type, $params);
