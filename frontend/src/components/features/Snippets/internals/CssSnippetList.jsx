export default function CssSnippetList() {
  return (
    <div className="overflow-x-auto bg-white rounded-lg shadow">
      <table className="w-full table-auto">
        <thead>
          <tr className="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
            <th className="py-3 px-6 text-left">ID</th>
            <th className="py-3 px-6 text-left">Name</th>
            <th className="py-3 px-6 text-left">Source Code</th>
            <th className="py-3 px-6 text-left">Created At</th>
            <th className="py-3 px-6 text-left">Author</th>
            <th className="py-3 px-6 text-left">Actions</th>
            <th className="py-3 px-6 text-left">Status</th>
          </tr>
        </thead>
        <tbody className="text-gray-600 text-sm">
          <tr className="border-b border-gray-200 hover:bg-gray-100">
            <td className="py-3 px-6 text-center text-lg!" colSpan={7}>Comming Soon</td>
          </tr>
        </tbody>
      </table>
    </div>
  );
}
