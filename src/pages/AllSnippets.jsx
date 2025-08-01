import React, { useState, useEffect } from "react";
import { Button } from "../components/ui/Button";
import { Switch } from "../components/ui/Switch";
import { Label } from "@/components/ui/label";

import TopBar from "@/components/features/TopBar";
import { TypographyH3 } from "@/components/ui/TypographyH3";
import Snippets from "@/components/features/Snippets";

export default function AllSnippets() {
  const [code, setCode] = useState("");
  const [result, setResult] = useState("");
  const [loading, setLoading] = useState(false);
  const [savedSnippets, setSavedSnippets] = useState([]);

  useEffect(() => {
    fetch("/wp-json/codesnip/v1/snippets")
      .then((res) => res.json())
      .then(setSavedSnippets);
  }, []);

  return (
    <div className="min-h-screen text-gray-800">
      <TopBar />
      {/* <div className="p-6">
        <h2 className="text-lg font-semibold mb-4">All Snippets</h2>
        <div className="bg-white p-4 rounded shadow">
          {savedSnippets.map((item) => (
            <div key={item.id} className="bg-white border p-4 mb-3 rounded shadow-sm">
              <div className="text-sm text-gray-600 mb-2">Shortcode: <code>[codesnip id="{item.id}"]</code></div>
              <pre className="bg-gray-100 p-3 rounded text-xs whitespace-pre-wrap">{item.output}</pre>
            </div>
          ))}
        </div>
      </div> */}
      <div className="p-6">
        <TypographyH3 className="border-b pb-2 mt-0!">All Snippets</TypographyH3>
        <Snippets />
      </div>
    </div>
  );
}
