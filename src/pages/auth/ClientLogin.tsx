import LoginForm from "@/components/auth/LoginForm";
import { Link } from 'react-router-dom';

const ClientLogin = () => {
  return (
    <>
      <LoginForm 
        userType="client"
        title="Client Login"
        subtitle="Welcome back! Please enter your details to access your account."
        redirectPath="/client/dashboard"
        backgroundImage="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTB8fHJlYWwlMjBlc3RhdGV8ZW58MHx8MHx8&auto=format&fit=crop&w=1200&q=80"
      />
      <div className="text-center mt-4">
        Don't have an account? <Link to="/client-registration" className="text-blue-600 hover:underline">Sign Up</Link>
      </div>
    </>
  );
};

export default ClientLogin; 