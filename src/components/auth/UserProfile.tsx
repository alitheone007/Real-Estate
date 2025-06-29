import React from 'react';
import KYCUpload from '../kyc/KYCUpload';
import CampaignTracker from '../campaign/CampaignTracker';
import NotificationCenter from '../notification/NotificationCenter';

interface UserProfileProps {
  user: { id: number; [key: string]: any };
}

const UserProfile: React.FC<UserProfileProps> = ({ user }) => {
  return (
    <div className="user-profile">
      <h2>User Profile</h2>
      {/* Other profile info here */}
      <KYCUpload userId={user.id} />
      <CampaignTracker userId={user.id} />
      <NotificationCenter userId={user.id} />
    </div>
  );
};

export default UserProfile; 