// src/pages/admin/AdminAnalyticsPage.tsx
import React, { useEffect, useState } from 'react';
import DashboardLayout from '../../components/dashboard/DashboardLayout';

interface UserStat {
  role: string;
  count: string;
}

interface PropertyStats {
  total_properties: string;
  active_properties: string;
  pending_properties: string;
  average_price: string;
}

interface LeadStats {
  total_leads: string;
  new_leads: string;
  contacted_leads: string;
  qualified_leads: string;
}

interface RecentLead {
    id: number;
    name: string;
    email: string;
    phone?: string | null;
    property_id?: number | null;
    message?: string | null;
    created_at: string;
    status: 'new' | 'contacted' | 'qualified' | 'closed';
    property_title?: string | null;
}

interface RecentProperty {
    id: number;
    title: string;
    price: string;
    status: 'active' | 'pending' | 'sold' | 'rented';
    created_at: string;
    builder_id?: number | null;
    builder_name?: string | null;
}

interface AnalyticsData {
  date: string;
  views?: number;
  leads?: number;
  // Add other analytics fields as needed
}

interface DashboardData {
  userStats: UserStat[];
  propertyStats: PropertyStats;
  leadStats: LeadStats;
  recentLeads: RecentLead[];
  recentProperties: RecentProperty[];
  analytics: AnalyticsData[];
}

const AdminAnalyticsPage: React.FC = () => {
  const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        const response = await fetch('http://localhost/Real-Estate/api/admin/dashboard.php');
        const data = await response.json();

        if (data.success) {
          setDashboardData(data.data);
        } else {
          setError(data.message || 'Failed to fetch dashboard data');
        }
      } catch (err: any) {
        setError(err.message || 'An unexpected error occurred');
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  if (loading) {
    return <DashboardLayout userType="admin"><div>Loading Analytics...</div></DashboardLayout>;
  }

  if (error) {
    return <DashboardLayout userType="admin"><div>Error loading Analytics: {error}</div></DashboardLayout>;
  }

  return (
    <DashboardLayout userType="admin">
      <div className="container mx-auto p-4">
        <h1 className="text-2xl font-bold mb-4">Admin Analytics</h1>

        {dashboardData && (
          <div>
            {/* User Stats */}
            <div className="mb-6">
              <h2 className="text-xl font-semibold mb-2">User Statistics</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {dashboardData.userStats.map((stat, index) => (
                  <div key={index} className="bg-white p-4 rounded shadow">
                    <h3 className="text-lg font-medium">{stat.role}</h3>
                    <p className="text-gray-700">Count: {stat.count}</p>
                  </div>
                ))}
              </div>
            </div>

            {/* Property Stats */}
            {dashboardData.propertyStats && (
                <div className="mb-6">
                <h2 className="text-xl font-semibold mb-2">Property Statistics</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="bg-white p-4 rounded shadow">
                        <h3 className="text-lg font-medium">Total Properties</h3>
                        <p className="text-gray-700">{dashboardData.propertyStats.total_properties}</p>
                    </div>
                    <div className="bg-white p-4 rounded shadow">
                        <h3 className="text-lg font-medium">Active Properties</h3>
                        <p className="text-gray-700">{dashboardData.propertyStats.active_properties}</p>
                    </div>
                    <div className="bg-white p-4 rounded shadow">
                        <h3 className="text-lg font-medium">Pending Properties</h3>
                        <p className="text-gray-700">{dashboardData.propertyStats.pending_properties}</p>
                    </div>
                    <div className="bg-white p-4 rounded shadow">
                        <h3 className="text-lg font-medium">Average Price</h3>
                        <p className="text-gray-700">{dashboardData.propertyStats.average_price}</p>
                    </div>
                </div>
                </div>
            )}
            
            {/* Lead Stats */}
             {dashboardData.leadStats && (
                <div className="mb-6">
                    <h2 className="text-xl font-semibold mb-2">Lead Statistics</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="bg-white p-4 rounded shadow">
                            <h3 className="text-lg font-medium">Total Leads</h3>
                            <p className="text-gray-700">{dashboardData.leadStats.total_leads}</p>
                        </div>
                        <div className="bg-white p-4 rounded shadow">
                            <h3 className="text-lg font-medium">New Leads</h3>
                            <p className="text-gray-700">{dashboardData.leadStats.new_leads}</p>
                        </div>
                        <div className="bg-white p-4 rounded shadow">
                            <h3 className="text-lg font-medium">Contacted Leads</h3>
                            <p className="text-gray-700">{dashboardData.leadStats.contacted_leads}</p>
                        </div>
                        <div className="bg-white p-4 rounded shadow">
                            <h3 className="text-lg font-medium">Qualified Leads</h3>
                            <p className="text-gray-700">{dashboardData.leadStats.qualified_leads}</p>
                        </div>
                    </div>
                </div>
            )}

            {/* Recent Leads */}
            <div className="mb-6">
              <h2 className="text-xl font-semibold mb-2">Recent Leads</h2>
              <div className="bg-white p-4 rounded shadow">
                <ul className="divide-y divide-gray-200">
                  {dashboardData.recentLeads.map((lead) => (
                    <li key={lead.id} className="py-3">
                      <p className="text-lg font-medium">{lead.name} ({lead.email})</p>
                      <p className="text-gray-700 text-sm">Property: {lead.property_title || 'N/A'}</p>
                      <p className="text-gray-700 text-sm">Status: {lead.status}</p>
                      <p className="text-gray-500 text-xs">Created: {new Date(lead.created_at).toLocaleString()}</p>
                    </li>
                  ))}
                </ul>
              </div>
            </div>

            {/* Recent Properties */}
            <div className="mb-6">
              <h2 className="text-xl font-semibold mb-2">Recent Properties</h2>
              <div className="bg-white p-4 rounded shadow">
                <ul className="divide-y divide-gray-200">
                  {dashboardData.recentProperties.map((property) => (
                    <li key={property.id} className="py-3">
                      <p className="text-lg font-medium">{property.title} (${property.price})</p>
                       <p className="text-gray-700 text-sm">Builder: {property.builder_name || 'N/A'}</p>
                      <p className="text-gray-700 text-sm">Status: {property.status}</p>
                      <p className="text-gray-500 text-xs">Created: {new Date(property.created_at).toLocaleString()}</p>
                    </li>
                  ))}
                </ul>
              </div>
            </div>

             {/* Analytics Data (Placeholder) */}
             <div className="mb-6">
              <h2 className="text-xl font-semibold mb-2">Analytics Data (Last 7 Days)</h2>
               {dashboardData.analytics && dashboardData.analytics.length > 0 ? (
                   <div className="bg-white p-4 rounded shadow">
                       {/* Display analytics data here - e.g., a simple list or integrate a chart library */}
                         <ul className="divide-y divide-gray-200">
                              {dashboardData.analytics.map((day, index) => (
                                   <li key={index} className="py-3">
                                         <p className="text-lg font-medium">Date: {day.date}</p>
                                          <p className="text-gray-700 text-sm">Views: {day.views ?? 'N/A'}</p>
                                           <p className="text-gray-700 text-sm">Leads: {day.leads ?? 'N/A'}</p>
                                    </li>
                               ))}
                         </ul>
                   </div>
               ) : (
                    <div className="bg-white p-4 rounded shadow">
                        <p>No analytics data available for the last 7 days.</p>
                    </div>
               )}
             </div>

          </div>
        )}
      </div>
    </DashboardLayout>
  );
};

export default AdminAnalyticsPage; 