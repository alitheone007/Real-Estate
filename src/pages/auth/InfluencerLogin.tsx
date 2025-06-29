import LoginForm from "@/components/auth/LoginForm";
import { Link } from 'react-router-dom';

const InfluencerLogin = () => {
  return (
    <>
      <LoginForm 
        userType="influencer"
        title="Influencer Portal"
        subtitle="Access your influencer dashboard to manage leads and campaigns."
        redirectPath="/influencer/dashboard"
        backgroundImage="https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8OHx8aW5mbHVlbmNlcnxlbnwwfHwwfHw%3D&auto=format&fit=crop&w=1200&q=80"
      />
      <div className="text-center mt-4">
        Don't have an account? <Link to="/influencer-registration" className="text-blue-600 hover:underline">Sign Up</Link>
      </div>
    </>
  );
};

export default InfluencerLogin; 