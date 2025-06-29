import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { GradientButton } from "../ui/GradientButton";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "../ui/card";

const InfluencerRegistration = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    phone: "",
    instagram: "",
    tiktok: "",
    youtube: "",
    followers: "",
    bio: "",
  });
  const [error, setError] = useState("");

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");

    try {
      // TODO: Implement actual registration
      console.log("Registering influencer:", formData);
      navigate("/influencer-login");
    } catch (err) {
      setError("Registration failed. Please try again.");
    }
  };

  return (
    <div className="max-w-2xl mx-auto">
      <Card>
        <CardHeader>
          <CardTitle className="text-2xl font-bold text-realtyflow-navy">
            Influencer Registration
          </CardTitle>
          <CardDescription>
            Join our network of real estate influencers and start earning from your content.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label htmlFor="fullName" className="block text-sm font-medium text-gray-700">
                  Full Name
                </label>
                <input
                  type="text"
                  id="fullName"
                  name="fullName"
                  value={formData.fullName}
                  onChange={handleChange}
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                  required
                />
              </div>
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                  Email
                </label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                  required
                />
              </div>
              <div>
                <label htmlFor="phone" className="block text-sm font-medium text-gray-700">
                  Phone Number
                </label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                  required
                />
              </div>
              <div>
                <label htmlFor="followers" className="block text-sm font-medium text-gray-700">
                  Total Followers
                </label>
                <input
                  type="number"
                  id="followers"
                  name="followers"
                  value={formData.followers}
                  onChange={handleChange}
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                  required
                />
              </div>
            </div>

            <div className="space-y-4">
              <h3 className="text-lg font-medium text-realtyflow-navy">Social Media Profiles</h3>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label htmlFor="instagram" className="block text-sm font-medium text-gray-700">
                    Instagram
                  </label>
                  <input
                    type="text"
                    id="instagram"
                    name="instagram"
                    value={formData.instagram}
                    onChange={handleChange}
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    placeholder="@username"
                  />
                </div>
                <div>
                  <label htmlFor="tiktok" className="block text-sm font-medium text-gray-700">
                    TikTok
                  </label>
                  <input
                    type="text"
                    id="tiktok"
                    name="tiktok"
                    value={formData.tiktok}
                    onChange={handleChange}
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    placeholder="@username"
                  />
                </div>
                <div>
                  <label htmlFor="youtube" className="block text-sm font-medium text-gray-700">
                    YouTube
                  </label>
                  <input
                    type="text"
                    id="youtube"
                    name="youtube"
                    value={formData.youtube}
                    onChange={handleChange}
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    placeholder="Channel URL"
                  />
                </div>
              </div>
            </div>

            <div>
              <label htmlFor="bio" className="block text-sm font-medium text-gray-700">
                Bio
              </label>
              <textarea
                id="bio"
                name="bio"
                value={formData.bio}
                onChange={handleChange}
                rows={4}
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                placeholder="Tell us about yourself and your experience in real estate content creation..."
                required
              />
            </div>

            {error && (
              <p className="text-sm text-red-600">{error}</p>
            )}

            <GradientButton type="submit" className="w-full">
              Submit Application
            </GradientButton>
          </form>
        </CardContent>
      </Card>
    </div>
  );
};

export default InfluencerRegistration; 