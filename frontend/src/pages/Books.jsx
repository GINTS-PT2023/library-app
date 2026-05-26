import React, { useEffect, useState } from 'react';
import { Plus, Edit2, Trash2, Search } from 'lucide-react';
import { booksApi } from '../api/services';
import Modal from '../components/Modal';
import './Table.css';

const Books = () => {
  const [books, setBooks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [currentBook, setCurrentBook] = useState(null);
  const [formData, setFormData] = useState({
    title: '',
    author: '',
    isbn: '',
    published_year: new Date().getFullYear(),
    total_copies: 1,
  });

  const fetchBooks = async () => {
    try {
      const response = await booksApi.getAll();
      setBooks(response.data);
    } catch (error) {
      console.error('Error fetching books:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchBooks();
  }, []);

  const handleOpenModal = (book = null) => {
    if (book) {
      setCurrentBook(book);
      setFormData({
        title: book.title,
        author: book.author,
        isbn: book.isbn,
        published_year: book.published_year,
        total_copies: book.total_copies,
      });
    } else {
      setCurrentBook(null);
      setFormData({
        title: '',
        author: '',
        isbn: '',
        published_year: new Date().getFullYear(),
        total_copies: 1,
      });
    }
    setIsModalOpen(true);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (currentBook) {
        await booksApi.update(currentBook.id, formData);
      } else {
        await booksApi.create(formData);
      }
      fetchBooks();
      setIsModalOpen(false);
    } catch (error) {
      console.error('Error saving book:', error);
      alert('Failed to save book. Please check if ISBN is unique.');
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this book?')) {
      try {
        await booksApi.delete(id);
        fetchBooks();
      } catch (error) {
        console.error('Error deleting book:', error);
      }
    }
  };

  const filteredBooks = books.filter(book =>
    book.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
    book.author.toLowerCase().includes(searchTerm.toLowerCase()) ||
    book.isbn.includes(searchTerm)
  );

  return (
    <div className="page-container">
      <div className="page-header">
        <div className="search-bar">
          <Search size={20} color="#94a3b8" />
          <input
            type="text"
            placeholder="Search by title, author, or ISBN..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <button className="btn btn-primary" onClick={() => handleOpenModal()}>
          <Plus size={20} />
          <span>Add Book</span>
        </button>
      </div>

      <div className="table-container">
        <table>
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>ISBN</th>
              <th>Year</th>
              <th>Available</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan="6" className="text-center">Loading...</td></tr>
            ) : filteredBooks.length === 0 ? (
              <tr><td colSpan="6" className="text-center">No books found.</td></tr>
            ) : (
              filteredBooks.map((book) => (
                <tr key={book.id}>
                  <td><strong>{book.title}</strong></td>
                  <td>{book.author}</td>
                  <td>{book.isbn}</td>
                  <td>{book.published_year}</td>
                  <td>{book.available_copies} / {book.total_copies}</td>
                  <td className="actions">
                    <button className="action-btn edit" onClick={() => handleOpenModal(book)}>
                      <Edit2 size={16} />
                    </button>
                    <button className="action-btn delete" onClick={() => handleDelete(book.id)}>
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
        title={currentBook ? 'Edit Book' : 'Add New Book'}
      >
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Title</label>
            <input
              type="text"
              required
              value={formData.title}
              onChange={(e) => setFormData({ ...formData, title: e.target.value })}
            />
          </div>
          <div className="form-group">
            <label>Author</label>
            <input
              type="text"
              required
              value={formData.author}
              onChange={(e) => setFormData({ ...formData, author: e.target.value })}
            />
          </div>
          <div className="form-group">
            <label>ISBN</label>
            <input
              type="text"
              required
              value={formData.isbn}
              onChange={(e) => setFormData({ ...formData, isbn: e.target.value })}
            />
          </div>
          <div className="form-row">
            <div className="form-group">
              <label>Published Year</label>
              <input
                type="number"
                required
                value={formData.published_year}
                onChange={(e) => setFormData({ ...formData, published_year: e.target.value })}
              />
            </div>
            <div className="form-group">
              <label>Total Copies</label>
              <input
                type="number"
                required
                min="1"
                value={formData.total_copies}
                onChange={(e) => setFormData({ ...formData, total_copies: e.target.value })}
              />
            </div>
          </div>
          <div className="modal-footer">
            <button type="button" className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>
              Cancel
            </button>
            <button type="submit" className="btn btn-primary">
              {currentBook ? 'Update' : 'Create'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
};

export default Books;
