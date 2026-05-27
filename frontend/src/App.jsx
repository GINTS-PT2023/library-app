import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Layout from './layouts/Layout';
import Dashboard from './pages/Dashboard';
import Books from './pages/Books';
import Readers from './pages/Readers';
import Rentals from './pages/Rentals';
import OverdueRentals from './pages/OverdueRentals';
import './App.css';

function App() {
  return (
    <Router>
      <Layout>
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/books" element={<Books />} />
          <Route path="/readers" element={<Readers />} />
          <Route path="/rentals" element={<Rentals />} />
          <Route path="/overdue" element={<OverdueRentals />} />
        </Routes>
      </Layout>
    </Router>
  );
}

export default App;
