import React, { useState, useEffect } from 'react';

const CampaignTracker: React.FC<{ userId: number }> = ({ userId }) => {
  const [form, setForm] = useState({
    campaign_id: '',
    influencer_id: '',
    property_id: '',
    event_type: 'click',
  });
  const [status, setStatus] = useState<string | null>(null);
  const [events, setEvents] = useState<any[]>([]);

  useEffect(() => {
    fetch(`/api/campaigns/track.php?user_id=${userId}`)
      .then(res => res.json())
      .then(data => setEvents(data.events || []));
  }, [userId, status]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus('Logging...');
    const res = await fetch('/api/campaigns/track.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...form, user_id: userId }),
    });
    const data = await res.json();
    setStatus(data.message || (data.success ? 'Logged!' : 'Failed'));
  };

  return (
    <div className="campaign-tracker">
      <h3>Campaign/Referral Tracker</h3>
      <form onSubmit={handleSubmit} className="space-y-2">
        <input type="text" placeholder="Campaign ID" value={form.campaign_id} onChange={e => setForm(f => ({ ...f, campaign_id: e.target.value }))} required className="w-full border p-2 rounded" />
        <input type="text" placeholder="Influencer ID" value={form.influencer_id} onChange={e => setForm(f => ({ ...f, influencer_id: e.target.value }))} className="w-full border p-2 rounded" />
        <input type="text" placeholder="Property ID" value={form.property_id} onChange={e => setForm(f => ({ ...f, property_id: e.target.value }))} className="w-full border p-2 rounded" />
        <select value={form.event_type} onChange={e => setForm(f => ({ ...f, event_type: e.target.value }))} className="w-full border p-2 rounded">
          <option value="click">Click</option>
          <option value="conversion">Conversion</option>
        </select>
        <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded">Log Event</button>
      </form>
      {status && <div className="mt-2 text-sm">{status}</div>}
      <h4>Recent Campaign Events</h4>
      <ul className="max-h-32 overflow-y-auto text-sm">
        {events.map((e, idx) => (
          <li key={idx} className="border-b py-1">
            <strong>{e.event_type}</strong> - Campaign: {e.campaign_id}, Influencer: {e.influencer_id}, Property: {e.property_id}, Time: {e.created_at}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default CampaignTracker; 