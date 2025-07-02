<?php

function codesnip_register_cpt() {
  register_post_type('codesnip', [
    'label' => 'Code Snippets',
    'public' => false,
    'show_ui' => true,
    'supports' => ['title', 'editor'],
    'menu_icon' => 'dashicons-editor-code',
  ]);
}
add_action('init', 'codesnip_register_cpt');
