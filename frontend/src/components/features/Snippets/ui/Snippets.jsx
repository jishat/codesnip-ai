import { Tabs, TabsContent, TabsList, TabsTrigger } from "../../../../components/ui/Tabs";
import HtmlSnippetList from "../internals/HtmlSnippetList";
import CssSnippetList from "../internals/CssSnippetList";
import JavascriptSnippetList from "../internals/JavascriptSnippetList";
import AllSnippetList from "../internals/AllSnippetList";

export default function Snippets() {
  return (
    <Tabs defaultValue="allSnippet">
      <TabsList>
        <TabsTrigger className="cursor-pointer" value="allSnippet">All Snippets</TabsTrigger>
        <TabsTrigger className="cursor-pointer" value="htmlSnippet">HTML</TabsTrigger>
        <TabsTrigger className="cursor-pointer" value="cssSnippet">CSS</TabsTrigger>
        <TabsTrigger className="cursor-pointer" value="jsSnippet">JavaScript</TabsTrigger>
      </TabsList>
      <TabsContent value="allSnippet">
        <AllSnippetList />
      </TabsContent>
      <TabsContent value="htmlSnippet">
        <HtmlSnippetList />
      </TabsContent>
      <TabsContent value="cssSnippet"><CssSnippetList /></TabsContent>
      <TabsContent value="jsSnippet"><JavascriptSnippetList /></TabsContent>
    </Tabs>
  );
}
