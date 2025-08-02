import React from 'react'
import { EditorView } from '@codemirror/view'
import CodeMirror from '@uiw/react-codemirror'
import { html } from '@codemirror/lang-html'


const CodeEditor = ({ value, onChange, className="" }) => {
  return (
    <div className={`border shadow-sm ${className}`}>
      <CodeMirror
        value={value}
        height="450px"
        extensions={[html()]}
        onChange={(val) => onChange(val)}
        theme="light"
        placeholder="Paste your HTML/CSS code here.."
        basicSetup={{
          lineNumbers: true,
          highlightActiveLine: true,
          foldGutter: true,
        }}
      />
    </div>
  )
}

export default CodeEditor
