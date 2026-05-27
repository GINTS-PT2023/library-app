import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { LayoutDashboard, Book, Users, ClipboardList, AlertCircle } from 'lucide-react';
import './Layout.css';

const Layout = ({ children }) => {
  const location = useLocation();

  const navItems = [
    { path: '/', label: 'Dashboard', icon: LayoutDashboard },
    { path: '/books', label: 'Books', icon: Book },
    { path: '/readers', label: 'Readers', icon: Users },
    { path: '/rentals', label: 'Rentals', icon: ClipboardList },
    { path: '/overdue', label: 'Overdue', icon: AlertCircle },
  ];

  return (
    <div className="layout">
      <aside className="sidebar">
        <div className="sidebar-header">
          <h1>Library CMS</h1>
        </div>
        <nav className="sidebar-nav">
          {navItems.map((item) => {
            const Icon = item.icon;
            const isActive = location.pathname === item.path;
            return (
              <Link
                key={item.path}
                to={item.path}
                className={`nav-item ${isActive ? 'active' : ''}`}
              >
                <Icon size={20} />
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>
      </aside>
      <main className="main-content">
        <header className="top-header">
          <h2>{navItems.find(n => n.path === location.pathname)?.label || 'Library'}</h2>
        </header>
        <section className="content">
          {children}
        </section>
      </main>
    </div>
  );
};

export default Layout;
