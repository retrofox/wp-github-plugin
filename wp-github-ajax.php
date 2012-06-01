<?php
header('Content-Type: application/jsonrequest');

include('core/constants.php');
include('core/api.php');

define('wp_tmp_folder', getcwd().'/../../uploads/wp-github-plugin/');

$params = array (
    'user' => $_GET["user"]
  , 'repo' => $_GET["repo"]
);

echo getData($params);
