import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';

interface FeedbackTicket {
  id: number;
  user_id: number | null;
  name: string;
  email: string;
  subject: string;
  message: string;
  category: 'bug' | 'feature_request' | 'general' | 'support' | 'complaint';
  priority: 'low' | 'medium' | 'high' | 'urgent';
  status: 'open' | 'in_progress' | 'resolved' | 'closed';
  assigned_to: number | null;
  resolution_notes: string | null;
  created_at: string;
  updated_at: string;
  responses?: TicketResponse[];
}

interface TicketResponse {
  id: number;
  ticket_id: number;
  user_id: number | null;
  message: string;
  is_internal: boolean;
  created_at: string;
  user_name?: string;
  user_email?: string;
}

const FeedbackSystem: React.FC = () => {
  const [tickets, setTickets] = useState<FeedbackTicket[]>([]);
  const [selectedTicket, setSelectedTicket] = useState<FeedbackTicket | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [filters, setFilters] = useState({
    status: '',
    priority: '',
    category: ''
  });

  // Form state
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
    category: 'general' as const,
    priority: 'medium' as const
  });

  const [responseMessage, setResponseMessage] = useState('');

  const fetchTickets = async () => {
    try {
      setLoading(true);
      const params = new URLSearchParams();
      if (filters.status) params.append('status', filters.status);
      if (filters.priority) params.append('priority', filters.priority);
      if (filters.category) params.append('category', filters.category);

      const response = await fetch(`/api/feedback/tickets?${params}`);
      const data = await response.json();

      if (data.success) {
        setTickets(data.data);
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to fetch tickets');
    } finally {
      setLoading(false);
    }
  };

  const fetchTicket = async (ticketId: number) => {
    try {
      const response = await fetch(`/api/feedback/tickets?id=${ticketId}`);
      const data = await response.json();

      if (data.success) {
        setSelectedTicket(data.data);
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to fetch ticket details');
    }
  };

  useEffect(() => {
    fetchTickets();
  }, [filters]);

  const handleCreateTicket = async (e: React.FormEvent) => {
    e.preventDefault();
    
    try {
      const response = await fetch('/api/feedback/tickets', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (data.success) {
        setShowCreateForm(false);
        setFormData({
          name: '',
          email: '',
          subject: '',
          message: '',
          category: 'general',
          priority: 'medium'
        });
        fetchTickets();
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to create ticket');
    }
  };

  const handleAddResponse = async (ticketId: number) => {
    if (!responseMessage.trim()) return;

    try {
      const response = await fetch('/api/feedback/tickets', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          ticket_id: ticketId,
          action: 'add_response',
          message: responseMessage,
          is_internal: false
        }),
      });

      const data = await response.json();

      if (data.success) {
        setResponseMessage('');
        fetchTicket(ticketId);
        fetchTickets();
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to add response');
    }
  };

  const handleStatusUpdate = async (ticketId: number, status: string) => {
    try {
      const response = await fetch('/api/feedback/tickets', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          ticket_id: ticketId,
          action: 'update_status',
          status: status
        }),
      });

      const data = await response.json();

      if (data.success) {
        fetchTicket(ticketId);
        fetchTickets();
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to update status');
    }
  };

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'urgent':
        return 'text-red-600 bg-red-100';
      case 'high':
        return 'text-orange-600 bg-orange-100';
      case 'medium':
        return 'text-yellow-600 bg-yellow-100';
      case 'low':
        return 'text-green-600 bg-green-100';
      default:
        return 'text-gray-600 bg-gray-100';
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'open':
        return 'text-blue-600 bg-blue-100';
      case 'in_progress':
        return 'text-yellow-600 bg-yellow-100';
      case 'resolved':
        return 'text-green-600 bg-green-100';
      case 'closed':
        return 'text-gray-600 bg-gray-100';
      default:
        return 'text-gray-600 bg-gray-100';
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Feedback & Support</h1>
        <button
          onClick={() => setShowCreateForm(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Create Ticket
        </button>
      </div>

      {/* Filters */}
      <Card>
        <CardHeader>
          <CardTitle>Filters</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select
              value={filters.status}
              onChange={(e) => setFilters({ ...filters, status: e.target.value })}
              className="border rounded px-3 py-2"
            >
              <option value="">All Status</option>
              <option value="open">Open</option>
              <option value="in_progress">In Progress</option>
              <option value="resolved">Resolved</option>
              <option value="closed">Closed</option>
            </select>

            <select
              value={filters.priority}
              onChange={(e) => setFilters({ ...filters, priority: e.target.value })}
              className="border rounded px-3 py-2"
            >
              <option value="">All Priorities</option>
              <option value="urgent">Urgent</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>

            <select
              value={filters.category}
              onChange={(e) => setFilters({ ...filters, category: e.target.value })}
              className="border rounded px-3 py-2"
            >
              <option value="">All Categories</option>
              <option value="bug">Bug Report</option>
              <option value="feature_request">Feature Request</option>
              <option value="general">General</option>
              <option value="support">Support</option>
              <option value="complaint">Complaint</option>
            </select>
          </div>
        </CardContent>
      </Card>

      {/* Create Ticket Form */}
      {showCreateForm && (
        <Card>
          <CardHeader>
            <CardTitle>Create New Ticket</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleCreateTicket} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Name</label>
                  <input
                    type="text"
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Email</label>
                  <input
                    type="email"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                    required
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium mb-1">Subject</label>
                <input
                  type="text"
                  value={formData.subject}
                  onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                  className="w-full border rounded px-3 py-2"
                  required
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Category</label>
                  <select
                    value={formData.category}
                    onChange={(e) => setFormData({ ...formData, category: e.target.value as any })}
                    className="w-full border rounded px-3 py-2"
                  >
                    <option value="general">General</option>
                    <option value="bug">Bug Report</option>
                    <option value="feature_request">Feature Request</option>
                    <option value="support">Support</option>
                    <option value="complaint">Complaint</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Priority</label>
                  <select
                    value={formData.priority}
                    onChange={(e) => setFormData({ ...formData, priority: e.target.value as any })}
                    className="w-full border rounded px-3 py-2"
                  >
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium mb-1">Message</label>
                <textarea
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  className="w-full border rounded px-3 py-2 h-32"
                  required
                />
              </div>

              <div className="flex gap-2">
                <button
                  type="submit"
                  className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  Create Ticket
                </button>
                <button
                  type="button"
                  onClick={() => setShowCreateForm(false)}
                  className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                >
                  Cancel
                </button>
              </div>
            </form>
          </CardContent>
        </Card>
      )}

      {/* Tickets List */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Tickets List */}
        <div className="lg:col-span-1">
          <Card>
            <CardHeader>
              <CardTitle>Tickets ({tickets.length})</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-2">
                {tickets.map((ticket) => (
                  <div
                    key={ticket.id}
                    onClick={() => fetchTicket(ticket.id)}
                    className={`p-3 border rounded cursor-pointer hover:bg-gray-50 ${
                      selectedTicket?.id === ticket.id ? 'bg-blue-50 border-blue-200' : ''
                    }`}
                  >
                    <div className="flex justify-between items-start mb-2">
                      <h3 className="font-medium text-sm truncate">{ticket.subject}</h3>
                      <span className={`px-2 py-1 rounded text-xs ${getPriorityColor(ticket.priority)}`}>
                        {ticket.priority}
                      </span>
                    </div>
                    <p className="text-xs text-gray-600 mb-2">{ticket.name} • {ticket.email}</p>
                    <div className="flex justify-between items-center">
                      <span className={`px-2 py-1 rounded text-xs ${getStatusColor(ticket.status)}`}>
                        {ticket.status.replace('_', ' ')}
                      </span>
                      <span className="text-xs text-gray-500">
                        {new Date(ticket.created_at).toLocaleDateString()}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Ticket Details */}
        <div className="lg:col-span-2">
          {selectedTicket ? (
            <Card>
              <CardHeader>
                <div className="flex justify-between items-start">
                  <div>
                    <CardTitle>{selectedTicket.subject}</CardTitle>
                    <p className="text-sm text-gray-600">
                      Created by {selectedTicket.name} on {new Date(selectedTicket.created_at).toLocaleDateString()}
                    </p>
                  </div>
                  <div className="flex gap-2">
                    <select
                      value={selectedTicket.status}
                      onChange={(e) => handleStatusUpdate(selectedTicket.id, e.target.value)}
                      className="border rounded px-2 py-1 text-sm"
                    >
                      <option value="open">Open</option>
                      <option value="in_progress">In Progress</option>
                      <option value="resolved">Resolved</option>
                      <option value="closed">Closed</option>
                    </select>
                  </div>
                </div>
              </CardHeader>
              <CardContent className="space-y-4">
                {/* Ticket Info */}
                <div className="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span className="font-medium">Category:</span> {selectedTicket.category.replace('_', ' ')}
                  </div>
                  <div>
                    <span className="font-medium">Priority:</span> 
                    <span className={`ml-1 px-2 py-1 rounded text-xs ${getPriorityColor(selectedTicket.priority)}`}>
                      {selectedTicket.priority}
                    </span>
                  </div>
                  <div>
                    <span className="font-medium">Status:</span>
                    <span className={`ml-1 px-2 py-1 rounded text-xs ${getStatusColor(selectedTicket.status)}`}>
                      {selectedTicket.status.replace('_', ' ')}
                    </span>
                  </div>
                  <div>
                    <span className="font-medium">Email:</span> {selectedTicket.email}
                  </div>
                </div>

                {/* Original Message */}
                <div className="border-t pt-4">
                  <h4 className="font-medium mb-2">Original Message</h4>
                  <div className="bg-gray-50 p-3 rounded">
                    <p className="text-sm whitespace-pre-wrap">{selectedTicket.message}</p>
                  </div>
                </div>

                {/* Responses */}
                {selectedTicket.responses && selectedTicket.responses.length > 0 && (
                  <div className="border-t pt-4">
                    <h4 className="font-medium mb-2">Responses</h4>
                    <div className="space-y-3">
                      {selectedTicket.responses.map((response) => (
                        <div key={response.id} className="bg-gray-50 p-3 rounded">
                          <div className="flex justify-between items-start mb-2">
                            <span className="text-sm font-medium">
                              {response.user_name || 'Anonymous'}
                            </span>
                            <span className="text-xs text-gray-500">
                              {new Date(response.created_at).toLocaleString()}
                            </span>
                          </div>
                          <p className="text-sm whitespace-pre-wrap">{response.message}</p>
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {/* Add Response */}
                <div className="border-t pt-4">
                  <h4 className="font-medium mb-2">Add Response</h4>
                  <textarea
                    value={responseMessage}
                    onChange={(e) => setResponseMessage(e.target.value)}
                    placeholder="Type your response..."
                    className="w-full border rounded px-3 py-2 h-24"
                  />
                  <button
                    onClick={() => handleAddResponse(selectedTicket.id)}
                    disabled={!responseMessage.trim()}
                    className="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400"
                  >
                    Send Response
                  </button>
                </div>
              </CardContent>
            </Card>
          ) : (
            <Card>
              <CardContent className="flex items-center justify-center h-64">
                <p className="text-gray-500">Select a ticket to view details</p>
              </CardContent>
            </Card>
          )}
        </div>
      </div>

      {error && (
        <div className="text-center text-red-600 p-4 bg-red-50 rounded">
          <p>{error}</p>
          <button 
            onClick={() => setError(null)}
            className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          >
            Dismiss
          </button>
        </div>
      )}
    </div>
  );
};

export default FeedbackSystem; 