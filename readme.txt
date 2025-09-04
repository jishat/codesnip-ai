=== CodeSnip AI ===
Contributors: jishat
Tags: html, snippets, code, ai, openai
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later

Save and manage HTML code snippets safely in WordPress. Connect AI providers to optimize snippets. HTML support included; CSS/JS coming soon.

== Description ==

CodeSnip AI helps developers and site owners save and manage reusable **HTML code snippets** directly from the WordPress admin.  
This makes it easy to store, organize, and reuse snippets without editing theme files or risking unsafe code.

🚀 **Initial Release (v1.0.0)**  
- Supports **HTML snippets only**.  
- CSS and JavaScript snippet support is planned for upcoming versions.  

**Key Features:**
- Save and manage HTML snippets securely.
- Sanitization and validation of all snippet input.
- Optional AI Assistant for snippet optimization or refactoring.
- Supports external AI providers (OpenAI) using **your own API key**.
- No API key required for core snippet management.
- Secure WordPress coding practices (escaping, sanitization, capability checks).

**AI Assistant (Optional):**
- If you want to use AI to optimize your snippets, you can enter your own API key (OpenAI) in the plugin settings.
- No key is provided by this plugin. You must bring your own.
- If you do not configure a key, AI features remain disabled.  
- All AI input/output is sanitized before being stored or displayed.

This ensures that the plugin works out of the box for **manual snippet management**, and AI features are **100% optional**.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/codesnip-ai` directory, or install via the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **CodeSnip AI** in the admin menu to start saving snippets.
4. (Optional) If you want to enable AI assistant features:
   - Go to **Settings → CodeSnip AI**.
   - Enter your own API key (OpenAI or Claude).
   - Save settings.  
   Without this, AI features remain disabled.

== Frequently Asked Questions ==

= Do I need an API key to use this plugin? =
No. The plugin works fully for saving and managing HTML snippets without any API key.  
Only if you want to use the AI assistant features do you need to provide your own key.

= Does the plugin send my code to external services? =
Only if you use the AI Assistant. In that case, your snippet and prompt are sent to your chosen AI provider (OpenAI or others which coming soon).  
The plugin itself does not log or share your data.

= Can I save CSS, JS, or PHP snippets? =
At launch, the plugin supports **HTML snippets only**.  
Support for **CSS and JavaScript** snippets is planned in future releases.

= Why do I need to provide my own API key? =
For security and transparency. The plugin does not include or share any API keys. If you choose to enable AI features, you must provide your own key from your AI provider.

== Privacy Policy ==
This plugin does not collect or share any personal data by default.  
All snippets are stored locally in your WordPress database.

If you choose to enable the optional AI Assistant feature and configure your own API key (OpenAI), then your snippet text and prompt will be sent to that external provider for processing. No data is sent anywhere else, and the plugin does not log or store any AI responses outside of your WordPress database.

== Development and Contribution ==
Codesnip ai free versions codes are Open Source and available in [GitHub](https://github.com/jishat/codesnip-ai).

== Screenshots ==
1. Snippet management screen.
2. Settings page with API key input.
3. AI optimization in action (optional).

== Changelog ==

= 1.0.0 =
* Initial release.
* HTML snippet management only.
* AI assistant (optional, requires user-provided API key).

== Upgrade Notice ==

= 1.0.0 =
First release with HTML snippet management and optional AI assistant.
