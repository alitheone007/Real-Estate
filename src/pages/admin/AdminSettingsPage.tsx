// src/pages/admin/AdminSettingsPage.tsx
import React, { useState, useEffect } from 'react';
import DashboardLayout from '../../components/dashboard/DashboardLayout';
import { Card, CardContent } from '../../components/ui/card';
import { GradientButton } from '../../components/ui/GradientButton'; // Import GradientButton
import CampaignManager from '../../components/campaign/CampaignManager';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
  // Add country_id and country_name if needed for display/editing
  country_id?: number;
  country_name?: string;
}

const AdminSettingsPage: React.FC = () => {
  const [activeSetting, setActiveSetting] = useState<'general' | 'users' | 'privileges'>('general');

  // State for User Management section
  const [users, setUsers] = useState<User[]>([]);
  const [loadingUsers, setLoadingUsers] = useState(true);
  const [errorUsers, setErrorUsers] = useState<string | null>(null);
  const [editingUser, setEditingUser] = useState<User | null>(null); // State to track user being edited
  const [newRole, setNewRole] = useState(''); // State for the new role input
  const [savingUser, setSavingUser] = useState(false); // State to indicate saving process
  const [saveError, setSaveError] = useState<string | null>(null); // State for save errors

  // Define allowed roles (should ideally come from backend)
  const allowedRoles = ['admin', 'agent', 'builder', 'client', 'influencer'];

  // Fetch users when the 'users' tab is active
  useEffect(() => {
    if (activeSetting === 'users') {
      const fetchUsers = async () => {
        setLoadingUsers(true);
        setErrorUsers(null);
        try {
          const response = await fetch('http://localhost/Real-Estate/api/admin/users.php');
          if (!response.ok) {
            throw new Error('Failed to fetch users');
          }
          const result = await response.json();
          if (result.success) {
            setUsers(result.data);
          } else {
            throw new Error(result.message || 'Failed to fetch users');
          }
        } catch (err: any) {
          setErrorUsers(err.message);
        } finally {
          setLoadingUsers(false);
        }
      };

      fetchUsers();
    }
  }, [activeSetting]); // Rerun when activeSetting changes

  // Function to handle clicking Edit button
  const handleEditClick = (user: User) => {
    setEditingUser(user);
    setNewRole(user.role); // Initialize newRole with current role
    setSaveError(null);
  };

  // Function to handle saving the edited user
  const handleSaveUser = async () => {
    if (!editingUser) return;

    setSavingUser(true);
    setSaveError(null);

    try {
      const response = await fetch('http://localhost/Real-Estate/api/admin/users.php', {
        method: 'PUT', // Use PUT method for update
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          id: editingUser.id, // Send the user ID
          role: newRole,     // Send the new role
        }),
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        // Handle HTTP errors or backend logic errors
        throw new Error(result.message || 'Failed to save user changes.');
      }

      // Update users list with the saved user (optimistically or refetch)
      // Optimistic update:
      setUsers(users.map(user => user.id === editingUser.id ? { ...user, role: newRole } : user));

      // Alternatively, refetch all users after save:
      // fetchUsers(); // You would need to make fetchUsers accessible here or move it

      setEditingUser(null); // Close edit form on success

    } catch (err: any) {
      console.error("Error saving user:", err);
      setSaveError(err.message || 'An unexpected error occurred while saving.');
    } finally {
      setSavingUser(false);
    }
  };

  // Function to cancel editing
  const handleCancelEdit = () => {
    setEditingUser(null);
    setNewRole('');
    setSaveError(null);
  };

  // Function to handle deleting a user (Backend implementation needed)
  const handleDeleteUser = (userId: number) => {
    // TODO: Implement backend API call to delete user
    console.log(`Deleting user: ${userId}`);
    // After successful deletion, refetch users or remove from state
  };

  const renderSettingContent = () => {
    switch (activeSetting) {
      case 'general':
        return (
          <div>
            <h2 className="text-xl font-semibold mb-4">General Settings</h2>
            <p>Manage application-wide settings here (e.g., site name, contact info, integrations).</p>
            {/* Add forms and controls for general settings */}
          </div>
        );
      case 'users':
        return (
          <div>
            <h2 className="text-xl font-semibold mb-4">User Management</h2>
            <p className="mb-4">View, create, edit, and delete users. Assign roles and manage profiles.</p>

            {/* User List Table */}
            {loadingUsers && <div className="text-center">Loading users...</div>}
            {errorUsers && <div className="text-red-600">Error loading users: {errorUsers}</div>}
            {!loadingUsers && !errorUsers && (
              <div className="bg-white shadow-md rounded-lg overflow-hidden">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {users.map((user) => (
                      <tr key={user.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.id}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{user.name}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.email}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.role}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.created_at}</td>
                         <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                           {/* Action Buttons */}
                           <button 
                             className="text-indigo-600 hover:text-indigo-900 mr-4 disabled:opacity-50"
                             onClick={() => handleEditClick(user)}
                             disabled={savingUser}
                           >
                             Edit
                           </button>
                            <button 
                             className="text-red-600 hover:text-red-900 disabled:opacity-50"
                             onClick={() => handleDeleteUser(user.id)} // Placeholder function
                             disabled={savingUser}
                           >
                             Delete
                           </button>
                         </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}

            {/* Edit User Form (Inline or Modal) */}
            {editingUser && (
              <div className="mt-6 p-4 bg-gray-100 rounded shadow">
                <h3 className="text-lg font-semibold mb-4">Edit User: {editingUser.name}</h3>
                {saveError && (
                  <p className="text-sm text-red-600 mb-4">Error: {saveError}</p>
                )}
                <div className="mb-4">
                  <label htmlFor="userRole" className="block text-sm font-medium text-gray-700">Role</label>
                  <select
                    id="userRole"
                    value={newRole}
                    onChange={(e) => setNewRole(e.target.value)}
                    className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    disabled={savingUser}
                  >
                    {allowedRoles.map(role => (
                      <option key={role} value={role}>{role}</option>
                    ))}
                  </select>
                </div>
                <div className="flex space-x-4">
                  <GradientButton 
                    onClick={handleSaveUser}
                    disabled={savingUser}
                  >
                    {savingUser ? 'Saving...' : 'Save Changes'}
                  </GradientButton>
                  <button 
                    type="button" 
                    className="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    onClick={handleCancelEdit}
                    disabled={savingUser}
                  >
                    Cancel
                  </button>
                </div>
              </div>
            )}
          </div>
        );
      case 'privileges':
        return (
          <div>
            <h2 className="text-xl font-semibold mb-4">Privilege Management</h2>
            <p>Define and assign permissions for different user roles.</p>
            {/* Add interface for managing roles and permissions */}
          </div>
        );
      default:
        return null;
    }
  };

  return (
    <DashboardLayout userType="admin">
      <div className="container mx-auto p-4">
        <h1 className="text-2xl font-bold mb-4">Admin Settings</h1>

        <div className="flex space-x-4 mb-6">
          <GradientButton 
            variant={activeSetting === 'general' ? 'default' : 'outline'} 
            onClick={() => setActiveSetting('general')}
          >
            General
          </GradientButton>
           <GradientButton 
            variant={activeSetting === 'users' ? 'default' : 'outline'} 
            onClick={() => setActiveSetting('users')}
          >
            Users
          </GradientButton>
           <GradientButton 
            variant={activeSetting === 'privileges' ? 'default' : 'outline'} 
            onClick={() => setActiveSetting('privileges')}
          >
            Privileges
          </GradientButton>
        </div>

        <Card>
          <CardContent className="p-6">
            {renderSettingContent()}
          </CardContent>
        </Card>

        <section className="my-8">
          <CampaignManager userRole="admin" userId={/* admin user id from context or props */ 1} />
        </section>

      </div>
    </DashboardLayout>
  );
};

export default AdminSettingsPage; 