<?php

function codesnip_shortcode($atts) {
  $atts = shortcode_atts(['id' => ''], $atts);
  $post = get_post($atts['id']);
  if (!$post || $post->post_type !== 'codesnip') return '';

  ob_start();
  echo '<div class="codesnip-snippet">';
  echo '<style scoped>' . get_post_meta($post->ID, '_css', true) . '</style>';
  echo apply_filters('the_content', $post->post_content);
  echo '</div>';
  return ob_get_clean();
}
add_shortcode('codesnip', 'codesnip_shortcode');
