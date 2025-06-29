import React, { useEffect, useState } from 'react';

interface Campaign {
  id?: number;
  title: string;
  description?: string;
  discount_type?: string;
  discount_value?: number;
  start_date?: string;
  end_date?: string;
  creator_role?: string;
  status?: string;
}

interface Property {
  id: number;
  title: string;
}

const CampaignManager: React.FC<{ userRole: string; userId: number }> = ({ userRole, userId }) => {
  const [campaigns, setCampaigns] = useState<Campaign[]>([]);
  const [form, setForm] = useState<Campaign>({ title: '', discount_type: 'percentage', discount_value: 0, creator_role: userRole, status: 'active' });
  const [editingId, setEditingId] = useState<number | null>(null);
  const [properties, setProperties] = useState<Property[]>([]);
  const [selectedProperties, setSelectedProperties] = useState<number[]>([]);
  const [status, setStatus] = useState<string | null>(null);

  useEffect(() => {
    fetch('/api/campaigns/list.php?role=' + userRole)
      .then(res => res.json())
      .then(data => setCampaigns(data.campaigns || []));
    // Fetch all properties (for assignment)
    fetch('/api/properties/list.php')
      .then(res => res.json())
      .then(data => setProperties(data.properties || []));
  }, [userRole, status]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus('Saving...');
    const payload = { ...form, created_by: userId };
    const res = await fetch('/api/campaigns/create.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (data.success && selectedProperties.length > 0) {
      await fetch('/api/campaigns/assign_property.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ campaign_id: data.campaign_id, property_ids: selectedProperties }),
      });
    }
    setStatus(data.success ? 'Saved!' : 'Failed: ' + data.message);
    setForm({ title: '', discount_type: 'percentage', discount_value: 0, creator_role: userRole, status: 'active' });
    setEditingId(null);
    setSelectedProperties([]);
  };

  const handleEdit = (c: Campaign) => {
    setForm(c);
    setEditingId(c.id!);
    // Optionally fetch assigned properties for this campaign
  };

  return (
    <div className="campaign-manager">
      <h2 className="text-xl font-bold mb-4">Campaign Management</h2>
      <form onSubmit={handleSubmit} className="glass-card p-4 mb-6 space-y-2">
        <input type="text" placeholder="Title" value={form.title} onChange={e => setForm(f => ({ ...f, title: e.target.value }))} required className="w-full border p-2 rounded" />
        <textarea placeholder="Description" value={form.description || ''} onChange={e => setForm(f => ({ ...f, description: e.target.value }))} className="w-full border p-2 rounded" />
        <div className="flex gap-2">
          <select value={form.discount_type} onChange={e => setForm(f => ({ ...f, discount_type: e.target.value }))} className="border p-2 rounded">
            <option value="percentage">% Off</option>
            <option value="fixed">Fixed Amount</option>
            <option value="custom">Custom</option>
          </select>
          <input type="number" placeholder="Discount Value" value={form.discount_value} onChange={e => setForm(f => ({ ...f, discount_value: Number(e.target.value) }))} className="border p-2 rounded w-32" />
        </div>
        <div className="flex gap-2">
          <input type="date" value={form.start_date || ''} onChange={e => setForm(f => ({ ...f, start_date: e.target.value }))} className="border p-2 rounded" />
          <input type="date" value={form.end_date || ''} onChange={e => setForm(f => ({ ...f, end_date: e.target.value }))} className="border p-2 rounded" />
        </div>
        <div>
          <label className="block mb-1">Assign to Properties:</label>
          <select multiple value={selectedProperties.map(String)} onChange={e => setSelectedProperties(Array.from(e.target.selectedOptions, o => Number(o.value)))} className="w-full border p-2 rounded h-24">
            {properties.map(p => <option key={p.id} value={p.id}>{p.title}</option>)}
          </select>
        </div>
        <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded">{editingId ? 'Update' : 'Create'} Campaign</button>
        {status && <div className="mt-2 text-sm">{status}</div>}
      </form>
      <h3 className="font-semibold mb-2">Existing Campaigns</h3>
      <ul className="space-y-2">
        {campaigns.map(c => (
          <li key={c.id} className="glass-card p-3 flex justify-between items-center">
            <div>
              <div className="font-bold">{c.title}</div>
              <div className="text-sm text-gray-500">{c.description}</div>
              <div className="text-xs">{c.discount_type} {c.discount_value} | {c.status} | {c.start_date} - {c.end_date}</div>
            </div>
            <button onClick={() => handleEdit(c)} className="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default CampaignManager; 