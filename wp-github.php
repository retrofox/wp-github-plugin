<?php
/*
Plugin Name: WP GitHub
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

load_plugin_textdomain('widget_wp_github', false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * Github constants
 */

define('gh_api_host', 'https://api.github.com/');

/**
 * getGuthubData
 * retrieve data through github API
 */

function getGithubData ($user, $repo) {
  $url = gh_api_host."repos/".$user."/".$repo."/contributors";

  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);

  $contributors = json_decode($response, true);
  curl_close($ch);

  return $contributors;
}

/**
 * wordpress github class
 */

class WP_GitHub extends WP_Widget {

  function __construct()  {
    $opciones = array(
        'classname'     => 'wp-github.css'
      , 'description'   => 'wordpress-github api'
    );

    parent::__construct('wp-github-api', 'WP GitHub API', $opciones);
  }

  function widget($args, $instance) {
    extract($args);
    extract($instance);

    $data = getGithubData($instance['user'], $instance['repo']);
    ?>


    <div class='widget-container wp-github wp-github-contributors'>
      <h2 class="user">
        <a target="_blank" href="https://github.com/<?php echo $instance['user'] ?>/<?php echo $instance['repo']; ?>" class="wp-github-title">
          <?php echo $instance['title']; ?>
        </a>
      </h2>
      <?php if ($data['message']) : ?>
      <p class="message"><?php echo $data['message']; ?></p>
      <?php else : ?>
      <ul>
        <?php for ($i = 0; $i < count($data); $i++) : ?>
        <li>
          <a href="<?php echo $data[$i]['url'] ?>" class="wp-github-user">
            <img src="<?php echo $data[$i]['avatar_url']; ?>" />
            <span class="user"><?php echo $data[$i]['login']; ?></span>
          </a>
        </li>
        <?php endfor; ?> 
      </ul>
      <?php endif; ?>
    </div>
    <?php
    echo $after_widget;
  }

  function update($new_instance, $old_instance) {
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
  register_widget('WP_GitHub');
  wp_register_script('WP_GitHub', 'pipo.js');
}

add_action('widgets_init', 'widget_wp_github');
?>
