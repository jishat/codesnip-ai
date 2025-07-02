<?php

function codesnip_ai_settings_menu() {
  add_options_page('CodeSnip AI Settings', 'CodeSnip AI', 'manage_options', 'codesnip-ai', 'codesnip_ai_settings_page');
}
add_action('admin_menu', 'codesnip_ai_settings_menu');

function codesnip_ai_settings_page() {
  ?>
  <div class="wrap">
    <h1>CodeSnip AI Settings</h1>
    <form method="post" action="options.php">
      <?php
        settings_fields('codesnip_ai_settings');
        do_settings_sections('codesnip-ai');
        submit_button();
      ?>
    </form>
  </div>
  <?php
}

function codesnip_ai_register_settings() {
  register_setting('codesnip_ai_settings', 'codesnip_openai_key');

  add_settings_section('codesnip_ai_main', 'OpenAI API Configuration', null, 'codesnip-ai');

  add_settings_field('codesnip_openai_key', 'OpenAI API Key', function () {
    $val = esc_attr(get_option('codesnip_openai_key'));
    echo "<input type='text' name='codesnip_openai_key' value='$val' style='width: 400px'>";
  }, 'codesnip-ai', 'codesnip_ai_main');
}
add_action('admin_init', 'codesnip_ai_register_settings');
