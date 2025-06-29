import { useState } from "react";

const Demo = () => {
  const [open, setOpen] = useState(true);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      {open && (
        <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg p-4 max-w-2xl w-full relative">
            <button
              className="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl"
              onClick={() => setOpen(false)}
              aria-label="Close"
            >
              &times;
            </button>
            <div className="aspect-w-16 aspect-h-9 w-full">
              <iframe
                width="560"
                height="315"
                src="https://www.youtube.com/embed/ZK-rNEhJIDs"
                title="Demo Explainer Video"
                frameBorder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen
                className="w-full h-96 rounded"
              ></iframe>
            </div>
            <div className="mt-4 text-center text-gray-700">
              <p>This is a sample explainer video. Replace with your own video as needed.</p>
            </div>
          </div>
        </div>
      )}
      <div className="text-center">
        <h1 className="text-3xl font-bold mb-4">Demo Page</h1>
        <button
          className="bg-realtyflow-navy text-white px-6 py-2 rounded shadow hover:bg-realtyflow-gold transition"
          onClick={() => setOpen(true)}
        >
          Watch Demo Video
        </button>
      </div>
    </div>
  );
};

export default Demo;
// NEW FILE: Demo page with explainer video modal 