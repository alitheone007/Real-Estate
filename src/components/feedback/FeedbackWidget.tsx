import React, { useState, useEffect } from 'react';

const FeedbackWidget: React.FC = () => {
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
    category: 'general',
    priority: 'medium',
  });
  const [status, setStatus] = useState<string | null>(null);
  const [tickets, setTickets] = useState<any[]>([]);
  const [userEmail, setUserEmail] = useState('');

  useEffect(() => {
    if (userEmail) {
      fetch(`/api/feedback/tickets.php?email=${encodeURIComponent(userEmail)}`)
        .then(res => res.json())
        .then(data => setTickets(data.tickets || []));
    }
  }, [userEmail, status]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus('Submitting...');
    const res = await fetch('/api/feedback/tickets.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form),
    });
    const data = await res.json();
    setStatus(data.message || (data.success ? 'Submitted!' : 'Failed'));
    setUserEmail(form.email);
  };

  return (
    <>
      <button className="fixed bottom-6 right-6 bg-blue-600 text-white rounded-full p-4 shadow-lg z-50" onClick={() => setOpen(true)}>
        Feedback
      </button>
      {open && (
        <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
          <div className="bg-white p-6 rounded shadow-lg w-full max-w-md relative">
            <button className="absolute top-2 right-2 text-gray-500" onClick={() => setOpen(false)}>&times;</button>
            <h3>Submit Feedback</h3>
            <form onSubmit={handleSubmit} className="space-y-2">
              <input type="text" placeholder="Name" value={form.name} onChange={e => setForm(f => ({ ...f, name: e.target.value }))} required className="w-full border p-2 rounded" />
              <input type="email" placeholder="Email" value={form.email} onChange={e => setForm(f => ({ ...f, email: e.target.value }))} required className="w-full border p-2 rounded" />
              <input type="text" placeholder="Subject" value={form.subject} onChange={e => setForm(f => ({ ...f, subject: e.target.value }))} required className="w-full border p-2 rounded" />
              <textarea placeholder="Message" value={form.message} onChange={e => setForm(f => ({ ...f, message: e.target.value }))} required className="w-full border p-2 rounded" />
              <select value={form.category} onChange={e => setForm(f => ({ ...f, category: e.target.value }))} className="w-full border p-2 rounded">
                <option value="general">General</option>
                <option value="bug">Bug</option>
                <option value="feature_request">Feature Request</option>
                <option value="support">Support</option>
              </select>
              <select value={form.priority} onChange={e => setForm(f => ({ ...f, priority: e.target.value }))} className="w-full border p-2 rounded">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
              <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
            </form>
            {status && <div className="mt-2 text-sm">{status}</div>}
            {tickets.length > 0 && (
              <div className="mt-4">
                <h4>Your Previous Tickets</h4>
                <ul className="max-h-32 overflow-y-auto text-sm">
                  {tickets.map((t, idx) => (
                    <li key={idx} className="border-b py-1">
                      <strong>{t.subject}</strong> ({t.status})<br />
                      {t.message}
                    </li>
                  ))}
                </ul>
              </div>
            )}
          </div>
        </div>
      )}
    </>
  );
};

export default FeedbackWidget; 