import React, { createContext, useContext, useState, ReactNode } from 'react';

interface Country {
  id: number;
  name: string;
  code: string;
  flag_icon: string | null;
}

interface CountryContextType {
  selectedCountry: Country | null;
  setSelectedCountry: (country: Country | null) => void;
}

const CountryContext = createContext<CountryContextType | undefined>(undefined);

export const CountryProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [selectedCountry, setSelectedCountry] = useState<Country | null>(null);

  return (
    <CountryContext.Provider value={{ selectedCountry, setSelectedCountry }}>
      {children}
    </CountryContext.Provider>
  );
};

export const useCountry = () => {
  const context = useContext(CountryContext);
  if (context === undefined) {
    throw new Error('useCountry must be used within a CountryProvider');
  }
  return context;
}; 