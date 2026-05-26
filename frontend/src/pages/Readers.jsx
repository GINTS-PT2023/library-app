import React, { useEffect, useState } from 'react';
import { Plus, Edit2, Trash2, Search } from 'lucide-react';
import { readersApi } from '../api/services';
import Modal from '../components/Modal';
import './Table.css';

const Readers = () => {
  const [readers, setReaders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [currentReader, setCurrentReader] = useState(null);
  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    address: '',
  });

  const fetchReaders = async () => {
    try {
      const response = await readersApi.getAll();
      setReaders(response.data);
    } catch (error) {
      console.error('Error fetching readers:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchReaders();
  }, []);

  const handleOpenModal = (reader = null) => {
    if (reader) {
      setCurrentReader(reader);
      setFormData({
        first_name: reader.first_name,
        last_name: reader.last_name,
        email: reader.email,
        phone_number: reader.phone_number || '',
        address: reader.address || '',
      });
    } else {
      setCurrentReader(null);
      setFormData({
        first_name: '',
        last_name: '',
        email: '',
        phone_number: '',
        address: '',
      });
    }
    setIsModalOpen(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (currentReader) {
        await readersApi.update(currentReader.id, formData);
      } else {
        await readersApi.create(formData);
      }
      fetchReaders();
      setIsModalOpen(false);
    } catch (error) {
      console.error('Error saving reader:', error);
      alert('Failed to save reader. Please check if email is unique.');
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this reader?')) {
      try {
        await readersApi.delete(id);
        fetchReaders();
      } catch (error) {
        console.error('Error deleting reader:', error);
      }
    }
  };

  const filteredReaders = readers.filter(reader =>
    `${reader.first_name} ${reader.last_name}`.toLowerCase().includes(searchTerm.toLowerCase()) ||
    reader.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="page-container">
      <div className="page-header">
        <div className="search-bar">
          <Search size={20} color="#94a3b8" />
          <input
            type="text"
            placeholder="Search by name or email..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <button className="btn btn-primary" onClick={() => handleOpenModal()}>
          <Plus size={20} />
          <span>Add Reader</span>
        </button>
      </div>

      <div className="table-container">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan="5" className="text-center">Loading...</td></tr>
            ) : filteredReaders.length === 0 ? (
              <tr><td colSpan="5" className="text-center">No readers found.</td></tr>
            ) : (
              filteredReaders.map((reader) => (
                <tr key={reader.id}>
                  <td><strong>{reader.first_name} {reader.last_name}</strong></td>
                  <td>{reader.email}</td>
                  <td>{reader.phone_number || '-'}</td>
                  <td>{reader.address || '-'}</td>
                  <td className="actions">
                    <button className="action-btn edit" onClick={() => handleOpenModal(reader)}>
                      <Edit2 size={16} />
                    </button>
                    <button className="action-btn delete" onClick={() => handleDelete(reader.id)}>
                      <Trash2 size={16} />
                    </button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={currentReader ? 'Edit Reader' : 'Add New Reader'}
      >
        <form onSubmit={handleSubmit}>
          <div className="form-row">
            <div className="form-group">
              <label>First Name</label>
              <input
                type="text"
                required
                value={formData.first_name}
                onChange={(e) => setFormData({ ...formData, first_name: e.target.value })}
              />
            </div>
            <div className="form-group">
              <label>Last Name</label>
              <input
                type="text"
                required
                value={formData.last_name}
                onChange={(e) => setFormData({ ...formData, last_name: e.target.value })}
              />
            </div>
          </div>
          <div className="form-group">
            <label>Email</label>
            <input
              type="email"
              required
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
            />
          </div>
          <div className="form-group">
            <label>Phone Number</label>
            <input
              type="text"
              value={formData.phone_number}
              onChange={(e) => setFormData({ ...formData, phone_number: e.target.value })}
            />
          </div>
          <div className="form-group">
            <label>Address</label>
            <input
              type="text"
              value={formData.address}
              onChange={(e) => setFormData({ ...formData, address: e.target.value })}
            />
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>
              Cancel
            </button>
            <button type="submit" className="btn btn-primary">
              {currentReader ? 'Update' : 'Create'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
};

export default Readers;
