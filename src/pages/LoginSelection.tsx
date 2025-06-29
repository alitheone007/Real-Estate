import { Link } from "react-router-dom";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Users, Home, User, Settings, UserPlus, Building2 } from "lucide-react";

const LoginSelection = () => {
  return (
    <div className="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
      <div className="mb-8 text-center">
        <h1 className="text-realtyflow-navy font-serif text-3xl font-bold">
          Realty<span className="text-realtyflow-gold">Flow</span>
          <span className="text-realtyflow-navy text-sm align-top ml-1">Pro</span>
        </h1>
        <p className="text-gray-600 mt-2">Select your account type to continue</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl w-full">
        <Link to="/client-login">
          <Card className="hover:shadow-md transition-shadow cursor-pointer h-full">
            <CardHeader className="pb-2">
              <div className="w-12 h-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-2">
                <User size={24} />
              </div>
              <CardTitle>Client</CardTitle>
              <CardDescription>Property buyers and investors</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600 text-sm">
                Search properties, track your interests, and connect with agents.
              </p>
            </CardContent>
          </Card>
        </Link>

        <Link to="/agent-login">
          <Card className="hover:shadow-md transition-shadow cursor-pointer h-full">
            <CardHeader className="pb-2">
              <div className="w-12 h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mb-2">
                <Building2 size={24} />
              </div>
              <CardTitle>Agent</CardTitle>
              <CardDescription>Real estate professionals</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600 text-sm">
                List properties, manage leads, and grow your business.
              </p>
            </CardContent>
          </Card>
        </Link>

        <Link to="/builder-login">
          <Card className="hover:shadow-md transition-shadow cursor-pointer h-full">
            <CardHeader className="pb-2">
              <div className="w-12 h-12 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center mb-2">
                <Home size={24} />
              </div>
              <CardTitle>Builder</CardTitle>
              <CardDescription>Property developers and builders</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600 text-sm">
                Showcase your projects and connect with potential buyers.
              </p>
            </CardContent>
          </Card>
        </Link>

        <Link to="/influencer-login">
          <Card className="hover:shadow-md transition-shadow cursor-pointer h-full">
            <CardHeader className="pb-2">
              <div className="w-12 h-12 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center mb-2">
                <Users size={24} />
              </div>
              <CardTitle>Influencer</CardTitle>
              <CardDescription>Content creators and marketers</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600 text-sm">
                Promote properties and earn commissions through your network.
              </p>
            </CardContent>
          </Card>
        </Link>

        <Link to="/admin-login">
          <Card className="hover:shadow-md transition-shadow cursor-pointer h-full">
            <CardHeader className="pb-2">
              <div className="w-12 h-12 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mb-2">
                <Settings size={24} />
              </div>
              <CardTitle>Admin</CardTitle>
              <CardDescription>System administrators</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600 text-sm">
                Manage users, monitor platform activity, and access analytics.
              </p>
            </CardContent>
          </Card>
        </Link>
      </div>

      <div className="mt-8 text-center">
        <p className="text-gray-600 mb-4">New influencer? Join our platform!</p>
        <Link to="/influencer-registration">
          <Card className="hover:shadow-md transition-shadow cursor-pointer max-w-md mx-auto">
            <CardHeader className="pb-2">
              <div className="w-12 h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mb-2 mx-auto">
                <UserPlus size={24} />
              </div>
              <CardTitle>Register as Influencer</CardTitle>
              <CardDescription>4,000+ followers required</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600 text-sm">
                Join our network and start earning from real estate referrals.
              </p>
            </CardContent>
          </Card>
        </Link>
      </div>
    </div>
  );
};

export default LoginSelection; 