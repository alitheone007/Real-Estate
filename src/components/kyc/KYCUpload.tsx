import React, { useState, useEffect } from 'react';

interface KYCUploadProps {
  userId: number;
}

const KYCUpload: React.FC<KYCUploadProps> = ({ userId }) => {
  const [documentType, setDocumentType] = useState('passport');
  const [file, setFile] = useState<File | null>(null);
  const [status, setStatus] = useState<string | null>(null);
  const [kycList, setKycList] = useState<any[]>([]);

  useEffect(() => {
    fetch(`/api/auth/kyc.php?user_id=${userId}`)
      .then(res => res.json())
      .then(data => setKycList(data.kyc || []));
  }, [userId, status]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!file) return setStatus('Please select a file.');
    const formData = new FormData();
    formData.append('user_id', String(userId));
    formData.append('document_type', documentType);
    formData.append('file', file);
    setStatus('Uploading...');
    const res = await fetch('/api/auth/kyc.php', {
      method: 'POST',
      body: formData,
    });
    const data = await res.json();
    setStatus(data.message || (data.success ? 'Uploaded!' : 'Failed'));
  };

  return (
    <div className="kyc-upload">
      <h3>KYC Verification</h3>
      <form onSubmit={handleSubmit}>
        <label>
          Document Type:
          <select value={documentType} onChange={e => setDocumentType(e.target.value)}>
            <option value="passport">Passport</option>
            <option value="id_card">ID Card</option>
            <option value="driver_license">Driver License</option>
          </select>
        </label>
        <label>
          Upload File:
          <input type="file" onChange={e => setFile(e.target.files?.[0] || null)} />
        </label>
        <button type="submit">Upload</button>
      </form>
      {status && <div className="kyc-status">{status}</div>}
      <h4>Previous KYC Submissions</h4>
      <ul>
        {kycList.map((item, idx) => (
          <li key={idx}>
            {item.document_type}: {item.file_name} - {item.status}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default KYCUpload; 