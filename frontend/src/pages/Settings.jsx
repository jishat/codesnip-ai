import { useState, useEffect } from 'react'

export default function Settings() {
  const [settings, setSettings] = useState({ apiKey: '', model: '', maxTokens: 1000 })

  useEffect(() => {
    // Fetch settings from DB if needed
  }, [])

  const handleSave = async () => {
    await fetch('/wp-json/codesnip/v1/settings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(settings)
    })
    alert('Settings saved!')
  }

  return (
    <div className="min-h-screen bg-gray-100 p-6">
      <h2 className="text-xl font-bold mb-4">Plugin Settings</h2>
      <div className="space-y-4 bg-white p-4 rounded shadow">
        <input
          className="w-full border p-2 rounded"
          placeholder="OpenAI API Key"
          value={settings.apiKey}
          onChange={e => setSettings({ ...settings, apiKey: e.target.value })}
        />
        <input
          className="w-full border p-2 rounded"
          placeholder="Model (e.g., gpt-3.5-turbo)"
          value={settings.model}
          onChange={e => setSettings({ ...settings, model: e.target.value })}
        />
        <input
          type="number"
          className="w-full border p-2 rounded"
          placeholder="Max Tokens"
          value={settings.maxTokens}
          onChange={e => setSettings({ ...settings, maxTokens: e.target.value })}
        />
        <button onClick={handleSave} className="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Save</button>
      </div>
    </div>
  )
}
