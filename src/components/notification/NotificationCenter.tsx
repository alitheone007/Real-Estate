import React, { useEffect, useState } from 'react';

const NotificationCenter: React.FC<{ userId: number }> = ({ userId }) => {
  const [inactivity, setInactivity] = useState<any[]>([]);
  const [whatsapp, setWhatsapp] = useState<any>(null);

  useEffect(() => {
    fetch(`/api/notification/inactivity.php?user_id=${userId}`)
      .then(res => res.json())
      .then(data => setInactivity(data.notifications || []));
    fetch(`/api/notification/whatsapp.php?user_id=${userId}`)
      .then(res => res.json())
      .then(data => setWhatsapp(data.whatsapp || null));
  }, [userId]);

  return (
    <div className="notification-center">
      <h3>Notifications</h3>
      <h4>Inactivity</h4>
      <ul className="max-h-32 overflow-y-auto text-sm">
        {inactivity.length === 0 && <li>No inactivity notifications.</li>}
        {inactivity.map((n, idx) => (
          <li key={idx} className="border-b py-1">
            <strong>{n.notification_type}</strong> - Sent: {n.sent_at}, Read: {n.is_read ? 'Yes' : 'No'}
          </li>
        ))}
      </ul>
      <h4>WhatsApp</h4>
      {whatsapp ? (
        <div>
          <strong>Number:</strong> {whatsapp.phone_number}<br />
          <strong>Verified:</strong> {whatsapp.is_verified ? 'Yes' : 'No'}<br />
          <strong>Last Sync:</strong> {whatsapp.last_sync}
        </div>
      ) : (
        <div>No WhatsApp integration info.</div>
      )}
    </div>
  );
};

export default NotificationCenter; 