<?php

function codesnip_call_openai($prompt) {
  $api_key = get_option('codesnip_openai_key');
  if (!$api_key) return 'No API key configured.';

  $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
    'headers' => [
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type' => 'application/json',
    ],
    'body' => json_encode([
      'model' => 'gpt-3.5-turbo',
      'messages' => [['role' => 'user', 'content' => $prompt]],
    ]),
  ]);

  if (is_wp_error($response)) return 'Error contacting AI.';
  $body = json_decode(wp_remote_retrieve_body($response), true);
  return $body['choices'][0]['message']['content'] ?? 'No response';
}
