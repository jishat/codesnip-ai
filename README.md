# CodeSnip AI - WordPress Plugin

A powerful WordPress plugin that integrates OpenAI's AI capabilities to help developers generate, manage, and organize code snippets.

## Features

- 🤖 **AI-Powered Code Generation**: Generate code snippets using OpenAI's advanced language models
- 📝 **Snippet Management**: Create, edit, and organize code snippets by type (HTML, CSS, JavaScript, PHP)
- 🔧 **Flexible Configuration**: Customize OpenAI settings including API key, model selection, and token limits
- 🎨 **Modern React Interface**: Beautiful, responsive UI built with React and modern web technologies
- 🔒 **Security First**: Built following WordPress security best practices with proper nonce verification and capability checks
- 📱 **Responsive Design**: Works seamlessly on desktop and mobile devices

## OpenAI Configuration

The plugin requires an OpenAI API key to function. You can configure the following settings:

### Required Settings
- **API Key**: Your OpenAI API key (starts with `sk-`)
- **AI Model**: Choose from available OpenAI models:
  - GPT-4.1 Nano (recommended - fast and efficient)
  - GPT-4.1 Mini
  - GPT-4.1
  - GPT-4o
  - GPT-4o Mini
  - o1 (most capable)
  - o1 Mini
  - o3
  - o3 Mini
  - GPT-4
  - GPT-4 Turbo
  - GPT-3.5 Turbo
- **Max Tokens**: Maximum response length (1-4000 tokens)

### How to Configure

#### Option 1: React Interface (Recommended)
1. Navigate to **CodeSnip AI > Settings** in your WordPress admin
2. Enter your OpenAI API key
3. Select your preferred AI model
4. Set the maximum tokens limit
5. Click "Save Settings"
6. Test the connection using "Test API Connection"

#### Option 2: Traditional WordPress Settings
1. Go to **Settings > CodeSnip AI** in your WordPress admin
2. Configure the same options as above
3. Click "Save Changes"

## Installation

1. Upload the plugin files to `/wp-content/plugins/codesnip-ai/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure your OpenAI API key in the settings
4. Start creating and managing code snippets!

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

## Database Schema

The plugin creates a custom table `{prefix}codesnip_snippets` with the following structure:

```sql
CREATE TABLE {prefix}codesnip_snippets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  snippet LONGTEXT NOT NULL,
  type VARCHAR(20) DEFAULT 'html' NOT NULL,
  status TINYINT(1) DEFAULT 1 NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## API Endpoints

The plugin provides several AJAX endpoints for managing snippets and settings:

- `codesnip_ai_assist` - Generate code using AI
- `codesnip_ai_save` - Save new snippets
- `codesnip_ai_get_all` - Retrieve all snippets
- `codesnip_ai_get_by_type` - Get snippets by type
- `codesnip_ai_get_by_id` - Get specific snippet
- `codesnip_ai_update` - Update existing snippets
- `codesnip_ai_delete` - Delete snippets
- `codesnip_ai_toggle_status` - Toggle snippet status
- `codesnip_ai_save_settings` - Save OpenAI configuration
- `codesnip_ai_get_settings` - Retrieve OpenAI configuration

## Shortcode Usage

Display snippets anywhere on your site using the shortcode:

```
[codesnip id="1"]
```

Replace `1` with the actual snippet ID you want to display.

## Hooks and Filters

The plugin follows WordPress coding standards and provides various hooks for customization:

- `codesnip_ai_before_save` - Action before saving snippets
- `codesnip_ai_after_save` - Action after saving snippets
- `codesnip_ai_before_delete` - Action before deleting snippets
- `codesnip_ai_after_delete` - Action after deleting snippets

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

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Changelog

### Version 1.0
- Initial release
- AI-powered code generation
- Snippet management system
- OpenAI configuration settings
- Modern React interface
- WordPress security best practices

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support, feature requests, or bug reports, please create an issue in the repository.

## Credits

- Built with React and modern web technologies
- Powered by OpenAI's GPT models
- Follows WordPress coding standards and best practices
