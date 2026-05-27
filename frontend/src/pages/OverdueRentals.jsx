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
      <div className="page-header" style={{ borderColor: '#ef4444' }}>
        <h1 style={{ display: 'flex', alignItems: 'center', gap: '12px', color: '#ef4444', textShadow: '0 0 10px rgba(239, 68, 68, 0.3)' }}>
          <AlertCircle size={24} />
          OVERDUE_RENTALS_REPORT
        </h1>
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
      
      <div style={{ marginTop: '2rem', padding: '1.5rem', background: 'rgba(239, 68, 68, 0.05)', border: '1px solid rgba(239, 68, 68, 0.2)', position: 'relative' }}>
        <p style={{ color: '#ef4444', fontSize: '0.8rem', margin: 0, fontFamily: 'JetBrains Mono, monospace', letterSpacing: '1px' }}>
          <strong style={{ display: 'block', marginBottom: '0.5rem' }}>[ SYSTEM_ALERT ]</strong>
          PROTOCOL: AUTOMATIC_OVERDUE_DETECTION_ACTIVE. THIS DATA IS STREAMED DIRECTLY FROM THE NEURAL_VIEW_LAYER. 
          ALL IDENTIFIED SUBJECTS ARE MARKED FOR IMMEDIATE ACTION.
        </p>
      </div>
    </div>
  );
};

export default OverdueRentals;
