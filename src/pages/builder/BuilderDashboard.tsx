import DashboardLayout from "@/components/dashboard/DashboardLayout";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Home, Users, DollarSign, TrendingUp } from "lucide-react";
import { useEffect, useState } from "react";

const BuilderDashboard = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        const response = await fetch("/api/builder/dashboard.php");
        if (!response.ok) throw new Error("Failed to fetch dashboard data");
        await response.json(); // You can use this for future dynamic data
      } catch (err: any) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };
    fetchDashboardData();
  }, []);

  if (loading) return <DashboardLayout userType="builder"><div className="flex items-center justify-center h-full"><div className="text-center"><div className="animate-spin rounded-full h-12 w-12 border-b-2 border-realtyflow-navy mx-auto"></div><p className="mt-4 text-gray-600">Loading dashboard data...</p></div></div></DashboardLayout>;
  if (error) return <DashboardLayout userType="builder"><div className="flex items-center justify-center h-full"><div className="text-center"><p className="text-red-600">{error}</p></div></div></DashboardLayout>;

  return (
    <DashboardLayout userType="builder">
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-realtyflow-navy">Builder Dashboard</h1>
          <p className="text-gray-600">Welcome to your builder dashboard.</p>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Active Properties</CardTitle>
              <Home className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">24</div>
              <p className="text-xs text-muted-foreground">
                +3 new this month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Total Leads</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">1,234</div>
              <p className="text-xs text-muted-foreground">
                +15% from last month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Revenue</CardTitle>
              <DollarSign className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">$89,231</div>
              <p className="text-xs text-muted-foreground">
                +25% from last month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Conversion Rate</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">12.5%</div>
              <p className="text-xs text-muted-foreground">
                +2.5% from last month
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Recent Properties */}
        <Card>
          <CardHeader>
            <CardTitle>Recent Properties</CardTitle>
            <CardDescription>
              Your latest property listings and their performance.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add property items here */}
              <p className="text-gray-600">No recent properties to display.</p>
            </div>
          </CardContent>
        </Card>

        {/* Active Campaigns */}
        <Card>
          <CardHeader>
            <CardTitle>Active Campaigns</CardTitle>
            <CardDescription>
              Your current marketing campaigns and their performance.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add campaign items here */}
              <p className="text-gray-600">No active campaigns to display.</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </DashboardLayout>
  );
};

export default BuilderDashboard; 