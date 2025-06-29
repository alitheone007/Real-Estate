import { Users, BarChart3, Target, MessageSquare, Shield, Zap } from "lucide-react";

const features = [
  {
    icon: <Users className="w-6 h-6" />,
    title: "Influencer Network",
    description: "Connect with verified real estate influencers and content creators.",
  },
  {
    icon: <BarChart3 className="w-6 h-6" />,
    title: "Analytics Dashboard",
    description: "Track campaign performance and ROI with detailed analytics.",
  },
  {
    icon: <Target className="w-6 h-6" />,
    title: "Lead Management",
    description: "Capture, qualify, and nurture leads from influencer campaigns.",
  },
  {
    icon: <MessageSquare className="w-6 h-6" />,
    title: "Campaign Collaboration",
    description: "Seamlessly collaborate with influencers on marketing campaigns.",
  },
  {
    icon: <Shield className="w-6 h-6" />,
    title: "Secure Payments",
    description: "Automated and secure payment processing for influencer commissions.",
  },
  {
    icon: <Zap className="w-6 h-6" />,
    title: "Quick Integration",
    description: "Easy integration with your existing CRM and marketing tools.",
  },
];

const FeaturesSection = () => {
  return (
    <section className="py-24 bg-white" id="features">
      <div className="container mx-auto px-4">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-realtyflow-navy mb-4">
            Powerful Features for Real Estate Marketing
          </h2>
          <p className="text-gray-600 max-w-2xl mx-auto">
            Everything you need to run successful influencer marketing campaigns
            and grow your real estate business.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <div
              key={index}
              className="p-6 rounded-lg border border-gray-100 hover:shadow-lg transition-shadow"
            >
              <div className="w-12 h-12 rounded-lg bg-realtyflow-navy text-white flex items-center justify-center mb-4">
                {feature.icon}
              </div>
              <h3 className="text-xl font-semibold text-realtyflow-navy mb-2">
                {feature.title}
              </h3>
              <p className="text-gray-600">{feature.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default FeaturesSection; 