import DashboardLayout from "@/components/dashboard/DashboardLayout";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { TrendingUp, DollarSign, Users, Calendar } from "lucide-react";
import { useEffect, useState } from "react";

const InfluencerDashboard = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        const response = await fetch("/api/influencer/dashboard.php");
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

  if (loading) return (
    <DashboardLayout userType="influencer">
      <div className="flex items-center justify-center h-full">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-realtyflow-navy mx-auto"></div>
          <p className="mt-4 text-gray-600">Loading dashboard data...</p>
        </div>
      </div>
    </DashboardLayout>
  );

  if (error) return (
    <DashboardLayout userType="influencer">
      <div className="flex items-center justify-center h-full">
        <div className="text-center">
          <p className="text-red-600">{error}</p>
        </div>
      </div>
    </DashboardLayout>
  );

  return (
    <DashboardLayout userType="influencer">
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-realtyflow-navy">Influencer Dashboard</h1>
          <p className="text-gray-600">Welcome to your influencer dashboard.</p>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Active Campaigns</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">5</div>
              <p className="text-xs text-muted-foreground">
                +2 new this month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Total Earnings</CardTitle>
              <DollarSign className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">$12,450</div>
              <p className="text-xs text-muted-foreground">
                +35% from last month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Engagement Rate</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">4.8%</div>
              <p className="text-xs text-muted-foreground">
                +0.5% from last month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Upcoming Posts</CardTitle>
              <Calendar className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">8</div>
              <p className="text-xs text-muted-foreground">
                Next: Tomorrow at 10 AM
              </p>
            </CardContent>
          </Card>
        </div>

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

        {/* Content Calendar */}
        <Card>
          <CardHeader>
            <CardTitle>Content Calendar</CardTitle>
            <CardDescription>
              Your upcoming content schedule and deadlines.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add calendar items here */}
              <p className="text-gray-600">No upcoming content to display.</p>
            </div>
          </CardContent>
        </Card>

        {/* Performance Analytics */}
        <Card>
          <CardHeader>
            <CardTitle>Performance Analytics</CardTitle>
            <CardDescription>
              Your content performance metrics and insights.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add analytics items here */}
              <p className="text-gray-600">No analytics data to display.</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </DashboardLayout>
  );
};

export default InfluencerDashboard; 