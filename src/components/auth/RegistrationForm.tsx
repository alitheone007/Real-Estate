import React, { useState } from 'react';
import { useCountry } from '../../contexts/CountryContext'; // Import useCountry hook

interface RegistrationFormProps {
  userType: 'builder' | 'client' | 'influencer';
  // Add other props if needed, like redirection path after successful registration
}

const RegistrationForm: React.FC<RegistrationFormProps> = ({ userType }) => {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  const { selectedCountry } = useCountry(); // Get selectedCountry from context

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setSuccess(null);

    if (!selectedCountry) {
      setError('Please select a marketplace (country) on the home page first.');
      return;
    }

    setLoading(true);

    const registrationData = {
      name: name,
      email: email,
      password: password,
      role: userType, // Use the userType prop as the role
      country_id: selectedCountry.id,
    };

    console.log(`Submitting registration for ${userType}:`, registrationData);

    try {
      // Adjust the URL based on your local XAMPP setup and project directory
      // Assuming your project is in htdocs/Real-Estate and Apache is on port 81
      const response = await fetch('http://localhost/Real-Estate/api/auth/register.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(registrationData),
      });

      const responseData = await response.json();

      if (!response.ok) {
        // Handle HTTP errors (e.g., 400, 409, 500)
        setError(responseData.error || `Registration failed with status: ${response.status}`);
      } else {
        // Handle success (e.g., 201 Created)
        setSuccess(responseData.message || 'Registration successful!');
        // TODO: Redirect user to login page or dashboard
        setName('');
        setEmail('');
        setPassword('');
      }

    } catch (err: any) {
      // Handle network errors or other exceptions
      setError('An error occurred during registration. Please try again.');
      console.error("Registration error:", err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <div>
        <label htmlFor="name" className="block text-sm font-medium text-gray-700">Name</label>
        <input 
          id="name" 
          name="name" 
          type="text" 
          required 
          value={name} 
          onChange={(e) => setName(e.target.value)}
          className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        />
      </div>

      <div>
        <label htmlFor="email" className="block text-sm font-medium text-gray-700">Email address</label>
        <input 
          id="email" 
          name="email" 
          type="email" 
          autoComplete="email" 
          required 
          value={email} 
          onChange={(e) => setEmail(e.target.value)}
          className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        />
      </div>

      <div>
        <label htmlFor="password" className="block text-sm font-medium text-gray-700">Password</label>
        <input 
          id="password" 
          name="password" 
          type="password" 
          autoComplete="new-password" 
          required 
          value={password} 
          onChange={(e) => setPassword(e.target.value)}
          className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
        />
      </div>

      <div>
        <button 
          type="submit" 
          disabled={loading || !selectedCountry} // Disable button if loading or no country selected
          className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
        >
          {loading ? 'Registering...' : 'Register'}
        </button>
      </div>

      {/* Display messages */} 
      {error && <p className="text-center text-red-600 text-sm">{error}</p>}
      {success && <p className="text-center text-green-600 text-sm">{success}</p>}
      {!selectedCountry && (
         <p className="text-center text-red-500 text-sm">Please select a marketplace on the home page first.</p>
      )}
    </form>
  );
};

export default RegistrationForm; 