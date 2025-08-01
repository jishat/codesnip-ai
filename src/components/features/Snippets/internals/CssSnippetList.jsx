import { Switch } from "@/components/ui/Switch";

export default function CssSnippetList() {
  return (
    <div class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="w-full table-auto">
        <thead>
          <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <th class="py-3 px-6 text-left">ID</th>
            <th class="py-3 px-6 text-left">Name</th>
            <th class="py-3 px-6 text-left">Source Code</th>
            <th class="py-3 px-6 text-left">Created At</th>
            <th class="py-3 px-6 text-left">Author</th>
            <th class="py-3 px-6 text-left">Actions</th>
            <th class="py-3 px-6 text-left">Status</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
          <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="py-3 px-6 text-center text-lg!" colSpan={7}>Comming Soon</td>
          </tr>
        </tbody>
      </table>
    </div>
  );
}
