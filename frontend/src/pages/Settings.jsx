import TopBar from '@/components/features/TopBar'
import { TypographyH3 } from '@/components/ui/TypographyH3'
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
    <div className="min-h-screen text-gray-800 p-6">
      <TopBar />
      <div>
        <div className="flex justify-between items-center border-b pb-2 mb-8">
          <TypographyH3 className="m-0!">Settngs</TypographyH3>
        </div>
      </div>
    </div>
  )
}
