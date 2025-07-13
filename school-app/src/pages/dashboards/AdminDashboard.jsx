import React, { useEffect, useState } from "react";
import api from "../../services/api";
import { useNavigate } from "react-router-dom";

export default function AdminDashboard() {
  const [profile, setProfile] = useState(null);
  const [loading, setLoading] = useState(true);
  const [teachers, setTeachers] = useState([]);
  const [students, setStudents] = useState([]);
  const [selectedTeacher, setSelectedTeacher] = useState(null);
  const [selectedStudent, setSelectedStudent] = useState(null);
  const [showTeacherForm, setShowTeacherForm] = useState(false);
  const [showStudentForm, setShowStudentForm] = useState(false);
  const [showStudentDetails, setShowStudentDetails] = useState(false);
  const [formData, setFormData] = useState({ name: '', email: '', password: '' });
  const [studentGrades, setStudentGrades] = useState([]);
  const [studentAttendance, setStudentAttendance] = useState([]);
  const [activeSection, setActiveSection] = useState('dashboard');
  const [roles, setRoles] = useState([0,1,2]);
  const [permissions, setPermissions] = useState([]);
  const [users, setUsers] = useState([]);
  const [roleForm, setRoleForm] = useState({ name: '' });
  const [roleFeedback, setRoleFeedback] = useState('');
  const [products, setProducts] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [orderQty, setOrderQty] = useState(1);
  const [newsletterStats, setNewsletterStats] = useState({ sent: 0, failed: 0, pending: 0 });
   const [sendingNewsletter, setSendingNewsletter] = useState(false);
  const [courses] = useState([
    { id: 1, name: 'Mathematics', description: 'Algebra, Geometry, Calculus' },
    { id: 2, name: 'Science', description: 'Physics, Chemistry, Biology' },
    { id: 3, name: 'English', description: 'Grammar, Literature, Writing' },
    { id: 4, name: 'History', description: 'World History, Civics' },
    { id: 5, name: 'Computer', description: 'Programming, IT Basics' },
  ]);
  const navigate = useNavigate();

  useEffect(() => {
    api
      .get("/admin/profile")
      .then((res) => setProfile(res.data))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    api.get('/admin/teachers').then(res => setTeachers(res.data));
    api.get('/admin/students').then(res => setStudents(res.data));
    api.get('/admin/roles').then(res => setRoles(res.data));
    api.get('/admin/permissions').then(res => setPermissions(res.data));
    api.get('/admin/users').then(res => setUsers(res.data));
    api.get('/admin/products').then(res => setProducts(res.data));
    api.get('/admin/newsletter-dashboard').then(res => setNewsletterStats(res.data));
  }, []);

  const handleLogout = async () => {
    localStorage.removeItem("access_token");
    localStorage.removeItem("role");
    await api.post("/logout");
    navigate("/login");
  };

  const handleShowStudentDetails = async (student) => {
    setSelectedStudent(student.id);
    setFormData({ name: student.name, email: student.email, password: '' });
    const gradesRes = await api.get(`/admin/student/${student.id}/grades`);
    const attendanceRes = await api.get(`/admin/student/${student.id}/attendance`);
    setStudentGrades(gradesRes.data);
    setStudentAttendance(attendanceRes.data);
    setShowStudentDetails(true);
  };

  // CRUD operations
  const handleAddTeacher = async () => {
    await api.post('/admin/teachers', formData);
    setShowTeacherForm(false);
    setFormData({ name: '', email: '', password: '' });
    const res = await api.get('/admin/teachers');
    setTeachers(res.data);
  };
  const handleEditTeacher = async (id) => {
    await api.put(`/admin/teachers/${id}`, formData);
    setShowTeacherForm(false);
    setFormData({ name: '', email: '', password: '' });
    const res = await api.get('/admin/teachers');
    setTeachers(res.data);
  };
  const handleDeleteTeacher = async (id) => {
    await api.delete(`/admin/teachers/${id}`);
    const res = await api.get('/admin/teachers');
    setTeachers(res.data);
  };
  const handleAddStudent = async () => {
    await api.post('/admin/students', { ...formData, teacher_id: selectedTeacher });
    setShowStudentForm(false);
    setFormData({ name: '', email: '', password: '' });
    const res = await api.get('/admin/students');
    setStudents(res.data);
  };
  const handleEditStudent = async (id) => {
    await api.put(`/admin/students/${id}`, formData);
    setShowStudentForm(false);
    setFormData({ name: '', email: '', password: '' });
    const res = await api.get('/admin/students');
    setStudents(res.data);
  };
  const handleDeleteStudent = async (id) => {
    await api.delete(`/admin/students/${id}`);
    const res = await api.get('/admin/students');
    setStudents(res.data);
  };
  const handleCreateRole = async () => {
    await api.post('/admin/roles', roleForm);
    setRoleForm({ name: '' });
    setRoleFeedback('Role created successfully!');
    const res = await api.get('/admin/roles');
    setRoles(res.data);
    setTimeout(() => setRoleFeedback(''), 2000);
  };
  const handleAssignPermission = async (roleId, permissionId) => {
    await api.post(`/admin/roles/${roleId}/assign-permission`, { permission_id: permissionId });
    const res = await api.get('/admin/roles');
    setRoles(res.data);
    setRoleFeedback('Permission assigned!');
    setTimeout(() => setRoleFeedback(''), 1500);
  };
  const handleRevokePermission = async (roleId, permissionId) => {
    await api.post(`/admin/roles/${roleId}/revoke-permission`, { permission_id: permissionId });
    const res = await api.get('/admin/roles');
    setRoles(res.data);
    setRoleFeedback('Permission revoked!');
    setTimeout(() => setRoleFeedback(''), 1500);
  };
  const handleUpdateUserRole = async (userId, roleId) => {
    await api.put(`/admin/users/${userId}/role`, { role_id: roleId });
    const res = await api.get('/admin/users');
    setUsers(res.data);
    setRoleFeedback('User role updated!');
    setTimeout(() => setRoleFeedback(''), 1500);
  };

  const handleSendNewsletter = async () => {
    setSendingNewsletter(true);
    try {
      await api.post('/newsletter/send');
      alert('Newsletter job triggered!');
      const res = await api.get('/admin/newsletter-dashboard');
      setNewsletterStats(res.data);
    } catch (e) {
      alert('Failed to trigger newsletter job.');
    } finally {
      setSendingNewsletter(false);
    }
  };

  const handlePlaceOrder = async (productId) => {
    const email = profile?.email || 'admin@example.com';
    try {
      await api.post('/admin/orders', {
        product_id: productId,
        quantity: orderQty,
        email: email
      });
      alert('Order placed successfully');
    } catch (e) {
      alert('Failed to place order');
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-purple-100 to-indigo-200 flex">
      {/* Left Nav Bar */}
      <div className="w-64 bg-white shadow-lg flex flex-col py-8 px-4">
        <h2 className="text-2xl font-bold text-indigo-700 mb-8">Admin Panel</h2>
        <button className={`text-left px-4 py-2 mb-2 rounded ${activeSection === 'dashboard' ? 'bg-indigo-100 font-bold' : ''}`} onClick={() => setActiveSection('dashboard')}>Dashboard</button>
        <button className={`text-left px-4 py-2 mb-2 rounded ${activeSection === 'teachers' ? 'bg-indigo-100 font-bold' : ''}`} onClick={() => setActiveSection('teachers')}>Teachers</button>
        <button className={`text-left px-4 py-2 mb-2 rounded ${activeSection === 'students' ? 'bg-indigo-100 font-bold' : ''}`} onClick={() => setActiveSection('students')}>Students</button>
        <button className={`text-left px-4 py-2 mb-2 rounded ${activeSection === 'courses' ? 'bg-indigo-100 font-bold' : ''}`} onClick={() => setActiveSection('courses')}>Courses</button>
        <button className={`text-left px-4 py-2 mb-2 rounded ${activeSection === 'roles' ? 'bg-indigo-100 font-bold' : ''}`} onClick={() => setActiveSection('roles')}>Roles</button>
         <button className={`text-left px-4 py-2 mb-2 rounded ${activeSection === 'orders' ? 'bg-indigo-100 font-bold' : ''}`} onClick={() => setActiveSection('orders')}>Place Order</button>
          <button className={`text-left px-4 py-2 mb-2 rounded ${activeSection === 'newsletter' ? 'bg-indigo-100 font-bold' : ''}`} onClick={() => setActiveSection('newsletter')}>📬 Newsletter</button>
        <div className="mt-auto">
          <button
            onClick={handleLogout}
            className="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-full hover:from-red-600 hover:to-red-700 transition duration-200 shadow-lg w-full"
          >
            Logout
          </button>
        </div>
      </div>

             
      <div className="flex-1 p-8">
        {activeSection === 'dashboard' && (
          <div>
            <h2 className="text-3xl font-bold text-gray-800 mb-6">👑 Dashboard</h2>
            <div className="grid grid-cols-2 gap-8 mb-8">
              <div className="bg-white rounded-xl shadow p-6 text-center">
                <h3 className="text-xl font-semibold text-indigo-700 mb-2">Teachers</h3>
                <div className="text-4xl font-bold">{teachers.length}</div>
              </div>
              <div className="bg-white rounded-xl shadow p-6 text-center">
                <h3 className="text-xl font-semibold text-green-700 mb-2">Students</h3>
                <div className="text-4xl font-bold">{students.length}</div>
              </div>
            </div>
          </div>
        )}
         {activeSection === 'newsletter' && (
          <section>
            <h3 className="text-2xl font-bold text-gray-800 mb-6">📬 Newsletter Job Dashboard</h3>
            <div className="bg-white rounded-lg shadow p-6">
              <ul className="list-disc ml-6 text-lg space-y-2 mb-4">
                <li>✅ <strong>Sent:</strong> {newsletterStats.sent}</li>
                <li>❌ <strong>Failed:</strong> {newsletterStats.failed}</li>
                <li>🕒 <strong>Pending:</strong> {newsletterStats.pending}</li>
              </ul>
              <button
                onClick={handleSendNewsletter}
                className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50"
                disabled={sendingNewsletter}
              >
                {sendingNewsletter ? 'Sending...' : 'Send Newsletter'}
              </button>
            </div>
          </section>
        )}
        {activeSection === 'teachers' && (
          <div>
            <h3 className="text-xl font-bold text-gray-700 mb-4">👨‍🏫 Teachers</h3>
            <button className="bg-green-500 text-white px-4 py-2 rounded mb-4" onClick={() => setShowTeacherForm(true)}>Add Teacher</button>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {teachers.map(teacher => (
                <div key={teacher.id} className="bg-white rounded-xl shadow p-5">
                  <div className="font-bold text-lg mb-2">{teacher.name}</div>
                  <div className="text-gray-600 mb-2">{teacher.email}</div>
                  <button className="bg-blue-500 text-white px-2 py-1 rounded mr-2" onClick={() => { setSelectedTeacher(teacher.id); setFormData({ name: teacher.name, email: teacher.email, password: '' }); setShowTeacherForm(true); }}>Edit</button>
                  <button className="bg-red-500 text-white px-2 py-1 rounded" onClick={() => handleDeleteTeacher(teacher.id)}>Delete</button>
                  <div className="mt-4">
                    <h4 className="font-semibold mb-2">Students</h4>
                    <button className="bg-green-400 text-white px-2 py-1 rounded mb-2" onClick={() => { setSelectedTeacher(teacher.id); setShowStudentForm(true); }}>Add Student</button>
                    <div className="grid grid-cols-2 gap-2">
                      {students.filter(s => s.teacher_id === teacher.id).map(student => (
                        <div key={student.id} className="bg-gray-100 rounded p-2">
                          <div className="font-medium">{student.name}</div>
                          <div className="text-xs text-gray-500">{student.email}</div>
                          <button className="bg-blue-500 text-white px-2 py-1 rounded mr-2 mt-1" onClick={() => { setSelectedStudent(student.id); setFormData({ name: student.name, email: student.email, password: '' }); setShowStudentForm(true); }}>Edit</button>
                          <button className="bg-red-500 text-white px-2 py-1 rounded mt-1" onClick={() => handleDeleteStudent(student.id)}>Delete</button>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
        {activeSection === 'students' && (
          <div>
            <h3 className="text-xl font-bold text-gray-700 mb-4">🎓 Students</h3>
            <button className="bg-green-500 text-white px-4 py-2 rounded mb-4" onClick={() => { setShowStudentForm(true); setSelectedStudent(null); setFormData({ name: '', email: '', password: '' }); }}>Add Student</button>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {students.map(student => (
                <div key={student.id} className="bg-white rounded-xl shadow p-5 cursor-pointer mb-4" onClick={() => handleShowStudentDetails(student)}>
                  <div className="font-bold text-lg mb-2">{student.name}</div>
                  <div className="text-gray-600 mb-2">{student.email}</div>
                  <button className="bg-blue-500 text-white px-2 py-1 rounded mr-2" onClick={e => { e.stopPropagation(); setSelectedStudent(student.id); setFormData({ name: student.name, email: student.email, password: '' }); setShowStudentForm(true); }}>Edit</button>
                  <button className="bg-red-500 text-white px-2 py-1 rounded" onClick={e => { e.stopPropagation(); handleDeleteStudent(student.id); }}>Delete</button>
                </div>
              ))}
            </div>
          </div>
        )}
        {activeSection === 'courses' && (
          <div>
            <h3 className="text-xl font-bold text-gray-700 mb-4">📚 Courses</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {courses.map(course => (
                <div key={course.id} className="bg-white rounded-xl shadow p-5 mb-4">
                  <div className="font-bold text-lg mb-2">{course.name}</div>
                  <div className="text-gray-600 mb-2">{course.description}</div>
                </div>
              ))}
            </div>
          </div>
        )}
        {activeSection === 'roles' && (
          <div>
            <h3 className="text-xl font-bold text-gray-700 mb-4">🔑 Roles & Permissions</h3>
            <div className="mb-6">
              <h4 className="font-semibold mb-2">Create New Role</h4>
              <input className="border p-2 rounded mr-2" placeholder="Role Name" value={roleForm.name} onChange={e => setRoleForm({ name: e.target.value })} />
              <button className="bg-green-500 text-white px-4 py-2 rounded" onClick={handleCreateRole}>Create</button>
              {roleFeedback && <span className="ml-4 text-green-600 font-semibold">{roleFeedback}</span>}
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {roles.map(role => (
                <div key={role.id} className="bg-white rounded-xl shadow p-5 mb-4">
                  <div className="font-bold text-lg mb-2">{role.name}</div>
                  <div className="mb-2">Permissions:</div>
                  <div className="flex flex-wrap gap-2 mb-2">
                    {permissions.map(permission => {
                      const hasPermission = role.permissions.some(p => p.id === permission.id);
                      return (
                        <label key={permission.id} className="flex items-center gap-1">
                          <input
                            type="checkbox"
                            checked={hasPermission}
                            onChange={() => hasPermission ? handleRevokePermission(role.id, permission.id) : handleAssignPermission(role.id, permission.id)}
                          />
                          <span>{permission.name}</span>
                        </label>
                      );
                    })}
                  </div>
                </div>
              ))}
            </div>
            <div className="mt-8">
              <h4 className="font-semibold mb-2">Edit User Roles</h4>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {users.map(user => (
                  <div key={user.id} className="bg-gray-100 rounded p-4 mb-2">
                    <div className="font-medium mb-1">{user.name} <span className="text-xs text-gray-500">({user.email})</span></div>
                    <select
                      className="border p-1 rounded"
                      value={user.role ? user.role.id : ''}
                      onChange={e => handleUpdateUserRole(user.id, e.target.value)}
                    >
                      <option value="">Select Role</option>
                      {roles.map(role => (
                        <option key={role.id} value={role.id}>{role.name}</option>
                      ))}
                    </select>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}
        {/* Teacher Form Modal */}
        {showTeacherForm && (
          <div className="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div className="bg-white rounded shadow-lg p-6 w-full max-w-md relative">
              <button className="absolute top-2 right-2 text-gray-500 hover:text-gray-800" onClick={() => { setShowTeacherForm(false); setSelectedTeacher(null); setFormData({ name: '', email: '', password: '' }); }}>&times;</button>
              <h3 className="text-xl font-bold mb-4">{selectedTeacher ? 'Edit Teacher' : 'Add Teacher'}</h3>
              <input className="w-full border p-2 mb-2" placeholder="Name" value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} />
              <input className="w-full border p-2 mb-2" placeholder="Email" value={formData.email} onChange={e => setFormData({ ...formData, email: e.target.value })} />
              <input className="w-full border p-2 mb-4" placeholder="Password" type="password" value={formData.password} onChange={e => setFormData({ ...formData, password: e.target.value })} />
              <button className="bg-green-500 text-white px-4 py-2 rounded mr-2" onClick={() => selectedTeacher ? handleEditTeacher(selectedTeacher) : handleAddTeacher()}>{selectedTeacher ? 'Save' : 'Add'}</button>
            </div>
          </div>
        )}
        {/* Student Form Modal */}
        {showStudentForm && (
          <div className="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div className="bg-white rounded shadow-lg p-6 w-full max-w-md relative">
              <button className="absolute top-2 right-2 text-gray-500 hover:text-gray-800" onClick={() => { setShowStudentForm(false); setSelectedStudent(null); setFormData({ name: '', email: '', password: '' }); }}>&times;</button>
              <h3 className="text-xl font-bold mb-4">{selectedStudent ? 'Edit Student' : 'Add Student'}</h3>
              <input className="w-full border p-2 mb-2" placeholder="Name" value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} />
              <input className="w-full border p-2 mb-2" placeholder="Email" value={formData.email} onChange={e => setFormData({ ...formData, email: e.target.value })} />
              <input className="w-full border p-2 mb-4" placeholder="Password" type="password" value={formData.password} onChange={e => setFormData({ ...formData, password: e.target.value })} />
              <button className="bg-green-500 text-white px-4 py-2 rounded mr-2" onClick={() => selectedStudent ? handleEditStudent(selectedStudent) : handleAddStudent()}>{selectedStudent ? 'Save' : 'Add'}</button>
            </div>
          </div>
        )}
        {/* Student Details Modal */}
        {showStudentDetails && (
          <div className="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div className="bg-white rounded shadow-lg p-6 w-full max-w-md relative">
              <button className="absolute top-2 right-2 text-gray-500 hover:text-gray-800" onClick={() => setShowStudentDetails(false)}>&times;</button>
              <h3 className="text-xl font-bold mb-4">{formData.name}'s Details</h3>
              <div className="mb-4">
                <h4 className="font-semibold mb-2">Grades</h4>
                <table className="w-full border mb-2">
                  <thead>
                    <tr>
                      <th className="border p-2">Subject</th>
                      <th className="border p-2">Grade</th>
                    </tr>
                  </thead>
                  <tbody>
                    {studentGrades.map((g, idx) => (
                      <tr key={idx}>
                        <td className="border p-2">{g.subject}</td>
                        <td className="border p-2">{g.grade}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              <div>
                <h4 className="font-semibold mb-2">Attendance</h4>
                <table className="w-full border">
                  <thead>
                    <tr>
                      <th className="border p-2">Date</th>
                      <th className="border p-2">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    {studentAttendance.map((a, idx) => (
                      <tr key={idx}>
                        <td className="border p-2">{a.date}</td>
                        <td className="border p-2">{a.status}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}
      </div>
        {activeSection === 'orders' && (
          <div>
            <h3 className="text-xl font-bold text-gray-700 mb-4">🛒 Place Order</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {products.map(product => (
                <div key={product.id} className="bg-white rounded-xl shadow p-5 mb-4">
                  <div className="font-bold text-lg mb-2">{product.name}</div>
                  <div className="text-gray-600 mb-2">{product.description}</div>
                  <div className="mb-2">Price: ₹{product.price}</div>
                  <div className="mb-2">In Stock: {product.stock}</div>
                  <input
                    type="number"
                    min="1"
                    className="border p-1 rounded w-20 mr-2"
                    placeholder="Qty"
                    value={selectedProduct?.id === product.id ? orderQty : 1}
                    onChange={(e) => {
                      setSelectedProduct(product);
                      setOrderQty(parseInt(e.target.value));
                    }}
                  />
                  <button className="bg-indigo-600 text-white px-3 py-1 rounded" onClick={() => handlePlaceOrder(product.id)}>Order</button>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
      
  );
}
