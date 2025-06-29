import React, { useState } from 'react';

const apiList = [
  { name: 'User Analytics', url: '/api/analytics/track.php', method: 'POST' },
  { name: 'User Activity', url: '/api/analytics/activity.php', method: 'POST' },
  { name: 'Marketplace Status', url: '/api/timezone/status.php', method: 'GET' },
  { name: 'Feedback Tickets', url: '/api/feedback/tickets.php', method: 'POST/GET' },
  { name: 'Ticket Responses', url: '/api/feedback/responses.php', method: 'POST/GET' },
  { name: 'Inactivity Notifications', url: '/api/notification/inactivity.php', method: 'POST/GET' },
  { name: 'WhatsApp Integration', url: '/api/notification/whatsapp.php', method: 'POST/GET' },
  { name: 'Campaign Tracking', url: '/api/campaigns/track.php', method: 'POST/GET' },
  { name: 'KYC', url: '/api/auth/kyc.php', method: 'POST/GET' },
];

const AdminAPIsPage: React.FC = () => {
  const [selectedAPI, setSelectedAPI] = useState(apiList[0]);
  const [response, setResponse] = useState<any>(null);
  const [file, setFile] = useState<File | null>(null);
  const [template, setTemplate] = useState('');
  const [bulkStatus, setBulkStatus] = useState<string | null>(null);

  const handleTestAPI = async () => {
    let res;
    if (selectedAPI.method.startsWith('GET')) {
      res = await fetch(selectedAPI.url);
    } else {
      res = await fetch(selectedAPI.url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: '{}' });
    }
    setResponse(await res.json());
  };

  const handleBulkUpload = async () => {
    if (!file) return setBulkStatus('Please select a file.');
    setBulkStatus('Uploading...');
    const formData = new FormData();
    formData.append('file', file);
    formData.append('template', template);
    // Example: send to a bulk messaging endpoint (to be implemented)
    const res = await fetch('/api/admin/bulk_message.php', { method: 'POST', body: formData });
    const data = await res.json();
    setBulkStatus(data.message || (data.success ? 'Bulk messages sent!' : 'Failed'));
  };

  return (
    <div className="admin-apis-page p-6">
      <h2>API Connected Section</h2>
      <div className="mb-4">
        <label>Select API: </label>
        <select value={selectedAPI.url} onChange={e => setSelectedAPI(apiList.find(a => a.url === e.target.value) || apiList[0])}>
          {apiList.map(api => (
            <option key={api.url} value={api.url}>{api.name} ({api.method})</option>
          ))}
        </select>
        <button className="ml-2 px-3 py-1 bg-blue-600 text-white rounded" onClick={handleTestAPI}>Test API</button>
      </div>
      {response && (
        <div className="bg-gray-100 p-2 rounded mb-4">
          <strong>Response:</strong>
          <pre className="overflow-x-auto text-xs">{JSON.stringify(response, null, 2)}</pre>
        </div>
      )}
      <h3>Bulk Messaging</h3>
      <div className="mb-2">
        <input type="file" accept=".csv,.json" onChange={e => setFile(e.target.files?.[0] || null)} />
      </div>
      <div className="mb-2">
        <textarea placeholder="Message Template (use {{name}} etc.)" value={template} onChange={e => setTemplate(e.target.value)} className="w-full border p-2 rounded" />
      </div>
      <button className="px-3 py-1 bg-green-600 text-white rounded" onClick={handleBulkUpload}>Send Bulk Messages</button>
      {bulkStatus && <div className="mt-2 text-sm">{bulkStatus}</div>}
    </div>
  );
};

export default AdminAPIsPage; 