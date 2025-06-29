import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';

interface MarketplaceStatus {
  current_status: 'operational' | 'non-operational' | 'limited';
  current_time_local: string;
  next_operational_time: string | null;
  status_message: string;
  country_name: string;
  country_timezone: string;
  currency_code: string;
  currency_symbol: string;
}

const MarketplaceStatus: React.FC = () => {
  const [status, setStatus] = useState<MarketplaceStatus | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchMarketplaceStatus = async () => {
    try {
      setLoading(true);
      const response = await fetch('/api/timezone/status');
      const data = await response.json();

      if (data.success) {
        setStatus(data.data);
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to fetch marketplace status');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchMarketplaceStatus();
    
    // Update status every minute
    const interval = setInterval(fetchMarketplaceStatus, 60000);
    
    return () => clearInterval(interval);
  }, []);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'operational':
        return 'text-green-600 bg-green-100';
      case 'non-operational':
        return 'text-red-600 bg-red-100';
      case 'limited':
        return 'text-yellow-600 bg-yellow-100';
      default:
        return 'text-gray-600 bg-gray-100';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'operational':
        return (
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
          </svg>
        );
      case 'non-operational':
        return (
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
          </svg>
        );
      case 'limited':
        return (
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        );
      default:
        return null;
    }
  };

  const formatTime = (timeString: string) => {
    if (!timeString) return '';
    const time = new Date(`2000-01-01T${timeString}`);
    return time.toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit',
      hour12: true 
    });
  };

  const formatNextOperationalTime = (dateTimeString: string | null) => {
    if (!dateTimeString) return '';
    const date = new Date(dateTimeString);
    return date.toLocaleString('en-US', {
      weekday: 'long',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    });
  };

  if (loading) {
    return (
      <Card>
        <CardContent className="flex items-center justify-center h-32">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </CardContent>
      </Card>
    );
  }

  if (error) {
    return (
      <Card>
        <CardContent className="text-center text-red-600 p-4">
          <p>{error}</p>
          <button 
            onClick={fetchMarketplaceStatus}
            className="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Retry
          </button>
        </CardContent>
      </Card>
    );
  }

  if (!status) {
    return (
      <Card>
        <CardContent className="text-center p-4">
          No marketplace status available
        </CardContent>
      </Card>
    );
  }

  return (
    <div className="space-y-4">
      {/* Main Status Card */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center justify-between">
            <span>Marketplace Status</span>
            <div className={`flex items-center space-x-2 px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(status.current_status)}`}>
              {getStatusIcon(status.current_status)}
              <span className="capitalize">{status.current_status}</span>
            </div>
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h3 className="text-sm font-medium text-gray-500">Location</h3>
              <p className="text-lg font-semibold">{status.country_name}</p>
              <p className="text-sm text-gray-600">{status.country_timezone}</p>
            </div>
            <div>
              <h3 className="text-sm font-medium text-gray-500">Current Time</h3>
              <p className="text-lg font-semibold">{formatTime(status.current_time_local)}</p>
              <p className="text-sm text-gray-600">Local Time</p>
            </div>
          </div>
          
          <div className="border-t pt-4">
            <p className="text-sm text-gray-600">{status.status_message}</p>
          </div>

          {status.next_operational_time && status.current_status === 'non-operational' && (
            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <h4 className="text-sm font-medium text-blue-800">Next Opening</h4>
              <p className="text-blue-700">{formatNextOperationalTime(status.next_operational_time)}</p>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Currency Information */}
      <Card>
        <CardHeader>
          <CardTitle>Local Currency</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex items-center space-x-2">
            <span className="text-2xl">{status.currency_symbol}</span>
            <span className="text-lg font-medium">{status.currency_code}</span>
          </div>
          <p className="text-sm text-gray-600 mt-2">
            All prices are displayed in local currency
          </p>
        </CardContent>
      </Card>

      {/* Operational Hours */}
      <Card>
        <CardHeader>
          <CardTitle>Operational Hours</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-2">
            <div className="flex justify-between items-center">
              <span className="text-sm">Monday - Friday</span>
              <span className="text-sm font-medium">9:00 AM - 6:00 PM</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-sm">Saturday</span>
              <span className="text-sm font-medium">10:00 AM - 4:00 PM</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-sm">Sunday</span>
              <span className="text-sm font-medium text-red-600">Closed</span>
            </div>
          </div>
          
          <div className="mt-4 p-3 bg-gray-50 rounded-lg">
            <p className="text-xs text-gray-600">
              * Hours may vary during holidays. Contact support for specific inquiries.
            </p>
          </div>
        </CardContent>
      </Card>

      {/* Quick Actions */}
      <Card>
        <CardHeader>
          <CardTitle>Quick Actions</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 gap-2">
            <button className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
              Contact Support
            </button>
            <button className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
              Schedule Call
            </button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default MarketplaceStatus; 