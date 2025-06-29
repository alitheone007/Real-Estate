import React from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { TrendingUp, TrendingDown } from 'lucide-react';

interface StatCardProps {
  title: string;
  value: string | number;
  icon: React.ReactNode;
  trend?: 'up' | 'down';
  trendValue?: string;
}

export const StatCard: React.FC<StatCardProps> = ({
  title,
  value,
  icon,
  trend,
  trendValue,
}) => {
  return (
    <Card>
      <CardContent className="p-6">
        <div className="flex items-center justify-between">
          <div>
            <p className="text-sm font-medium text-gray-600">{title}</p>
            <p className="text-2xl font-semibold text-gray-900 mt-1">{value}</p>
            {trend && trendValue && (
              <div className="flex items-center mt-1">
                {trend === 'up' ? (
                  <TrendingUp className="h-4 w-4 text-green-600" />
                ) : (
                  <TrendingDown className="h-4 w-4 text-red-600" />
                )}
                <span
                  className={`text-sm ml-1 ${
                    trend === 'up' ? 'text-green-600' : 'text-red-600'
                  }`}
                >
                  {trendValue}
                </span>
              </div>
            )}
          </div>
          <div className="p-3 bg-gray-50 rounded-full">{icon}</div>
        </div>
      </CardContent>
    </Card>
  );
}; 