<?php
/*
Plugin Name: WP GitHub Plugin
Plugin URI: http://www.nodejs.es/wp-github-plugin
Description: Allow retrieve data from github through API 
Author: Damian Suarez
Version: 0.0.1
Author URI: http://www.retrofox.com.ar
Licence: A "Slug" license name e.g. GPL2
*/

/*  Copyright 2012  Damian Suarez  (email : rdsuarez@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Translation support
 */

load_plugin_textdomain('wp_github_plugin', false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * Github constants
 */

define('gh_host', 'http://www.github.com/');
define('gh_api_host', 'https://api.github.com/');
define('gh_plugin_path', $siteurl.'/wp-content/plugins/wp-github-plugin');

/**
 * clean cache
 */

function cleanCache ($id) {
  $upload_dir = wp_upload_dir();
  $tmp_folder = $upload_dir['basedir'].'/wp-github-plugin';
  $idf = $tmp_folder.$id;

  if (file_exists($idf.'.txt')) {
    unlink($idf.'.txt');
  }

  if (file_exists($idf.'-etag.txt')) {
    unlink($idf.'-etag.txt');
  }
}

/**
 * wordpress github class
 */

class WP_Github_Plugin extends WP_Widget {

  function __construct()  {

    // add stylesheet file
    wp_enqueue_style('wp-github', gh_plugin_path.'/wp-github.css');
    wp_enqueue_script('wp-github', gh_plugin_path.'/wp-github.js', array('jquery'));

    // create tmp folder
    $upload_dir = wp_upload_dir();
    $tmp_folder = $upload_dir['basedir'].'/wp-github-plugin';

    if (!file_exists($tmp_folder))
      mkdir($tmp_folder, 0777);

    $opciones = array(
        'classname'     => 'WP_Github_Plugin'
      , 'description'   => 'wordpress-github api'
    );

    parent::__construct('wp-github-api', 'WP GitHub API', $opciones);

  }

  function widget($args, $instance) {
    extract($args);
    extract($instance);
  ?>

    <div class="widget-container wp-github-widget wp-github-contributors" data-user="<?php echo $instance['user']; ?>" data-repo="<?php echo $instance['repo']; ?>" data-type="contributors">
      <h2 class="user">
        <a target="_blank" href="https://github.com/<?php echo $instance['user'] ?>/<?php echo $instance['repo']; ?>" class="wp-github-title">
          <?php echo $instance['title']; ?>
        </a>
      </h2>
    <div class="placeholder"></div>
    <?php echo $after_widget; ?>
  <?php
  }

  function update($new_instance, $old_instance) {
    // delete file when widget is updated
    $id = '/contributors_'.$old_instance['user'].'_'.$old_instance['repo'];
    cleanCache($id);

    return array(
        'title'       => strip_tags($new_instance['title'])
      , 'user'        => strip_tags($new_instance['user'])
      , 'repo'        => strip_tags($new_instance['repo'])
    );
  }

  function form($instance) {
    $instance = wp_parse_args( (array) $instance, array(
        'title'          => 'Sexvim Repository'
      , 'user'           => 'RetroFOX'
      , 'repo'           => 'sexvim'
    ));

    $instance['title'] = esc_attr($instance['title']);
    $instance['user']  = esc_attr($instance['user']);
    $instance['repo']  = esc_attr($instance['repo']);
  ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">Title</label></p>
      <input value="<?php echo $instance['title']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>">
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('user'); ?>">GitHub user</label></p>
      <input value="<?php echo $instance['user']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>">
    </p>
	
    <p>
      <label for="<?php echo $this->get_field_id('repo'); ?>">GitHub Repository</label></p>
      <input value="<?php echo $instance['repo']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('repo'); ?>" name="<?php echo $this->get_field_name('repo'); ?>">
    </p>
    <?php
  }
}

function widget_wp_github() {
  register_widget('WP_Github_Plugin');
}

add_action('widgets_init', 'widget_wp_github');
?>
