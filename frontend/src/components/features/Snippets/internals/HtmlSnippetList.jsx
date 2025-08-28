import { Switch } from "../../../../components/ui/Switch";

export default function HtmlSnippetList() {
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
            <td className="py-3 px-6 text-left">1</td>
            <td className="py-3 px-6 text-left">Abhiraj k</td>
            <td className="py-3 px-6 text-left">Abhiraj k</td>
            <td className="py-3 px-6 text-left">abhi@kerala.com</td>
            <td className="py-3 px-6 text-left">Admin</td>
            <td className="py-3 px-6 text-left">
              <div className="flex item-center">
                <button className="w-4 mr-2 transform hover:text-blue-500 hover:scale-110">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                    />
                  </svg>
                </button>
                <button className="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                </button>
              </div>
            </td>
            <td className="py-3 px-6 text-left"><Switch /></td>
          </tr>
        </tbody>
      </table>
    </div>
  );
}
