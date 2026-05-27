import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Book, Users, ClipboardList, AlertCircle } from 'lucide-react';
import { booksApi, readersApi, rentalsApi } from '../api/services';
import NeuralCore from '../components/NeuralCore';
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
    { label: 'Total Books', value: stats.books, icon: Book, color: '#3b82f6', path: '/books' },
    { label: 'Total Readers', value: stats.readers, icon: Users, color: '#10b981', path: '/readers' },
    { label: 'Active Rentals', value: stats.rentals, icon: ClipboardList, color: '#f59e0b', path: '/rentals' },
    { label: 'Overdue Rentals', value: stats.overdue, icon: AlertCircle, color: '#ef4444', path: '/overdue' },
  ];

  return (
    <div className="dashboard">
      <div className="dashboard-layout">
        <div className="stats-column">
          <div className="stats-grid">
            {cards.map((card) => {
              const Icon = card.icon;
              const CardContent = (
                <div 
                  className="stat-card" 
                  style={{ 
                    '--card-color': card.color,
                    '--card-color-glow': `${card.color}44`
                  }}
                >
                  <div className="stat-icon" style={{ backgroundColor: `${card.color}22`, border: `1px solid ${card.color}` }}>
                    <Icon size={24} color={card.color} />
                  </div>
                  <div className="stat-info">
                    <h3>{card.label}</h3>
                    <p>{card.value}</p>
                  </div>
                </div>
              );

              return card.path ? (
                <Link key={card.label} to={card.path} style={{ textDecoration: 'none', color: 'inherit' }}>
                  {CardContent}
                </Link>
              ) : (
                <div key={card.label}>
                  {CardContent}
                </div>
              );
            })}
          </div>
        </div>
        <div className="visualizer-column">
          <NeuralCore />
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
