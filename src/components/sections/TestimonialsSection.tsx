import { Star } from "lucide-react";

const testimonials = [
  {
    quote:
      "RealtyFlow Pro has transformed how we work with influencers. The platform makes it easy to find the right creators and track campaign performance.",
    author: "Sarah Johnson",
    role: "Marketing Director",
    company: "Luxury Homes Inc.",
    rating: 5,
  },
  {
    quote:
      "As a real estate influencer, this platform has helped me connect with amazing properties and earn more from my content. The analytics are invaluable.",
    author: "Michael Chen",
    role: "Real Estate Influencer",
    company: "@michaelchenrealty",
    rating: 5,
  },
  {
    quote:
      "The lead management system is fantastic. We've seen a 40% increase in qualified leads since using RealtyFlow Pro.",
    author: "David Rodriguez",
    role: "Sales Manager",
    company: "Urban Properties",
    rating: 5,
  },
];

const TestimonialsSection = () => {
  return (
    <section className="py-24 bg-realtyflow-cream">
      <div className="container mx-auto px-4">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-realtyflow-navy mb-4">
            Trusted by Real Estate Professionals
          </h2>
          <p className="text-gray-600 max-w-2xl mx-auto">
            See what our clients have to say about their experience with
            RealtyFlow Pro.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {testimonials.map((testimonial, index) => (
            <div
              key={index}
              className="bg-white p-8 rounded-lg shadow-sm hover:shadow-md transition-shadow"
            >
              <div className="flex mb-4">
                {[...Array(testimonial.rating)].map((_, i) => (
                  <Star
                    key={i}
                    className="w-5 h-5 text-realtyflow-gold fill-current"
                  />
                ))}
              </div>
              <blockquote className="text-gray-600 mb-6">
                "{testimonial.quote}"
              </blockquote>
              <div>
                <p className="font-semibold text-realtyflow-navy">
                  {testimonial.author}
                </p>
                <p className="text-sm text-gray-500">
                  {testimonial.role} at {testimonial.company}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default TestimonialsSection; 