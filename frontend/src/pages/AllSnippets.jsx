import React, { useState, useEffect } from "react";
import { Button } from "../components/ui/Button";

import TopBar from "@/components/features/TopBar";
import { TypographyH3 } from "@/components/ui/TypographyH3";
import Snippets from "@/components/features/Snippets";
import { useNavigate } from "react-router-dom";

export default function AllSnippets() {
  const navigate = useNavigate()

  return (
    <div className="min-h-screen text-gray-800 p-6">
      <TopBar />
      <div>
        <div className="flex justify-between items-center border-b pb-2 mb-8">
          <TypographyH3 className="m-0!">All Snippets</TypographyH3>
          <Button className="cursor-pointer" onClick={() => navigate('/add-new')}>Add New Snippet</Button>
        </div>
        <Snippets />
      </div>
    </div>
  );
}
