import React, { useEffect, useState } from 'react';
import { Book, Users, ClipboardList } from 'lucide-react';
import { booksApi, readersApi, rentalsApi } from '../api/services';
import './Dashboard.css';

const Dashboard = () => {
  const [stats, setStats] = useState({
    books: 0,
    readers: 0,
    rentals: 0,
    overdue: 0,
  });

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const [books, readers, rentals, overdue] = await Promise.all([
          booksApi.getAll(),
          readersApi.getAll(),
          rentalsApi.getAll(),
          rentalsApi.getOverdue(),
        ]);
        setStats({
          books: books.data.length,
          readers: readers.data.length,
          rentals: rentals.data.filter(r => !r.returned_at).length,
          overdue: overdue.data.length,
        });
      } catch (error) {
        console.error('Error fetching stats:', error);
      }
    };
    fetchStats();
  }, []);

  const cards = [
    { label: 'Total Books', value: stats.books, icon: Book, color: '#3b82f6' },
    { label: 'Total Readers', value: stats.readers, icon: Users, color: '#10b981' },
    { label: 'Active Rentals', value: stats.rentals, icon: ClipboardList, color: '#f59e0b' },
    { label: 'Overdue Rentals', value: stats.overdue, icon: ClipboardList, color: '#ef4444' },
  ];

  return (
    <div className="dashboard">
      <div className="stats-grid">
        {cards.map((card) => {
          const Icon = card.icon;
          return (
            <div key={card.label} className="stat-card">
              <div className="stat-icon" style={{ backgroundColor: card.color }}>
                <Icon size={24} color="#fff" />
              </div>
              <div className="stat-info">
                <h3>{card.label}</h3>
                <p>{card.value}</p>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};

export default Dashboard;
