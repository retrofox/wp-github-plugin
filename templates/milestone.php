<?php $mile = $data->milestone; ?>
<?php $issues_data = $data->issues; ?>
<?php $issues = $issues_data->issues; ?>

<div class="wp-github-plugin wp-github-plugin-<?php echo $type; ?>">
  <h2>
    <a href="<?php echo $mile->url; ?>" target="_blank" title="<?php echo $mile->title; ?>"><?echo $mile->title ;?></a>
  </h2>

  <div class="resume">
    <ul>
      <li><?php _e('State', 'wp_github_plugin'); ?>: <strong><?php echo $mile->state; ?></strong></li>
      <li><?php _e('Open issues', 'wp_github_plugin'); ?>: <strong><?php echo $mile->open_issues; ?></strong></li>
      <li><?php _e('Closed issues', 'wp_github_plugin'); ?>: <strong><?php echo $mile->closed_issues; ?></strong></li>
    </ul>
    <div class="milestone-description">
      <p><?php echo $mile->description; ?></p>
    </div>

    <?php if (count($issues) > 0) : ?>
    <div class="milestone-issues-container">
      <h2><?php echo _e('Issues', 'wp_github_plugin'); ?></h2>
      <div class-"milestone-issues">
        <?php foreach ($issues as $issue) : ?>
        <ul>
          <li>
            <strong class-"issue-number">#<?php echo $issue->number; ?></strong>
            <a class="issue-link" href="<?php echo $issue->html_url; ?>" title="<?php echo $issue->title; ?>" target="_blank">
              <?php echo $issue->title; ?>
            </a>
          </li>
        </ul>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
