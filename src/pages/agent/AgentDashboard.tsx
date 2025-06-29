import { useEffect, useState } from 'react';
import DashboardLayout from '@/components/dashboard/DashboardLayout';
import { StatCard } from '@/components/dashboard/StatCard';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2, Users, DollarSign, TrendingUp } from 'lucide-react';

interface Property {
  id: number;
  title: string;
  price: number;
  status: string;
  leads_count: number;
  created_at: string;
}

interface Lead {
  id: number;
  name: string;
  email: string;
  phone: string;
  status: string;
  property_title: string;
  created_at: string;
}

interface Stats {
  total_properties: number;
  active_leads: number;
  converted_leads: number;
  total_commission: number;
}

const AgentDashboard = () => {
  const [properties, setProperties] = useState<Property[]>([]);
  const [leads, setLeads] = useState<Lead[]>([]);
  const [stats, setStats] = useState<Stats>({
    total_properties: 0,
    active_leads: 0,
    converted_leads: 0,
    total_commission: 0,
  });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        const response = await fetch('http://localhost/Real-Estate/api/agent/dashboard.php');
        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || 'Failed to fetch dashboard data');
        }

        if (data.success) {
          setProperties(data.properties);
          setLeads(data.leads);
          setStats(data.stats);
        } else {
          throw new Error(data.message || 'Failed to fetch dashboard data');
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
      <DashboardLayout userType="agent">
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
      <DashboardLayout userType="agent">
        <div className="flex items-center justify-center h-full">
          <div className="text-center">
            <p className="text-red-600">{error}</p>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout userType="agent">
      <div className="space-y-6">
        <h1 className="text-2xl font-bold text-gray-900">Agent Dashboard</h1>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <StatCard
            title="Total Properties"
            value={stats.total_properties}
            icon={<Building2 className="h-6 w-6" />}
            trend="up"
            trendValue="12%"
          />
          <StatCard
            title="Active Leads"
            value={stats.active_leads}
            icon={<Users className="h-6 w-6" />}
            trend="up"
            trendValue="8%"
          />
          <StatCard
            title="Converted Leads"
            value={stats.converted_leads}
            icon={<TrendingUp className="h-6 w-6" />}
            trend="up"
            trendValue="15%"
          />
          <StatCard
            title="Total Commission"
            value={`$${stats.total_commission.toLocaleString()}`}
            icon={<DollarSign className="h-6 w-6" />}
            trend="up"
            trendValue="20%"
          />
        </div>

        {/* Properties and Leads Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Properties List */}
          <Card>
            <CardHeader>
              <CardTitle>Recent Properties</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {properties.map((property) => (
                  <div
                    key={property.id}
                    className="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                  >
                    <div>
                      <h3 className="font-medium text-gray-900">{property.title}</h3>
                      <p className="text-sm text-gray-500">
                        ${property.price.toLocaleString()} • {property.leads_count} leads
                      </p>
                    </div>
                    <span
                      className={`px-2 py-1 text-xs font-medium rounded-full ${
                        property.status === 'active'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-gray-100 text-gray-800'
                      }`}
                    >
                      {property.status}
                    </span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Leads List */}
          <Card>
            <CardHeader>
              <CardTitle>Recent Leads</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {leads.map((lead) => (
                  <div
                    key={lead.id}
                    className="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                  >
                    <div>
                      <h3 className="font-medium text-gray-900">{lead.name}</h3>
                      <p className="text-sm text-gray-500">
                        {lead.email} • {lead.phone}
                      </p>
                      <p className="text-sm text-gray-500">{lead.property_title}</p>
                    </div>
                    <span
                      className={`px-2 py-1 text-xs font-medium rounded-full ${
                        lead.status === 'active'
                          ? 'bg-green-100 text-green-800'
                          : lead.status === 'converted'
                          ? 'bg-blue-100 text-blue-800'
                          : 'bg-gray-100 text-gray-800'
                      }`}
                    >
                      {lead.status}
                    </span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </DashboardLayout>
  );
};

export default AgentDashboard; 