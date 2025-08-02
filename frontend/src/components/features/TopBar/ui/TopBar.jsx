export default function TopBar(){
    return(
      <div className="flex items-center justify-between px-6 py-2 border-b-2 border-grey-50 bg-white">
        <h1 className="text-lg! font-bold! m-0">🧩 CodeSnip AI</h1>
        <div className="flex flexitems-center gap-x-2">
          <a href="#" className="text-green-950! px-4 py-2 text-md font-bold bg-green-300 leading-6 capitalize duration-100 transform rounded-sm shadow 
            cursor-pointer focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 focus:outline-none o 
            hover:shadow-md">
            Documentation
          </a>
        </div>
      </div>
    )
}