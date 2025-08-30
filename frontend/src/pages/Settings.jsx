import React, { useState, useEffect } from 'react'
import TopBar from '@/components/features/TopBar'
import { TypographyH3 } from '@/components/ui/TypographyH3'
import { TypographyH4 } from '@/components/ui/TypographyH4'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"

export default function Settings() {
  const [settings, setSettings] = useState({
    apiKey: '',
    model: 'gpt-4.1-nano',
    maxTokens: 1500
  })
  const [loading, setLoading] = useState(false)
  const [message, setMessage] = useState({ type: '', text: '' })

  const availableModels = [
    { value: 'gpt-4.1', label: 'GPT-4.1' },
    { value: 'gpt-4.1-mini', label: 'GPT-4.1 Mini' },
    { value: 'gpt-4.1-nano', label: 'GPT-4.1 Nano' },
    { value: 'gpt-4o', label: 'GPT-4o' },
    { value: 'gpt-4o-mini', label: 'GPT-4o Mini' },
    { value: 'o1', label: 'o1' },
    { value: 'o1-mini', label: 'o1 Mini' },
    { value: 'o3', label: 'o3' },
    { value: 'o3-mini', label: 'o3 Mini' },
    { value: 'gpt-4', label: 'GPT-4' },
    { value: 'gpt-4-turbo', label: 'GPT-4 Turbo' },
    { value: 'gpt-3.5-turbo', label: 'GPT-3.5 Turbo' }
  ]

  useEffect(() => {
    loadSettings()
  }, [])

  const loadSettings = async () => {
    try {
      const formData = new FormData()
      formData.append('action', 'codesnip_ai_get_settings')
      formData.append('_ajax_nonce', window.codesnip_ai_.nonce)

      const response = await fetch(window.codesnip_ai_.ajax_url, {
        method: 'POST',
        body: formData
      })

      const result = await response.json()
      if (result.success) {
        setSettings({
          apiKey: result.data.settings.api_key || '',
          model: result.data.settings.model || 'gpt-4-turbo',
          maxTokens: result.data.settings.max_tokens || 1500
        })
      }
    } catch (error) {
      console.error('Error loading settings:', error)
      setMessage({ type: 'error', text: 'Failed to load settings' })
    }
  }

  const handleSave = async () => {
    setLoading(true)
    setMessage({ type: '', text: '' })

    try {
      const formData = new FormData()
      formData.append('action', 'codesnip_ai_save_settings')
      formData.append('_ajax_nonce', window.codesnip_ai_.nonce)
      formData.append('api_key', settings.apiKey)
      formData.append('model', settings.model)
      formData.append('max_tokens', settings.maxTokens)

      const response = await fetch(window.codesnip_ai_.ajax_url, {
        method: 'POST',
        body: formData
      })

      const result = await response.json()
      if (result.success) {
        setMessage({ type: 'success', text: result.data.message })
      } else {
        setMessage({ type: 'error', text: result.data.error || 'Failed to save settings' })
      }
    } catch (error) {
      console.error('Error saving settings:', error)
      setMessage({ type: 'error', text: 'Failed to save settings' })
    } finally {
      setLoading(false)
    }
  }

  const handleInputChange = (field, value) => {
    setSettings(prev => ({ ...prev, [field]: value }))
  }

  const testApiConnection = async () => {
    if (!settings.apiKey) {
      setMessage({ type: 'error', text: 'Please enter an API key first' })
      return
    }

    setLoading(true)
    setMessage({ type: '', text: '' })

    try {
      const formData = new FormData()
      formData.append('action', 'codesnip_ai_assist')
      formData.append('_ajax_nonce', window.codesnip_ai_.nonce)
      formData.append('prompt', 'Hello, this is a test message to verify the API connection.')
      formData.append('snippet', '<div>Test</div>')

      const response = await fetch(window.codesnip_ai_.ajax_url, {
        method: 'POST',
        body: formData
      })

      const result = await response.json()
      if (result.success) {
        setMessage({ type: 'success', text: 'API connection successful! Your OpenAI configuration is working correctly.' })
      } else {
        setMessage({ type: 'error', text: result.data.error?.prompt || 'API connection failed. Please check your API key and try again.' })
      }
    } catch (error) {
      console.error('Error testing API:', error)
      setMessage({ type: 'error', text: 'Failed to test API connection' })
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen text-gray-800 p-6">
      <TopBar />
      <div>
        <div className="flex justify-between items-center border-b pb-2 mb-8">
          <TypographyH3 className="m-0!">Settings</TypographyH3>
        </div>

        <div className="bg-white rounded-lg shadow-sm border p-6 mb-6">
          <TypographyH4 className="mb-4">OpenAI Configuration</TypographyH4>
          
          <div className="space-y-4">
            <div className='max-w-xl'>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                OpenAI API Key
              </label>
              <Input
                value={settings.apiKey}
                onChange={(e) => handleInputChange('apiKey', e.target.value)}
                placeholder="sk-••••••••••••••••••••"
                className=""
              />

              <p className="text-xs text-gray-500 mt-1">
                You can get your API Keys in your <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener noreferrer" className="underline hover:text-blue-900">OpenAI Account</a>.
              </p>
            </div>

            <div className='max-w-xl'>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                AI Model
              </label>
              <Select 
                onValueChange={(value) => handleInputChange('model', value)}
                value={settings.model}
              >
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Select a fruit" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Model</SelectLabel>
                    {availableModels.map(model => (
                      <SelectItem key={model.value} value={model.value}>{model.label}</SelectItem>
                    ))}
                  </SelectGroup>
                </SelectContent>
              </Select>
              <p className="text-xs text-gray-500 mt-1">
                Choose the OpenAI model for code generation. GPT-4 models provide better results but cost more.
              </p>
            </div>

            <div className='max-w-xl'>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Max Tokens
              </label>
              <Input
                value={settings.maxTokens}
                onChange={(e) => handleInputChange('maxTokens', parseInt(e.target.value) || 1500)}
                min="1"
                max="4000"
                className="w-full"
              />
              <p className="text-xs text-gray-500 mt-1">
                Maximum number of tokens in the AI response (1-4000). Higher values allow for longer responses but cost more.
              </p>
            </div>

            {/* Message Display */}
            {message.text && (
              <div className={`p-3 rounded-md ${
                message.type === 'success' 
                  ? 'bg-green-50 text-green-800 border border-green-200' 
                  : 'bg-red-50 text-red-800 border border-red-200'
              }`}>
                {message.text}
              </div>
            )}

            <div className="flex space-x-3 pt-4">
              <Button
                onClick={handleSave}
                disabled={loading || !settings.apiKey}
                className="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 cursor-pointer"
              >
                {loading ? 'Saving...' : 'Save Settings'}
              </Button>
              
              <Button
                onClick={testApiConnection}
                disabled={loading || !settings.apiKey}
                variant="outline"
                className="border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer"
              >
                {loading ? 'Testing...' : 'Test API Connection'}
              </Button>
            </div>
          </div>
        </div>

        <div className="bg-blue-50 rounded-lg border border-blue-200 p-6">
          <TypographyH4 className="text-blue-800 mb-3">Getting Started</TypographyH4>
          <div className="text-blue-700 space-y-2 text-sm">
            <p>1. <strong>Get an OpenAI API Key:</strong> Visit <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener noreferrer" className="underline hover:text-blue-900">OpenAI Platform</a> to create your API key.</p>
            <p>2. <strong>Enter your API key</strong> in the field above and save the settings.</p>
            <p>3. <strong>Test the connection</strong> to ensure everything is working correctly.</p>
            <p>4. <strong>Start using CodeSnip AI</strong> to generate and manage your code snippets!</p>
          </div>
        </div>
      </div>
    </div>
  )
}
