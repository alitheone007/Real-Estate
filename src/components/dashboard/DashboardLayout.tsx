import { ReactNode, useState, useEffect } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import {
  LayoutDashboard,
  Users,
  BarChart3,
  Settings,
  Menu,
  X,
  LogOut,
} from "lucide-react";
import { GradientButton } from "../ui/GradientButton";
import { useCountry } from "@/contexts/CountryContext";

interface DashboardLayoutProps {
  children: ReactNode;
  userType: "admin" | "builder" | "client" | "influencer" | "agent";
}

const DashboardLayout = ({ children, userType }: DashboardLayoutProps) => {
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const location = useLocation();
  const navigate = useNavigate();
  const { selectedCountry } = useCountry();

  useEffect(() => {
    const user = JSON.parse(localStorage.getItem('user') || 'null');
    if (!user || user.role !== userType) {
      navigate('/login');
    }
    // Optionally enforce country selection for dashboard access
    if (!selectedCountry) {
      navigate('/');
    }
  }, [userType, navigate, selectedCountry]);

  const navigation = {
    admin: [
      { name: "Dashboard", href: "/admin", icon: LayoutDashboard },
      { name: "Users", href: "/admin/users", icon: Users },
      { name: "Analytics", href: "/admin/analytics", icon: BarChart3 },
      { name: "Settings", href: "/admin/settings", icon: Settings },
    ],
    builder: [
      { name: "Dashboard", href: "/builder", icon: LayoutDashboard },
      { name: "Properties", href: "/builder/properties", icon: Users },
      { name: "Campaigns", href: "/builder/campaigns", icon: BarChart3 },
      { name: "Settings", href: "/builder/settings", icon: Settings },
    ],
    client: [
      { name: "Dashboard", href: "/client", icon: LayoutDashboard },
      { name: "Properties", href: "/client/properties", icon: Users },
      { name: "Favorites", href: "/client/favorites", icon: BarChart3 },
      { name: "Settings", href: "/client/settings", icon: Settings },
    ],
    influencer: [
      { name: "Dashboard", href: "/influencer", icon: LayoutDashboard },
      { name: "Campaigns", href: "/influencer/campaigns", icon: Users },
      { name: "Social Media", href: "/influencer/social-media", icon: BarChart3 },
      { name: "Settings", href: "/influencer/settings", icon: Settings },
    ],
    agent: [
      { name: "Dashboard", href: "/agent", icon: LayoutDashboard },
      { name: "Properties", href: "/agent/properties", icon: Users },
      { name: "Leads", href: "/agent/leads", icon: BarChart3 },
      { name: "Settings", href: "/agent/settings", icon: Settings },
    ],
  };

  const currentNav = navigation[userType];

  const handleLogout = () => {
    localStorage.removeItem('user');
    navigate('/');
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Mobile Sidebar */}
      <div
        className={`fixed inset-0 z-40 lg:hidden ${
          isSidebarOpen ? "block" : "hidden"
        }`}
      >
        <div className="fixed inset-0 bg-gray-600 bg-opacity-75" onClick={() => setIsSidebarOpen(false)} />
        <div className="fixed inset-y-0 left-0 flex w-64 flex-col bg-white">
          <div className="flex h-16 items-center justify-between px-4">
            <Link to="/" className="flex items-center">
              <span className="text-realtyflow-navy font-serif text-2xl font-bold">
                Realty<span className="text-realtyflow-gold">Flow</span>
                <span className="text-realtyflow-navy text-sm align-top ml-1">Pro</span>
              </span>
            </Link>
            <button
              onClick={() => setIsSidebarOpen(false)}
              className="text-gray-500 hover:text-gray-600"
            >
              <X size={24} />
            </button>
          </div>
          <nav className="flex-1 space-y-1 px-2 py-4">
            {currentNav.map((item) => (
              <Link
                key={item.name}
                to={item.href}
                className={`group flex items-center px-2 py-2 text-sm font-medium rounded-md ${
                  location.pathname === item.href
                    ? "bg-realtyflow-navy text-white"
                    : "text-gray-600 hover:bg-gray-50 hover:text-realtyflow-navy"
                }`}
              >
                <item.icon
                  className={`mr-3 h-5 w-5 ${
                    location.pathname === item.href
                      ? "text-white"
                      : "text-gray-400 group-hover:text-realtyflow-navy"
                  }`}
                />
                {item.name}
              </Link>
            ))}
          </nav>
          <div className="border-t border-gray-200 p-4">
            <GradientButton
              variant="secondary"
              className="w-full justify-center"
              onClick={handleLogout}
            >
              <LogOut className="mr-2 h-4 w-4" />
              Logout
            </GradientButton>
          </div>
        </div>
      </div>

      {/* Desktop Sidebar */}
      <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div className="flex min-h-0 flex-1 flex-col border-r border-gray-200 bg-white">
          <div className="flex h-16 items-center px-4">
            <Link to="/" className="flex items-center">
              <span className="text-realtyflow-navy font-serif text-2xl font-bold">
                Realty<span className="text-realtyflow-gold">Flow</span>
                <span className="text-realtyflow-navy text-sm align-top ml-1">Pro</span>
              </span>
            </Link>
          </div>
          <nav className="flex-1 space-y-1 px-2 py-4">
            {currentNav.map((item) => (
              <Link
                key={item.name}
                to={item.href}
                className={`group flex items-center px-2 py-2 text-sm font-medium rounded-md ${
                  location.pathname === item.href
                    ? "bg-realtyflow-navy text-white"
                    : "text-gray-600 hover:bg-gray-50 hover:text-realtyflow-navy"
                }`}
              >
                <item.icon
                  className={`mr-3 h-5 w-5 ${
                    location.pathname === item.href
                      ? "text-white"
                      : "text-gray-400 group-hover:text-realtyflow-navy"
                  }`}
                />
                {item.name}
              </Link>
            ))}
          </nav>
          <div className="border-t border-gray-200 p-4">
            <GradientButton
              variant="secondary"
              className="w-full justify-center"
              onClick={handleLogout}
            >
              <LogOut className="mr-2 h-4 w-4" />
              Logout
            </GradientButton>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="lg:pl-64">
        <div className="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
          <button
            type="button"
            className="px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-realtyflow-navy lg:hidden"
            onClick={() => setIsSidebarOpen(true)}
          >
            <Menu size={24} />
          </button>
          <div className="flex flex-1 justify-between px-4">
            <div className="flex flex-1">
              {/* Add search or other header content here */}
            </div>
            <div className="ml-4 flex items-center md:ml-6">
              {/* Add profile dropdown or other header actions here */}
            </div>
          </div>
        </div>

        <main className="py-6">
          <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {children}
          </div>
        </main>
      </div>
    </div>
  );
};

export default DashboardLayout; 