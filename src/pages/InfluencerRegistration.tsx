import { useCountry } from '../contexts/CountryContext';
import RegistrationForm from '@/components/auth/RegistrationForm';

const InfluencerRegistrationPage = () => {
  const { selectedCountry } = useCountry();

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <h3 className="text-2xl font-bold text-center mb-4">Influencer Registration</h3>
        {selectedCountry && (
             <p className="text-center mt-2 mb-4 text-lg">Selected Marketplace: <span className="font-semibold">{selectedCountry.name}</span></p>
        )}
        {!selectedCountry && (
             <p className="text-center mt-2 mb-4 text-sm text-red-500">Please select a marketplace on the home page.</p>
        )}
        {/* The specific InfluencerRegistration component might contain the form or other content */}
        {/* For now, we place the generic form if needed, or keep the specific one */}
        {/* If InfluencerRegistration component contains the form, we would pass selectedCountry to it */}
        
        {/* Assuming InfluencerRegistration is the main container and we add the form here */}
        <div className="flex items-center justify-center">
          <div className="px-8 py-6 mt-4 text-left bg-white shadow-lg w-full max-w-md">
             <RegistrationForm userType="influencer" />
          </div>
        </div>

      </div>
    </div>
  );
};

export default InfluencerRegistrationPage; 