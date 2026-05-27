import React, { useEffect, useState } from 'react';
import { AlertCircle, Mail, Book as BookIcon, User } from 'lucide-react';
import { rentalsApi } from '../api/services';
import './Table.css';

const OverdueRentals = () => {
  const [overdueRentals, setOverdueRentals] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchOverdueRentals = async () => {
    try {
      const response = await rentalsApi.getOverdue();
      setOverdueRentals(response.data);
    } catch (error) {
      console.error('Error fetching overdue rentals:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchOverdueRentals();
  }, []);

  const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString();
  };

  return (
    <div className="page-container">
      <div className="page-header">
        <h2 style={{ display: 'flex', alignItems: 'center', gap: '8px', color: '#ef4444' }}>
          <AlertCircle size={24} />
          Overdue Rentals Report
        </h2>
      </div>

      <div className="table-container">
        <table>
          <thead>
            <tr>
              <th>Book Details</th>
              <th>Reader Information</th>
              <th>Rented At</th>
              <th>Due At</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan="5" className="text-center">Loading overdue rentals...</td></tr>
            ) : overdueRentals.length === 0 ? (
              <tr><td colSpan="5" className="text-center">No overdue rentals found.</td></tr>
            ) : (
              overdueRentals.map((rental) => (
                <tr key={rental.rental_id}>
                  <td>
                    <div style={{ display: 'flex', flexDirection: 'column' }}>
                      <strong style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                        <BookIcon size={14} /> {rental.book_title}
                      </strong>
                      <small style={{ color: '#64748b' }}>by {rental.book_author}</small>
                    </div>
                  </td>
                  <td>
                    <div style={{ display: 'flex', flexDirection: 'column' }}>
                      <span style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                        <User size={14} /> {rental.reader_name}
                      </span>
                      <small style={{ display: 'flex', alignItems: 'center', gap: '4px', color: '#64748b' }}>
                        <Mail size={12} /> {rental.reader_email}
                      </small>
                    </div>
                  </td>
                  <td>{formatDate(rental.rented_at)}</td>
                  <td>
                    <span style={{ color: '#ef4444', fontWeight: '500' }}>
                      {formatDate(rental.due_at)}
                    </span>
                  </td>
                  <td>
                    <span className="badge badge-danger">Overdue</span>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
      
      <div style={{ marginTop: '20px', padding: '15px', backgroundColor: '#fef2f2', border: '1px solid #fee2e2', borderRadius: '8px' }}>
        <p style={{ color: '#991b1b', fontSize: '14px', margin: 0 }}>
          <strong>Note:</strong> This report is generated directly from a database View. 
          It automatically identifies all rentals that are past their due date and haven't been returned.
        </p>
      </div>
    </div>
  );
};

export default OverdueRentals;
