import DashboardLayout from "@/components/dashboard/DashboardLayout";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Home, Heart, Bell, MessageSquare } from "lucide-react";
import { useEffect, useState } from "react";

const ClientDashboard = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        const response = await fetch("/api/client/dashboard.php");
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

  if (loading) return <DashboardLayout userType="client"><div className="flex items-center justify-center h-full"><div className="text-center"><div className="animate-spin rounded-full h-12 w-12 border-b-2 border-realtyflow-navy mx-auto"></div><p className="mt-4 text-gray-600">Loading dashboard data...</p></div></div></DashboardLayout>;
  if (error) return <DashboardLayout userType="client"><div className="flex items-center justify-center h-full"><div className="text-center"><p className="text-red-600">{error}</p></div></div></DashboardLayout>;

  return (
    <DashboardLayout userType="client">
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-realtyflow-navy">Client Dashboard</h1>
          <p className="text-gray-600">Welcome to your client dashboard.</p>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Saved Properties</CardTitle>
              <Heart className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">12</div>
              <p className="text-xs text-muted-foreground">
                +2 new this week
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Viewings Scheduled</CardTitle>
              <Home className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">3</div>
              <p className="text-xs text-muted-foreground">
                Next: Tomorrow at 2 PM
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Notifications</CardTitle>
              <Bell className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">5</div>
              <p className="text-xs text-muted-foreground">
                2 new today
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Messages</CardTitle>
              <MessageSquare className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">8</div>
              <p className="text-xs text-muted-foreground">
                3 unread
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Recommended Properties */}
        <Card>
          <CardHeader>
            <CardTitle>Recommended Properties</CardTitle>
            <CardDescription>
              Properties that match your preferences and search history.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add recommended properties here */}
              <p className="text-gray-600">No recommended properties to display.</p>
            </div>
          </CardContent>
        </Card>

        {/* Recent Activity */}
        <Card>
          <CardHeader>
            <CardTitle>Recent Activity</CardTitle>
            <CardDescription>
              Your recent interactions and updates.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add activity items here */}
              <p className="text-gray-600">No recent activity to display.</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </DashboardLayout>
  );
};

export default ClientDashboard; 