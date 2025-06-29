import React, { useState, useEffect } from 'react';
import { useCountry } from '../contexts/CountryContext';

interface Country {
  id: number;
  name: string;
  code: string;
  flag_icon: string | null;
}

async function fetchMarketplaceStatus(country_id: number) {
  const res = await fetch(`/api/timezone/status.php?country_id=${country_id}`);
  return await res.json();
}

const CountrySelector: React.FC = () => {
  const [countries, setCountries] = useState<Country[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const { selectedCountry, setSelectedCountry } = useCountry(); // Use context instead of local state
  const [marketplaceStatus, setMarketplaceStatus] = useState<any>(null);

  useEffect(() => {
    const fetchCountries = async () => {
      try {
        // Adjust the URL based on your local XAMPP setup and project directory
        // Assuming your project is in htdocs/Real-Estate and Apache is on port 81
        // Ensure your flag images are accessible, e.g., in public/assets/flags
        const response = await fetch('http://localhost:80/Real-Estate/api/countries/list.php');
        
        if (!response.ok) {
          // Handle HTTP errors
          const errorData = await response.json();
          throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        const data: Country[] = await response.json();
        setCountries(data);
      } catch (error: any) {
        setError(error.message);
        console.error("Error fetching countries:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchCountries();
  }, []); // Empty dependency array means this effect runs once on mount

  const handleCountrySelect = async (countryId: number) => {
    const status = await fetchMarketplaceStatus(countryId);
    setMarketplaceStatus(status);
  };

  if (loading) {
    return <div className="text-center py-8">Loading countries...</div>;
  }

  if (error) {
    return <div className="text-center py-8 text-red-600">Error: {error}</div>;
  }

  // Rendering countries with flags and styling
  return (
    <section className="country-selector-section bg-gray-50 py-16">
      <div className="container mx-auto px-4">
        <h2 className="text-4xl font-extrabold text-center text-gray-800 mb-10">Select Your Marketplace</h2>
        <p className="text-center text-gray-600 mb-12">Choose a country to tailor your experience.</p>
        
        <div className="flex flex-wrap justify-center items-center gap-6">
          {countries.map((country) => (
            <div 
              key={country.id} 
              className={`flex flex-col items-center p-6 border-2 rounded-lg shadow-lg cursor-pointer 
                         transition-transform transform hover:scale-105 hover:shadow-xl 
                         ${selectedCountry?.id === country.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200 bg-white'}`}
              onClick={() => {
                setSelectedCountry(country);
                handleCountrySelect(country.id);
              }}
            >
              {country.flag_icon && (
                <img 
                  src={`http://localhost:80/Real-Estate/assets/flags/${country.flag_icon}`} 
                  alt={`${country.name} flag`} 
                  className="w-12 h-12 object-cover rounded-full mb-4 border border-gray-300"
                />
              )}
              <span className="text-lg font-semibold text-gray-700">{country.name}</span>
            </div>
          ))}
        </div>
        
        {selectedCountry && (
          <div className="text-center mt-12 text-xl font-semibold text-gray-800">
            You have selected: <span className="text-blue-600">{selectedCountry.name}</span>
          </div>
        )}

        {marketplaceStatus && (
          <div className="marketplace-status">
            <strong>Status:</strong> {marketplaceStatus.marketplace_status?.current_status || 'Unknown'}<br />
            <strong>Operational Hours:</strong> {marketplaceStatus.operational_hours?.operational_start} - {marketplaceStatus.operational_hours?.operational_end}
          </div>
        )}
      </div>
    </section>
  );
};

export default CountrySelector; 