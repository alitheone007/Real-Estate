import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { DatePicker } from '../ui/date-picker';

interface AnalyticsData {
  total_views: number;
  unique_visitors: number;
  top_pages: Array<{ page_visited: string; views: number }>;
  device_breakdown: Array<{ device_type: string; count: number }>;
  country_breakdown: Array<{ country_code: string; count: number }>;
}

interface AnalyticsFilters {
  date_from?: string;
  date_to?: string;
}

const AnalyticsDashboard: React.FC = () => {
  const [analytics, setAnalytics] = useState<AnalyticsData | null>(null);
  const [filters, setFilters] = useState<AnalyticsFilters>({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Get the correct API base URL
  const getApiBaseUrl = () => {
    // In development, use localhost (XAMPP on default port)
    if (window.location.hostname === 'localhost' && window.location.port === '5173') {
      return 'http://localhost/Real-Estate';
    }
    // In production, use relative path
    return '';
  };

  const fetchAnalytics = async (filters: AnalyticsFilters = {}) => {
    try {
      setLoading(true);
      const params = new URLSearchParams();
      if (filters.date_from) params.append('date_from', filters.date_from);
      if (filters.date_to) params.append('date_to', filters.date_to);

      const apiUrl = `${getApiBaseUrl()}/api/analytics/track?${params}`;
      console.log('Fetching analytics from:', apiUrl);
      
      const response = await fetch(apiUrl);
      const data = await response.json();

      console.log('Analytics response:', data);

      if (data.success) {
        setAnalytics(data.data);
      } else {
        setError(data.message || 'Failed to fetch analytics data');
      }
    } catch (err) {
      console.error('Analytics fetch error:', err);
      setError('Failed to fetch analytics data');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAnalytics();
  }, []);

  const handleFilterChange = (newFilters: AnalyticsFilters) => {
    setFilters(newFilters);
    fetchAnalytics(newFilters);
  };

  const trackPageView = async (page: string) => {
    try {
      const apiUrl = `${getApiBaseUrl()}/api/analytics/track`;
      console.log('Tracking page view:', apiUrl, page);
      
      await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          page_visited: page,
          click_count: 1,
        }),
      });
    } catch (err) {
      console.error('Failed to track page view:', err);
    }
  };

  useEffect(() => {
    // Track current page view
    trackPageView(window.location.pathname);
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center text-red-600 p-4">
        <p>{error}</p>
        <Button onClick={() => fetchAnalytics()} className="mt-2">
          Retry
        </Button>
      </div>
    );
  }

  if (!analytics) {
    return <div className="text-center p-4">No analytics data available</div>;
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Analytics Dashboard</h1>
        <div className="flex gap-2">
          <DatePicker
            placeholder="From Date"
            onChange={(date: string) => handleFilterChange({ ...filters, date_from: date })}
          />
          <DatePicker
            placeholder="To Date"
            onChange={(date: string) => handleFilterChange({ ...filters, date_to: date })}
          />
          <Button onClick={() => handleFilterChange({})}>Reset Filters</Button>
        </div>
      </div>

      {/* Key Metrics */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Page Views</CardTitle>
            <svg className="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analytics.total_views.toLocaleString()}</div>
            <p className="text-xs text-muted-foreground">All time page views</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Unique Visitors</CardTitle>
            <svg className="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{analytics.unique_visitors.toLocaleString()}</div>
            <p className="text-xs text-muted-foreground">Distinct IP addresses</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Avg. Views per Visitor</CardTitle>
            <svg className="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {analytics.unique_visitors > 0 
                ? (analytics.total_views / analytics.unique_visitors).toFixed(1)
                : '0'
              }
            </div>
            <p className="text-xs text-muted-foreground">Pages per visitor</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Bounce Rate</CardTitle>
            <svg className="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {analytics.unique_visitors > 0 
                ? ((analytics.unique_visitors - analytics.top_pages.length) / analytics.unique_visitors * 100).toFixed(1)
                : '0'
              }%
            </div>
            <p className="text-xs text-muted-foreground">Single page visits</p>
          </CardContent>
        </Card>
      </div>

      {/* Detailed Analytics */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Top Pages */}
        <Card>
          <CardHeader>
            <CardTitle>Top Pages</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {analytics.top_pages.map((page, index) => (
                <div key={page.page_visited} className="flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    <span className="text-sm font-medium text-muted-foreground">#{index + 1}</span>
                    <span className="text-sm">{page.page_visited}</span>
                  </div>
                  <span className="text-sm font-medium">{page.views.toLocaleString()}</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Device Breakdown */}
        <Card>
          <CardHeader>
            <CardTitle>Device Breakdown</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {analytics.device_breakdown.map((device) => (
                <div key={device.device_type} className="flex items-center justify-between">
                  <span className="text-sm capitalize">{device.device_type}</span>
                  <span className="text-sm font-medium">{device.count.toLocaleString()}</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Country Breakdown */}
      <Card>
        <CardHeader>
          <CardTitle>Top Countries</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {analytics.country_breakdown.map((country) => (
              <div key={country.country_code} className="text-center">
                <div className="text-lg font-bold">{country.country_code}</div>
                <div className="text-sm text-muted-foreground">{country.count.toLocaleString()}</div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default AnalyticsDashboard; 