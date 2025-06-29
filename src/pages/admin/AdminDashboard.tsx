import React, { useState, useEffect } from 'react';
import { Users, DollarSign, Home } from 'lucide-react';
import DashboardLayout from '@/components/dashboard/DashboardLayout';
import { StatCard } from '@/components/dashboard/StatCard';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AdminAPIsPage from './AdminAPIsPage';

interface DashboardData {
  userStats: { role: string; count: number }[];
  propertyStats: {
    total_properties: number;
    active_properties: number;
    pending_properties: number;
    average_price: number;
  };
  leadStats: {
    total_leads: number;
    new_leads: number;
    contacted_leads: number;
    qualified_leads: number;
  };
  recentLeads: Array<{
    id: number;
    name: string;
    email: string;
    property_title: string;
    status: string;
    created_at: string;
  }>;
  recentProperties: Array<{
    id: number;
    title: string;
    price: number;
    status: string;
    builder_name: string;
    created_at: string;
  }>;
  analytics: Array<{
    page_views: number;
    unique_visitors: number;
    leads_generated: number;
    date: string;
  }>;
}

const AdminDashboard: React.FC = () => {
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showAPIs, setShowAPIs] = useState(false);

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        const response = await fetch('http://localhost/Real-Estate/api/admin/dashboard.php');
        if (!response.ok) {
          throw new Error('Failed to fetch dashboard data');
        }
        const result = await response.json();
        if (result.success) {
          setData(result.data);
        } else {
          throw new Error(result.message || 'Failed to fetch dashboard data');
        }
      } catch (err: any) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  if (loading) {
    return (
      <DashboardLayout userType="admin">
        <div className="flex items-center justify-center h-full">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-realtyflow-navy mx-auto"></div>
            <p className="mt-4 text-gray-600">Loading dashboard data...</p>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  if (error) {
    return (
      <DashboardLayout userType="admin">
        <div className="flex items-center justify-center h-full">
          <div className="text-center">
            <p className="text-red-600">Error: {error}</p>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  if (!data) return null;

  return (
    <DashboardLayout userType="admin">
      <div className="space-y-6">
        <h1 className="text-2xl font-bold text-gray-900">Admin Dashboard</h1>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatCard
            title="Total Users"
            value={data.userStats.reduce((acc, curr) => acc + curr.count, 0)}
            icon={<Users className="h-6 w-6 text-blue-600" />}
          />
          <StatCard
            title="Total Properties"
            value={data.propertyStats.total_properties}
            icon={<Home className="h-6 w-6 text-green-600" />}
          />
          <StatCard
            title="Total Leads"
            value={data.leadStats.total_leads}
            icon={<Users className="h-6 w-6 text-purple-600" />}
          />
          <StatCard
            title="Average Property Price"
            value={`$${Math.round(data.propertyStats.average_price).toLocaleString()}`}
            icon={<DollarSign className="h-6 w-6 text-yellow-600" />}
          />
        </div>

        {/* Recent Activity */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          {/* Recent Leads */}
          <Card>
            <CardHeader>
              <CardTitle>Recent Leads</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {data.recentLeads.map((lead) => (
                  <div key={lead.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                      <p className="font-medium text-gray-900">{lead.name}</p>
                      <p className="text-sm text-gray-600">{lead.property_title}</p>
                    </div>
                    <span className={`px-3 py-1 rounded-full text-sm ${
                      lead.status === 'new' ? 'bg-blue-100 text-blue-800' :
                      lead.status === 'contacted' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-green-100 text-green-800'
                    }`}>
                      {lead.status}
                    </span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Recent Properties */}
          <Card>
            <CardHeader>
              <CardTitle>Recent Properties</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {data.recentProperties.map((property) => (
                  <div key={property.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                      <p className="font-medium text-gray-900">{property.title}</p>
                      <p className="text-sm text-gray-600">By {property.builder_name}</p>
                    </div>
                    <div className="text-right">
                      <p className="font-medium text-gray-900">${property.price.toLocaleString()}</p>
                      <span className={`px-3 py-1 rounded-full text-sm ${
                        property.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                      }`}>
                        {property.status}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Analytics Chart */}
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Analytics Overview</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="p-4 bg-blue-50 rounded-lg">
              <h3 className="text-sm font-medium text-blue-800">Page Views</h3>
              <p className="text-2xl font-semibold text-blue-900">
                {data.analytics[0]?.page_views.toLocaleString()}
              </p>
            </div>
            <div className="p-4 bg-green-50 rounded-lg">
              <h3 className="text-sm font-medium text-green-800">Unique Visitors</h3>
              <p className="text-2xl font-semibold text-green-900">
                {data.analytics[0]?.unique_visitors.toLocaleString()}
              </p>
            </div>
            <div className="p-4 bg-purple-50 rounded-lg">
              <h3 className="text-sm font-medium text-purple-800">Leads Generated</h3>
              <p className="text-2xl font-semibold text-purple-900">
                {data.analytics[0]?.leads_generated.toLocaleString()}
              </p>
            </div>
          </div>
        </div>

        <button onClick={() => setShowAPIs(true)} className="px-3 py-1 bg-blue-600 text-white rounded mb-4">API Connected Section</button>
        {showAPIs && <AdminAPIsPage />}
      </div>
    </DashboardLayout>
  );
};

export default AdminDashboard; 