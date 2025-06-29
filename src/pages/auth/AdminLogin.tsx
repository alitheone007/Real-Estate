import LoginForm from "@/components/auth/LoginForm";

const AdminLogin = () => {
  return (
    <LoginForm 
      userType="admin"
      title="Admin Panel"
      subtitle="System administration and analytics dashboard."
      redirectPath="/admin"
      backgroundImage="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8NHx8ZGF0YSUyMGFuYWx5dGljc3xlbnwwfHwwfHw%3D&auto=format&fit=crop&w=1200&q=80"
    />
  );
};

export default AdminLogin; 