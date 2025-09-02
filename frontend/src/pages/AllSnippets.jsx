import React, { useState, useEffect } from "react";

import TopBar from "@/components/features/TopBar";
import { TypographyH3 } from "@/components/ui/TypographyH3";
import Snippets from "@/components/features/Snippets";

export default function AllSnippets() {
  return (
    <div className="codesnip-ai-wrapper">
      <div className="codesnip-ai-content">
        <TopBar />
        <div>
          <div className="flex justify-between items-center mb-8">
            <TypographyH3 className="m-0!">All Snippets</TypographyH3>
          </div>
          <Snippets />
        </div>
      </div>
    </div>
  );
}
