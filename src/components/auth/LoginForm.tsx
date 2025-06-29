import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { GradientButton } from "../ui/GradientButton";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "../ui/card";

interface LoginFormProps {
  userType: "admin" |"agent"| "builder" | "client" | "influencer";
  title: string;
  subtitle: string;
  redirectPath: string;
  backgroundImage: string;
}

const LoginForm = ({
  userType,
  title,
  subtitle,
  redirectPath,
  backgroundImage,
}: LoginFormProps) => {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    try {
      const response = await fetch('http://localhost:80/Real-Estate/api/auth/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email,
          password,
          role: userType,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        setError(data.message || 'Login failed');
      } else {
        if (data.success) {
            localStorage.setItem('user', JSON.stringify(data.user));
            navigate(redirectPath);
        } else {
            setError(data.message || 'Login failed');
        }
      }
    } catch (err: any) {
      console.error("Login error:", err);
      setError('An unexpected error occurred during login. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex">
      {/* Left side - Form */}
      <div className="flex-1 flex items-center justify-center p-8">
        <div className="max-w-md w-full">
          <Card>
            <CardHeader>
              <CardTitle className="text-2xl font-bold text-realtyflow-navy">
                {title}
              </CardTitle>
              <CardDescription>{subtitle}</CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <label
                    htmlFor="email"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Email
                  </label>
                  <input
                    type="email"
                    id="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    required
                  />
                </div>
                <div>
                  <label
                    htmlFor="password"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Password
                  </label>
                  <input
                    type="password"
                    id="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    required
                    autoComplete="current-password"
                  />
                </div>
                {error && (
                  <p className="text-sm text-red-600">{error}</p>
                )}
                <GradientButton 
                  type="submit" 
                  className="w-full"
                  disabled={loading}
                >
                  {loading ? 'Signing in...' : 'Sign In'}
                </GradientButton>
              </form>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Right side - Background Image */}
      <div
        className="hidden lg:block lg:w-1/2 bg-cover bg-center"
        style={{ backgroundImage: `url(${backgroundImage})` }}
      />
    </div>
  );
};

export default LoginForm; 