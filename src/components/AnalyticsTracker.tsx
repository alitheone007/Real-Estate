import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import { sendAnalyticsEvent, sendUserActivity } from '../lib/utils';

const AnalyticsTracker: React.FC = () => {
  const location = useLocation();
  useEffect(() => {
    const page = location.pathname + location.search;
    const userAgent = navigator.userAgent;
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    sendAnalyticsEvent({
      page_visited: page,
      user_agent: userAgent,
      timezone,
      session_duration: 0,
      click_count: 1,
    });
    sendUserActivity({
      last_active: new Date().toISOString(),
      session_duration: 0,
      pages_visited: 1,
    });
  }, [location]);
  return null;
};

export default AnalyticsTracker; 