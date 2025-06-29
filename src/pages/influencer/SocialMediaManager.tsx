import DashboardLayout from "@/components/dashboard/DashboardLayout";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { GradientButton } from "@/components/ui/GradientButton";
import { Instagram, Twitter, Facebook, Youtube, Plus } from "lucide-react";

const SocialMediaManager = () => {
  return (
    <DashboardLayout userType="influencer">
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold text-realtyflow-navy">Social Media Manager</h1>
            <p className="text-gray-600">Manage your social media content and campaigns.</p>
          </div>
          <GradientButton>
            <Plus className="w-4 h-4 mr-2" />
            New Post
          </GradientButton>
        </div>

        {/* Platform Overview */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Instagram</CardTitle>
              <Instagram className="h-4 w-4 text-pink-600" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">24.5K</div>
              <p className="text-xs text-muted-foreground">
                +1.2K this month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Twitter</CardTitle>
              <Twitter className="h-4 w-4 text-blue-400" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">12.8K</div>
              <p className="text-xs text-muted-foreground">
                +800 this month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Facebook</CardTitle>
              <Facebook className="h-4 w-4 text-blue-600" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">18.3K</div>
              <p className="text-xs text-muted-foreground">
                +950 this month
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">YouTube</CardTitle>
              <Youtube className="h-4 w-4 text-red-600" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">8.2K</div>
              <p className="text-xs text-muted-foreground">
                +500 this month
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Content Calendar */}
        <Card>
          <CardHeader>
            <CardTitle>Content Calendar</CardTitle>
            <CardDescription>
              Schedule and manage your upcoming content across all platforms.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add calendar items here */}
              <p className="text-gray-600">No scheduled content to display.</p>
            </div>
          </CardContent>
        </Card>

        {/* Recent Posts */}
        <Card>
          <CardHeader>
            <CardTitle>Recent Posts</CardTitle>
            <CardDescription>
              Your latest content and its performance across platforms.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {/* Add post items here */}
              <p className="text-gray-600">No recent posts to display.</p>
            </div>
          </CardContent>
        </Card>

        {/* Analytics Overview */}
        <Card>
          <CardHeader>
            <CardTitle>Analytics Overview</CardTitle>
            <CardDescription>
              Performance metrics across all your social media platforms.
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

export default SocialMediaManager; 