import React, { useState } from 'react'

export default function App() {
  const [code, setCode] = useState('')
  const [result, setResult] = useState('')
  const [loading, setLoading] = useState(false)

  const sendRequest = async (type) => {
    setLoading(true)
    const res = await fetch('/wp-json/codesnip/v1/ai', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type, code })
    })
    const data = await res.json()
    setResult(data.result || data.error)
    setLoading(false)
  }

  return (
    <div className="p-6 max-w-3xl mx-auto">
      <h1 className="text-2xl font-bold mb-4">🧠 CodeSnip AI</h1>
      <textarea
        className="w-full border border-gray-300 p-4 rounded mb-4"
        rows={6}
        value={code}
        onChange={(e) => setCode(e.target.value)}
        placeholder="Paste your code here..."
      />
      <div className="flex gap-3 mb-4">
        <button className="bg-blue-500 text-white px-4 py-2 rounded" onClick={() => sendRequest('explain')}>
          Explain
        </button>
        <button className="bg-green-600 text-white px-4 py-2 rounded" onClick={() => sendRequest('clean')}>
          Clean
        </button>
        <button className="bg-purple-600 text-white px-4 py-2 rounded" onClick={() => sendRequest('tailwind')}>
          Tailwindify
        </button>
      </div>
      {/* <div class="max-w-2xl mx-auto my-6 rounded-2xl border border-gray-200 bg-white shadow dark:bg-gray-900 dark:border-gray-700">
          <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-300">Example.js</span>
            <button
              class="text-xs text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
              onclick="navigator.clipboard.writeText(document.getElementById('sourcecode').textContent)"
            >
              📋 Copy
            </button>
          </div>
          <pre class="p-4 overflow-x-auto text-sm text-gray-800 dark:text-gray-100">
        <code id="sourcecode" class="font-mono">
          {loading ? 'Loading...' : result}
        </code>
          </pre>
        </div> */}

<div class="relative max-w-2xl mx-auto mt-24">
  <div class="bg-gray-900 text-white p-4 rounded-md">
    <div class="flex justify-between items-center mb-2">
      <span class="text-gray-400 flex gap-2"
        ><div class="w-3 h-3 rounded-full bg-red-500"></div>
        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
        <div class="w-3 h-3 rounded-full bg-green-500"></div></span><button
        class="bg-gray-800 code hover:bg-gray-700 text-gray-300 px-3 py-1 rounded-md"
        data-clipboard-target="#code">
        Copy
      </button>
    </div>
    <div class="overflow-x-auto">
      <pre id="code" class="language-javascript" xclass="text-gray-300 bg-gray-800 code p-4 rounded-md whitespace-pre overflow-x-auto">
<code>
<div class="pricing-table">
    <div class="plan">
      <h2>Basic Plan</h2>
      <p>$9/month</p>
    </div>
    <div class="plan featured">
      <h2>Pro Plan</h2>
      <p>$29/month</p>
    </div>
  </div>
  </code></pre>
    </div>
  </div>
</div>
      {/* <pre className="bg-gray-100 p-4 rounded text-sm whitespace-pre-wrap">{loading ? 'Loading...' : result}</pre> */}
    </div>
  )
}
