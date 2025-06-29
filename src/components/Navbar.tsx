import { Link } from "react-router-dom";
import { Menu, X } from "lucide-react";
import { useState } from "react";
import { GradientButton } from "./ui/GradientButton";

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  return (
    <nav className="bg-white shadow-sm">
      <div className="container mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <Link to="/" className="flex items-center">
            <span className="text-realtyflow-navy font-serif text-2xl font-bold">
              Realty<span className="text-realtyflow-gold">Flow</span>
              <span className="text-realtyflow-navy text-sm align-top ml-1">Pro</span>
            </span>
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-8">
            <Link to="/features" className="text-gray-600 hover:text-realtyflow-navy">
              Features
            </Link>
            <Link to="/pricing" className="text-gray-600 hover:text-realtyflow-navy">
              Pricing
            </Link>
            <Link to="/about" className="text-gray-600 hover:text-realtyflow-navy">
              About
            </Link>
            <Link to="/contact" className="text-gray-600 hover:text-realtyflow-navy">
              Contact
            </Link>
            <Link to="/login">
              <GradientButton>Login</GradientButton>
            </Link>
          </div>

          {/* Mobile Menu Button */}
          <div className="md:hidden">
            <button
              onClick={() => setIsMenuOpen(!isMenuOpen)}
              className="text-gray-600 hover:text-realtyflow-navy focus:outline-none"
            >
              {isMenuOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
          </div>
        </div>

        {/* Mobile Navigation */}
        {isMenuOpen && (
          <div className="md:hidden">
            <div className="px-2 pt-2 pb-3 space-y-1">
              <Link
                to="/features"
                className="block px-3 py-2 text-gray-600 hover:text-realtyflow-navy"
                onClick={() => setIsMenuOpen(false)}
              >
                Features
              </Link>
              <Link
                to="/pricing"
                className="block px-3 py-2 text-gray-600 hover:text-realtyflow-navy"
                onClick={() => setIsMenuOpen(false)}
              >
                Pricing
              </Link>
              <Link
                to="/about"
                className="block px-3 py-2 text-gray-600 hover:text-realtyflow-navy"
                onClick={() => setIsMenuOpen(false)}
              >
                About
              </Link>
              <Link
                to="/contact"
                className="block px-3 py-2 text-gray-600 hover:text-realtyflow-navy"
                onClick={() => setIsMenuOpen(false)}
              >
                Contact
              </Link>
              <Link
                to="/login"
                className="block px-3 py-2"
                onClick={() => setIsMenuOpen(false)}
              >
                <GradientButton className="w-full">Login</GradientButton>
              </Link>
            </div>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navbar; 