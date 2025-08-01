import React, { useState, useEffect } from 'react'

export default function dfdfd() {
  const [code, setCode] = useState('')
  const [result, setResult] = useState('')
  const [loading, setLoading] = useState(false)
  const [savedSnippets, setSavedSnippets] = useState([])

  useEffect(() => {
    fetch('/wp-json/codesnip/v1/snippets')
      .then(res => res.json())
      .then(setSavedSnippets)
  }, [])

  const sendRequest = async (type) => {
    if (!code.trim()) return setResult('Please enter code.')

    setLoading(true)
    const res = await fetch('/wp-json/codesnip/v1/ai', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type, code }),
    })
    const data = await res.json()
    setResult(data.result || data.error)
    setLoading(false)
  }

  const saveSnippet = async () => {
    const res = await fetch('/wp-json/codesnip/v1/snippets', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code, output: result })
    })
    const data = await res.json()
    setSavedSnippets(prev => [...prev, data])
  }

  return (
    <div className="p-6 max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold mb-6">🚀 CodeSnip AI</h1>

      <textarea
        className="w-full border p-4 rounded mb-4"
        rows="6"
        placeholder="Paste your code here..."
        value={code}
        onChange={e => setCode(e.target.value)}
      />

      <div className="flex gap-3 mb-4">
        <button className="bg-blue-500 text-white px-4 py-2 rounded" onClick={() => sendRequest('explain')}>Explain</button>
        <button className="bg-green-600 text-white px-4 py-2 rounded" onClick={() => sendRequest('clean')}>Clean</button>
        <button className="bg-purple-600 text-white px-4 py-2 rounded" onClick={() => sendRequest('tailwind_full')}>Tailwindify (Exact)</button>
        <button className="bg-purple-600 text-white px-4 py-2 rounded" onClick={() => sendRequest('tailwind_optimized')}>Tailwindify (Optimized)</button>
      </div>

      {result && (
        <div className="bg-gray-100 p-4 rounded mb-4 whitespace-pre-wrap text-sm">
          {result}
        </div>
      )}

      <button
        className="bg-black text-white px-4 py-2 rounded mb-6"
        onClick={saveSnippet}
        disabled={!result}
      >
        💾 Save Snippet
      </button>

      <h2 className="text-xl font-semibold mt-8 mb-2">📋 Saved Snippets</h2>
      {savedSnippets.map((item) => (
        <div key={item.id} className="bg-white border p-4 mb-3 rounded shadow-sm">
          <div className="text-sm text-gray-600 mb-2">Shortcode: <code>[codesnip id="{item.id}"]</code></div>
          <pre className="bg-gray-100 p-3 rounded text-xs whitespace-pre-wrap">{item.output}</pre>
        </div>
      ))}
    </div>
  )
}
