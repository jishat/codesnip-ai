import React, { useState, useEffect } from "react";
import TopBar from "@/components/features/TopBar";
import CodeEditor from "@/components/ui/CodeEditor";
import { TypographyH3 } from "@/components/ui/TypographyH3";
import { ResizableHandle, ResizablePanel, ResizablePanelGroup } from "@/components/ui/Resizable";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";
import { TypographyH4 } from "@/components/ui/TypographyH4";
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from "@/components/ui/Breadcrumb";

export default function AddNew() {
  const [code, setCode] = useState("");
  const [result, setResult] = useState("");
  const [loading, setLoading] = useState(false);

  const sendRequest = async (type) => {
    if (!code.trim()) return setResult("Please enter code.");

    setLoading(true);
    const res = await fetch("/wp-json/codesnip/v1/ai", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ type, code }),
    });
    const data = await res.json();
    setResult(data.result || data.error);
    setLoading(false);
  };

  const saveSnippet = async () => {
    const res = await fetch("/wp-json/codesnip/v1/snippets", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ code, output: result }),
    });
    const data = await res.json();
  };

  return (
    <div className="min-h-screen text-gray-800">
      <TopBar />
      <div className="p-6">
        <Breadcrumb>
          <BreadcrumbList>
            <BreadcrumbItem>
              <BreadcrumbLink href="/">Home</BreadcrumbLink>
            </BreadcrumbItem>
            <BreadcrumbSeparator />
            <BreadcrumbItem>
              <BreadcrumbLink href="/components">Components</BreadcrumbLink>
            </BreadcrumbItem>
            <BreadcrumbSeparator />
            <BreadcrumbItem>
              <BreadcrumbPage>Breadcrumb</BreadcrumbPage>
            </BreadcrumbItem>
          </BreadcrumbList>
        </Breadcrumb>
        <TypographyH3 className="border-b pb-2 mt-0!">
          Add New Snippet
        </TypographyH3>
        <RadioGroup defaultValue="html" className="flex mb-3">
          <div className="flex items-center gap-2">
            <RadioGroupItem value="html" id="r1" />
            <Label htmlFor="r1">HTML</Label>
          </div>
          <div className="flex items-center gap-2">
            <RadioGroupItem value="css" id="r2" disabled />
            <Label htmlFor="r2">CSS</Label>
          </div>
          <div className="flex items-center gap-2">
            <RadioGroupItem value="javascript" id="r3" disabled />
            <Label htmlFor="r3">Javascript</Label>
          </div>
        </RadioGroup>
        <ResizablePanelGroup
          direction="horizontal"
          className="max-w-full rounded-lg border md:min-w-[450px]"
        >
          <ResizablePanel defaultSize={50}>
            <TypographyH4 className="mb-4! mt-4! ml-4!">Code Preview</TypographyH4>
            <CodeEditor value={code} onChange={setCode} className="rounded-bl-lg pr-1" />
          </ResizablePanel>
          <ResizableHandle />
          <ResizablePanel defaultSize={50}>
            <TypographyH4 className="mb-4! mt-4! ml-4!">Tailwindify Snippet</TypographyH4>
            <CodeEditor value={code} onChange={setCode} className="rounded-br-lg pr-1" />
          </ResizablePanel>
        </ResizablePanelGroup>
        {/* <CodeEditor value={code} onChange={setCode} />
        <div className="flex gap-4 mb-4">
          <button
            onClick={() => sendRequest("tailwind_full")}
            className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
          >
            Tailwindify
          </button>
        </div>
        {loading && <p>Generating...</p>}
        <CodeEditor value={result} />
        <button
          className="bg-black text-white px-4 py-2 rounded mb-6"
          onClick={saveSnippet}
          disabled={!result}
        >
          💾 Save Snippet
        </button> */}
      </div>
    </div>
  );
}
