import { useNavigate } from "react-router-dom";
import { ArrowRight } from "lucide-react";
import { GradientButton } from "../ui/GradientButton";
import { useState } from "react";
import YouTube from "react-youtube";
import { useCountry } from "@/contexts/CountryContext";

const HeroSection = () => {
  const [showDemo, setShowDemo] = useState(false);
  const { selectedCountry } = useCountry();
  const navigate = useNavigate();
  const [showPrompt, setShowPrompt] = useState(false);
  return (
    <section className="relative bg-realtyflow-cream overflow-hidden">
      <div className="container mx-auto px-4 py-24">
        <div className="max-w-4xl mx-auto text-center">
          <h1 className="text-4xl md:text-6xl font-bold text-realtyflow-navy mb-6">
            Transform Your Real Estate Marketing with{" "}
            <span className="text-realtyflow-gold">Influencer Power</span>
          </h1>
          <p className="text-xl text-gray-600 mb-8">
            Connect with top influencers, manage campaigns, and drive more leads
            with our all-in-one real estate marketing platform.
          </p>
          <div className="flex flex-col sm:flex-row justify-center gap-4">
            <GradientButton
              size="lg"
              className="group"
              onClick={() => {
                if (selectedCountry) {
                  navigate('/login');
                } else {
                  setShowPrompt(true);
                }
              }}
            >
              Get Started
              <ArrowRight className="inline-block ml-2 group-hover:translate-x-1 transition-transform" size={20} />
            </GradientButton>
            <GradientButton variant="secondary" size="lg" onClick={() => setShowDemo(true)}>
              Watch Explainer Demo
            </GradientButton>
          </div>
          {showPrompt && (
            <div className="mt-4 text-red-600 text-lg font-semibold">Please select a marketplace (country) below before getting started.</div>
          )}
        </div>
      </div>

      {/* Modal for Demo Video */}
      {showDemo && (
        <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg p-4 max-w-2xl w-full relative">
            <button
              className="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl"
              onClick={() => setShowDemo(false)}
              aria-label="Close"
            >
              &times;
            </button>
            <div className="aspect-w-16 aspect-h-9 w-full">
              <YouTube
                videoId="ZK-rNEhJIDs"
                opts={{ width: "100%", height: "400" }}
                onEnd={() => setShowDemo(false)}
              />
            </div>
            <div className="mt-4 text-center text-gray-700">
              <p>This is a sample explainer video. Replace with your own video as needed.</p>
            </div>
          </div>
        </div>
      )}

      {/* Background Pattern */}
      <div className="absolute inset-0 -z-10 opacity-10">
        <div className="absolute inset-0 bg-[linear-gradient(to_right,#1a365d_1px,transparent_1px),linear-gradient(to_bottom,#1a365d_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
      </div>
    </section>
  );
};

export default HeroSection; 