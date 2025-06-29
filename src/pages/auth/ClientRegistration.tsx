import { useCountry } from '../../contexts/CountryContext';
import RegistrationForm from '@/components/auth/RegistrationForm';

const ClientRegistration = () => {
  const { selectedCountry } = useCountry();

  return (
    <div className="flex items-center justify-center min-h-screen bg-gray-100">
      <div className="px-8 py-6 mt-4 text-left bg-white shadow-lg">
        <h3 className="text-2xl font-bold text-center">Client Registration</h3>
         {selectedCountry && (
          <p className="text-center mt-2 mb-4 text-lg">Selected Marketplace: <span className="font-semibold">{selectedCountry.name}</span></p>
        )}
        {!selectedCountry && (
             <p className="text-center mt-2 mb-4 text-sm text-red-500">Please select a marketplace on the home page.</p>
        )}
        
        <RegistrationForm userType="client" />
      </div>
    </div>
  );
};

export default ClientRegistration; 