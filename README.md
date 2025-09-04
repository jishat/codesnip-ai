# CodeSnip AI - WordPress Plugin

## Description

CodeSnip AI helps developers and site owners save and manage reusable **HTML code snippets** directly from the WordPress admin. This makes it easy to store, organize, and reuse snippets without editing theme files or risking unsafe code.

🚀 **Initial Release (v1.0.0)**  
- Supports **HTML snippets only**.  
- CSS and JavaScript snippet support is planned for upcoming versions.  

### Key Features

- **Save and manage HTML snippets securely** - Store your code snippets safely in WordPress
- **Sanitization and validation** - All snippet input is properly sanitized and validated
- **Optional AI Assistant** - For snippet optimization or refactoring using your own API key
- **External AI provider support** - Supports OpenAI using your own API key
- **No API key required** - Core snippet management works without any API key
- **WordPress security best practices** - Proper escaping, sanitization, and capability checks
- **Modern React interface** - Beautiful, responsive UI built with React
- **Responsive design** - Works seamlessly on desktop and mobile devices

### AI Assistant (Optional)

- If you want to use AI to optimize your snippets, you can enter your own API key (OpenAI) in the plugin settings
- No key is provided by this plugin. You must bring your own
- If you do not configure a key, AI features remain disabled  
- All AI input/output is sanitized before being stored or displayed

This ensures that the plugin works out of the box for **manual snippet management**, and AI features are **100% optional**.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/codesnip-ai` directory, or install via the WordPress Plugins screen
2. Activate the plugin through the **Plugins** screen in WordPress
3. Go to **CodeSnip AI** in the admin menu to start saving snippets
4. (Optional) If you want to enable AI assistant features:
   - Go to **Settings → CodeSnip AI**
   - Enter your own API key (OpenAI)
   - Save settings  
   Without this, AI features remain disabled

## Development Setup

### Prerequisites
- Node.js (v16 or higher)
- npm or yarn
- WordPress development environment

### Setup Steps
1. Clone the repository
2. Install dependencies: `npm install`
3. Start development server: `npm run dev`
4. The React app will be available at `http://localhost:5173`

### Building for Production
```bash
npm run build
```

## Security Features

- **Nonce Verification**: All AJAX requests are protected with WordPress nonces
- **Capability Checks**: Settings management restricted to users with `manage_options` capability
- **Input Sanitization**: All user inputs are properly sanitized and validated
- **API Key Protection**: API keys are stored securely using WordPress options API
- **XSS Prevention**: Output is properly escaped to prevent cross-site scripting attacks

## Shortcode Usage

Display snippets anywhere on your site using the shortcode:

```
[codesnip id="1"]
```

Replace `1` with the actual snippet ID you want to display.

## Frequently Asked Questions

### Do I need an API key to use this plugin?
No. The plugin works fully for saving and managing HTML snippets without any API key. Only if you want to use the AI assistant features do you need to provide your own key.

### Does the plugin send my code to external services?
Only if you use the AI Assistant. In that case, your snippet and prompt are sent to your chosen AI provider (OpenAI). The plugin itself does not log or share your data.

### Can I save CSS, JS, or PHP snippets?
At launch, the plugin supports **HTML snippets only**. Support for **CSS and JavaScript** snippets is planned in future releases.

### Why do I need to provide my own API key?
For security and transparency. The plugin does not include or share any API keys. If you choose to enable AI features, you must provide your own key from your AI provider.

## Privacy Policy

This plugin does not collect or share any personal data by default. All snippets are stored locally in your WordPress database.

If you choose to enable the optional AI Assistant feature and configure your own API key (OpenAI), then your snippet text and prompt will be sent to that external provider for processing. No data is sent anywhere else, and the plugin does not log or store any AI responses outside of your WordPress database.

## Troubleshooting

### Common Issues

1. **"OpenAI API key not configured" error**
   - Ensure you've entered your API key in the settings
   - Verify the API key format (should start with `sk-`)

2. **API connection test fails**
   - Check your internet connection
   - Verify your OpenAI API key is valid
   - Ensure you have sufficient API credits

3. **Settings not saving**
   - Check if you have `manage_options` capability
   - Verify WordPress permissions
   - Check for JavaScript errors in browser console

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Development and Contribution

CodeSnip AI free version code is Open Source and available on [GitHub](https://github.com/jishat/codesnip-ai).

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Screenshots

1. Snippet management screen
2. Settings page with API key input
3. AI optimization in action (optional)

## Changelog

### Version 1.0.0
- Initial release
- HTML snippet management only
- AI assistant (optional, requires user-provided API key)

## Upgrade Notice

### Version 1.0.0
First release with HTML snippet management and optional AI assistant.

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support, feature requests, or bug reports, please create an issue in the repository.

## Credits

- Built with React and modern web technologies
- Powered by OpenAI's GPT models
- Follows WordPress coding standards and best practices
- Author: Mohammad Azizur Rahman Jishat
- GitHub: [https://github.com/jishat](https://github.com/jishat)