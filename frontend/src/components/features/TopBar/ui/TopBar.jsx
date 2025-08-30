import Logo from '@/components/ui/Logo';
import logo from '@/resource/img/codesnip-logo.png';
import { Star } from 'lucide-react';

export default function TopBar(){
    return(
      <div className="mb-10 flex items-center justify-between bg-white">
        <div className="flex items-center gap-3">
          {/* <img 
            src={logo} 
            alt="CodeSnip AI Logo" 
            className="w-12 h-auto object-contain"
          /> */}
          <Logo />
          <h1 className="text-lg! font-extrabold! m-0">CodeSnip AI</h1>
        </div>
        <div className="flex items-center gap-x-2">
          <a href="#" className="flex items-center text-green-950! px-4 py-2 text-md font-bold bg-gray-200 leading-6 capitalize duration-100 transform rounded-full 
            cursor-pointer focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 focus:outline-none o 
            hover:shadow-sm">
            <Star className="w-5 h-5 mr-2" />
            Review us
          </a>
        </div>
      </div>
    )
}