import { useEffect } from "react";
import HeroSection from "@/components/sections/HeroSection";
import FeaturesSection from "@/components/sections/FeaturesSection";
import TestimonialsSection from "@/components/sections/TestimonialsSection";
import CountrySelector from "@/components/CountrySelector";

const Index = () => {
  useEffect(() => {
    document.title = "RealtyFlow Pro - Real Estate Marketing Platform";
  }, []);

  return (
    <div>
      <HeroSection />
      <CountrySelector />
      <FeaturesSection />
      <TestimonialsSection />
    </div>
  );
};

export default Index; 