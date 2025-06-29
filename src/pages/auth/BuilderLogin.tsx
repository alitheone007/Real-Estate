import LoginForm from "@/components/auth/LoginForm";
import { Link } from 'react-router-dom';

const BuilderLogin = () => {
  return (
    <>
      <LoginForm 
        userType="builder"
        title="Builder & Developer Login"
        subtitle="Manage your property portfolio and track campaign performance."
        redirectPath="/builder/dashboard"
        backgroundImage="https://images.unsplash.com/photo-1486325212027-8081e485255e?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTB8fGNvbnN0cnVjdGlvbiUyMHNpdGV8ZW58MHx8MHx8&auto=format&fit=crop&w=1200&q=80"
      />
      <div className="text-center mt-4">
        Don't have an account? <Link to="/builder-registration" className="text-blue-600 hover:underline">Sign Up</Link>
      </div>
    </>
  );
};

export default BuilderLogin; 