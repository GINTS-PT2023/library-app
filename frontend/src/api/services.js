import client from './client';

export const booksApi = {
  getAll: () => client.get('/books'),
  getById: (id) => client.get(`/books/${id}`),
  create: (data) => client.post('/books', data),
  update: (id, data) => client.put(`/books/${id}`, data),
  delete: (id) => client.delete(`/books/${id}`),
};

export const readersApi = {
  getAll: () => client.get('/readers'),
  getById: (id) => client.get(`/readers/${id}`),
  create: (data) => client.post('/readers', data),
  update: (id, data) => client.put(`/readers/${id}`, data),
  delete: (id) => client.delete(`/readers/${id}`),
};

export const rentalsApi = {
  getAll: () => client.get('/rentals'),
  getOverdue: () => client.get('/rentals/overdue'),
  getById: (id) => client.get(`/rentals/${id}`),
  create: (data) => client.post('/rentals', data),
  update: (id, data) => client.put(`/rentals/${id}`, data),
  delete: (id) => client.delete(`/rentals/${id}`),
  return: (id) => client.put(`/rentals/${id}`, { returned_at: new Date().toISOString() }),
};
