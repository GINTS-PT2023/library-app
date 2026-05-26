import React, { useEffect, useState } from 'react';
import { Plus, CheckCircle, Search } from 'lucide-react';
import { rentalsApi, booksApi, readersApi } from '../api/services';
import Modal from '../components/Modal';
import './Table.css';

const Rentals = () => {
  const [rentals, setRentals] = useState([]);
  const [books, setBooks] = useState([]);
  const [readers, setReaders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [formData, setFormData] = useState({
    book_id: '',
    reader_id: '',
    due_at: '',
  });

  const fetchData = async () => {
    try {
      const [rentalsRes, booksRes, readersRes] = await Promise.all([
        rentalsApi.getAll(),
        booksApi.getAll(),
        readersApi.getAll(),
      ]);
      setRentals(rentalsRes.data);
      setBooks(booksRes.data.filter(b => b.available_copies > 0));
      setReaders(readersRes.data);
    } catch (error) {
      console.error('Error fetching data:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleOpenModal = () => {
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    setFormData({
      book_id: '',
      reader_id: '',
      due_at: nextWeek.toISOString().split('T')[0],
    });
    setIsModalOpen(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await rentalsApi.create({
        ...formData,
        rented_at: new Date().toISOString(),
      });
      fetchData();
      setIsModalOpen(false);
    } catch (error) {
      console.error('Error creating rental:', error);
      alert('Failed to create rental. Please check availability.');
    }
  };

  const handleReturn = async (id) => {
    try {
      await rentalsApi.return(id);
      fetchData();
    } catch (error) {
      console.error('Error returning rental:', error);
    }
  };

  const filteredRentals = rentals.filter(rental =>
    rental.book.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
    `${rental.reader.first_name} ${rental.reader.last_name}`.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString();
  };

  return (
    <div className="page-container">
      <div className="page-header">
        <div className="search-bar">
          <Search size={20} color="#94a3b8" />
          <input
            type="text"
            placeholder="Search by book or reader name..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <button className="btn btn-primary" onClick={handleOpenModal}>
          <Plus size={20} />
          <span>New Rental</span>
        </button>
      </div>

      <div className="table-container">
        <table>
          <thead>
            <tr>
              <th>Book</th>
              <th>Reader</th>
              <th>Rented At</th>
              <th>Due At</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan="6" className="text-center">Loading...</td></tr>
            ) : filteredRentals.length === 0 ? (
              <tr><td colSpan="6" className="text-center">No rentals found.</td></tr>
            ) : (
              filteredRentals.map((rental) => (
                <tr key={rental.id}>
                  <td><strong>{rental.book.title}</strong></td>
                  <td>{rental.reader.first_name} {rental.reader.last_name}</td>
                  <td>{formatDate(rental.rented_at)}</td>
                  <td>{formatDate(rental.due_at)}</td>
                  <td>
                    {rental.returned_at ? (
                      <span className="badge badge-success">Returned</span>
                    ) : new Date(rental.due_at) < new Date() ? (
                      <span className="badge badge-danger">Overdue</span>
                    ) : (
                      <span className="badge badge-warning">Active</span>
                    )}
                  </td>
                  <td className="actions">
                    {!rental.returned_at && (
                      <button className="action-btn edit" title="Return Book" onClick={() => handleReturn(rental.id)}>
                        <CheckCircle size={16} />
                      </button>
                    )}
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
        title="Create New Rental"
      >
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Select Book</label>
            <select
              required
              value={formData.book_id}
              onChange={(e) => setFormData({ ...formData, book_id: e.target.value })}
            >
              <option value="">Select a book...</option>
              {books.map(book => (
                <option key={book.id} value={book.id}>
                  {book.title} ({book.available_copies} available)
                </option>
              ))}
            </select>
          </div>
          <div className="form-group">
            <label>Select Reader</label>
            <select
              required
              value={formData.reader_id}
              onChange={(e) => setFormData({ ...formData, reader_id: e.target.value })}
            >
              <option value="">Select a reader...</option>
              {readers.map(reader => (
                <option key={reader.id} value={reader.id}>
                  {reader.first_name} {reader.last_name}
                </option>
              ))}
            </select>
          </div>
          <div className="form-group">
            <label>Due Date</label>
            <input
              type="date"
              required
              value={formData.due_at}
              onChange={(e) => setFormData({ ...formData, due_at: e.target.value })}
            />
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>
              Cancel
            </button>
            <button type="submit" className="btn btn-primary">
              Create Rental
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
};

export default Rentals;
