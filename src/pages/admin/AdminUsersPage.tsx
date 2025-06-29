import React, { useState, useEffect } from 'react';
import DashboardLayout from '@/components/dashboard/DashboardLayout';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
  country_id?: number;
  country_name?: string;
}

interface Country {
  id: number;
  name: string;
  code: string;
  flag_icon: string | null;
}

const roles = ['admin', 'agent', 'builder', 'client', 'influencer']; // Allowed roles

const AdminUsersPage: React.FC = () => {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showAdd, setShowAdd] = useState(false); // ADDED: modal state
  const [countries, setCountries] = useState<Country[]>([]); // ADDED: country list
  const [addForm, setAddForm] = useState({
    name: '',
    email: '',
    password: '',
    role: 'client',
    country_id: '',
  });
  const [addError, setAddError] = useState<string | null>(null);
  const [addLoading, setAddLoading] = useState(false);

    const fetchUsers = async () => {
    setLoading(true);
      try {
        const response = await fetch('http://localhost/Real-Estate/api/admin/users.php');
      if (!response.ok) throw new Error('Failed to fetch users');
        const result = await response.json();
      if (result.success) setUsers(result.data);
      else throw new Error(result.message || 'Failed to fetch users');
      } catch (err: any) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

  const fetchCountries = async () => {
    try {
      const response = await fetch('http://localhost/Real-Estate/api/countries/list.php');
      if (!response.ok) throw new Error('Failed to fetch countries');
      const data = await response.json();
      setCountries(data);
    } catch (err) {
      setCountries([]);
    }
  };

  useEffect(() => {
    fetchUsers();
    fetchCountries();
  }, []);

  const handleDelete = async (id: number, role: string) => {
    if (role === 'admin') return;
    if (!window.confirm('Are you sure you want to delete this user?')) return;
    try {
      const response = await fetch('http://localhost/Real-Estate/api/admin/users.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id }),
      });
      const result = await response.json();
      if (result.success) fetchUsers();
      else alert(result.message || 'Failed to delete user');
    } catch (err) {
      alert('Failed to delete user');
    }
  };

  const handleAddUser = async (e: React.FormEvent) => {
    e.preventDefault();
    setAddError(null);
    setAddLoading(true);
    try {
      const response = await fetch('http://localhost/Real-Estate/api/admin/users.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...addForm,
          country_id: Number(addForm.country_id),
        }),
      });
      const result = await response.json();
      if (result.success) {
        setShowAdd(false);
        setAddForm({ name: '', email: '', password: '', role: 'client', country_id: '' });
        fetchUsers();
      } else {
        setAddError(result.message || 'Failed to add user');
      }
    } catch (err) {
      setAddError('Failed to add user');
    } finally {
      setAddLoading(false);
    }
  };

  if (loading) {
    return (
      <DashboardLayout userType="admin">
        <div className="flex items-center justify-center h-full">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-realtyflow-navy mx-auto"></div>
            <p className="mt-4 text-gray-600">Loading users...</p>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  if (error) {
    return (
      <DashboardLayout userType="admin">
        <div className="flex items-center justify-center h-full">
          <div className="text-center">
            <p className="text-red-600">Error: {error}</p>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout userType="admin">
      <div className="space-y-6">
        <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-gray-900">Users</h1>
          <button
            className="bg-realtyflow-navy text-white px-4 py-2 rounded shadow hover:bg-realtyflow-gold transition"
            onClick={() => setShowAdd(true)}
          >
            Add User
          </button>
        </div>
        <div className="bg-white shadow-md rounded-lg overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                <th className="px-6 py-3"></th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {users.map((user) => (
                <tr key={user.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.id}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{user.name}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.email}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.role}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.country_name || '-'}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.created_at}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <button
                      className={`px-3 py-1 rounded ${user.role === 'admin' ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-red-500 text-white hover:bg-red-700'}`}
                      disabled={user.role === 'admin'}
                      onClick={() => handleDelete(user.id, user.role)}
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Add User Modal */}
        {showAdd && (
          <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 max-w-md w-full relative">
              <button
                className="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl"
                onClick={() => setShowAdd(false)}
                aria-label="Close"
              >
                &times;
              </button>
              <h2 className="text-xl font-bold mb-4">Add User</h2>
              <form onSubmit={handleAddUser} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">Name</label>
                  <input
                    type="text"
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    value={addForm.name}
                    onChange={e => setAddForm(f => ({ ...f, name: e.target.value }))}
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    type="email"
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    value={addForm.email}
                    onChange={e => setAddForm(f => ({ ...f, email: e.target.value }))}
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Password</label>
                  <input
                    type="password"
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    value={addForm.password}
                    onChange={e => setAddForm(f => ({ ...f, password: e.target.value }))}
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Role</label>
                  <select
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    value={addForm.role}
                    onChange={e => setAddForm(f => ({ ...f, role: e.target.value }))}
                    required
                  >
                    {roles.map(role => (
                      <option key={role} value={role}>{role.charAt(0).toUpperCase() + role.slice(1)}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Country</label>
                  <select
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-realtyflow-navy focus:outline-none focus:ring-1 focus:ring-realtyflow-navy"
                    value={addForm.country_id}
                    onChange={e => setAddForm(f => ({ ...f, country_id: e.target.value }))}
                    required
                  >
                    <option value="">Select country</option>
                    {countries.map(country => (
                      <option key={country.id} value={country.id}>{country.name}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <button
                    type="submit"
                    className="w-full bg-realtyflow-navy text-white py-2 rounded hover:bg-realtyflow-gold transition"
                    disabled={addLoading}
                  >
                    {addLoading ? 'Adding...' : 'Add User'}
                  </button>
                </div>
                {addError && <p className="text-red-600 text-sm text-center">{addError}</p>}
              </form>
            </div>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
};

export default AdminUsersPage; 