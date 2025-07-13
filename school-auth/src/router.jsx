import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Login from './pages/Login';
import Signup from './pages/Signup';
import AdminDashboard from './pages/dashboards/AdminDashboard';
import TeacherDashboard from './pages/dashboards/TeacherDashboard';
import StudentDashboard from './pages/dashboards/StudentDashboard';

const PrivateRoute = ({ children, role }) => {
  const token = localStorage.getItem('access_token');
  const userRole = localStorage.getItem('role');
  return token && userRole === role ? children : <Navigate to="/login" />;
};

export default function AppRouter() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Navigate to="/login" />} />
        <Route path="/signup" element={<Signup />} />
        <Route path="/login" element={<Login />} />

        <Route path="/admin" element={<PrivateRoute role="Admin"><AdminDashboard /></PrivateRoute>} />
        <Route path="/teacher" element={<PrivateRoute role="Teacher"><TeacherDashboard /></PrivateRoute>} />
        <Route path="/student" element={<PrivateRoute role="Student"><StudentDashboard /></PrivateRoute>} />
      </Routes>
    </Router>
  );
}
