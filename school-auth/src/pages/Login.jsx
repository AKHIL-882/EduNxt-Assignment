import { useState } from 'react';
import api from '../services/api';
import { useNavigate } from 'react-router-dom';

export default function Login() {
  const [form, setForm] = useState({ email: '', password: '' });
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const res = await api.post('/login', form);
      console.log('Token generation response:', res.data);
      const { access_token, role } = res.data;

      localStorage.setItem('access_token', access_token);
      localStorage.setItem('role', role);
      redirectToDashboard(Number(role)); 
    } catch (err) {
      alert(err.response?.data?.message || 'Login failed');
    }
  };

  const redirectToDashboard = (role) => {
    console.log('Redirecting to dashboard for role:', role);
    if (role === 0) navigate('/admin', { replace: true });
    else if (role === 1) navigate('/teacher', { replace: true });
    else navigate('/student', { replace: true });
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-indigo-200">
      <div className="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md border border-gray-200">
        <h2 className="text-3xl font-semibold text-center text-indigo-700 mb-1">Welcome Back</h2>
        <p className="text-sm text-center text-gray-500 mb-6">Please log in to your account</p>
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-1">Email</label>
            <input
              className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
              placeholder="Enter your email"
              type="email"
              onChange={e => setForm({ ...form, email: e.target.value })}
              required
            />
          </div>
          <div className="mb-6">
            <label className="block text-gray-700 text-sm font-medium mb-1">Password</label>
            <input
              className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
              placeholder="Enter your password"
              type="password"
              onChange={e => setForm({ ...form, password: e.target.value })}
              required
            />
          </div>
          <button
            type="submit"
            className="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition duration-200"
          >
            Login
          </button>
        </form>
        <div className="mt-4 text-center text-sm">
          <span className="text-gray-600">Don't have an account? </span>
          <button
            className="text-indigo-600 hover:underline"
            onClick={() => navigate('/signup')}
          >
            Create one
          </button>
        </div>
      </div>
    </div>
  );
}
