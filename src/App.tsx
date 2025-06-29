import { createBrowserRouter, RouterProvider, Outlet } from 'react-router-dom';
import Index from './pages/Index';
import LoginSelection from './pages/LoginSelection';
import AdminLogin from './pages/auth/AdminLogin';
import BuilderLogin from './pages/auth/BuilderLogin';
import ClientLogin from './pages/auth/ClientLogin';
import InfluencerLogin from './pages/auth/InfluencerLogin';
import AgentLogin from './pages/auth/AgentLogin';
import BuilderRegistration from './pages/auth/BuilderRegistration';
import ClientRegistration from './pages/auth/ClientRegistration';
import InfluencerRegistration from './pages/InfluencerRegistration';
import AgentRegistration from './pages/auth/AgentRegistration';
import AdminDashboard from './pages/admin/AdminDashboard';
import BuilderDashboard from './pages/builder/BuilderDashboard';
import ClientDashboard from './pages/client/ClientDashboard';
import InfluencerDashboard from './pages/influencer/InfluencerDashboard';
import AgentDashboard from './pages/agent/AgentDashboard';
import SocialMediaManager from './pages/influencer/SocialMediaManager';
import { CountryProvider } from './contexts/CountryContext';
import AdminUsersPage from './pages/admin/AdminUsersPage';
import AdminAnalyticsPage from './pages/admin/AdminAnalyticsPage';
import AdminSettingsPage from './pages/admin/AdminSettingsPage';
import Demo from './pages/Demo';
import FeedbackWidget from './components/feedback/FeedbackWidget';
import AnalyticsTracker from './components/AnalyticsTracker';

// Define a layout component that includes AnalyticsTracker and FeedbackWidget
function AppLayout() {
  return (
    <>
      <AnalyticsTracker />
      <FeedbackWidget />
      <Outlet />
    </>
  );
}

const router = createBrowserRouter([
  {
    path: '/',
    element: <AppLayout />,
    children: [
      { index: true, element: <Index /> },
      { path: 'login', element: <LoginSelection /> },
      { path: 'admin-login', element: <AdminLogin /> },
      { path: 'builder-login', element: <BuilderLogin /> },
      { path: 'client-login', element: <ClientLogin /> },
      { path: 'influencer-login', element: <InfluencerLogin /> },
      { path: 'agent-login', element: <AgentLogin /> },
      { path: 'builder-registration', element: <BuilderRegistration /> },
      { path: 'client-registration', element: <ClientRegistration /> },
      { path: 'influencer-registration', element: <InfluencerRegistration /> },
      { path: 'agent-registration', element: <AgentRegistration /> },
      { path: 'admin', element: <AdminDashboard /> },
      { path: 'admin/users', element: <AdminUsersPage /> },
      { path: 'admin/analytics', element: <AdminAnalyticsPage /> },
      { path: 'admin/settings', element: <AdminSettingsPage /> },
      { path: 'builder', element: <BuilderDashboard /> },
      { path: 'client', element: <ClientDashboard /> },
      { path: 'influencer', element: <InfluencerDashboard /> },
      { path: 'agent', element: <AgentDashboard /> },
      { path: 'influencer/social-media', element: <SocialMediaManager /> },
      { path: 'demo', element: <Demo /> },
      { path: '*', element: <Index /> },
    ],
  },
]);

function App() {
  return (
    <CountryProvider>
      <RouterProvider router={router} />
    </CountryProvider>
  );
}

export default App; 