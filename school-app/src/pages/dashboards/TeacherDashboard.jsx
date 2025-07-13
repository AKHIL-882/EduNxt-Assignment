import React, { useEffect, useState, useRef } from "react";
import api from "../../services/api";
import { useNavigate } from "react-router-dom";

export default function TeacherDashboard() {
  const [profile, setProfile] = useState(null);
  const [students, setStudents] = useState([]);
  const [attendance, setAttendance] = useState([]);
  const [permissions, setPermissions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [selectedStudent, setSelectedStudent] = useState(null);
  const navigate = useNavigate();
  const modalRef = useRef();

  useEffect(() => {
    async function fetchData() {
      try {
        const [profileRes, gradesRes, attendanceRes, permsRes] = await Promise.all([
          api.get("/teacher/profile"),
          api.get("/teacher/grades"),
          api.get("/teacher/attendance"),
          api.get("/teacher/permissions"),
        ]);
        setProfile(profileRes.data);
        setStudents(gradesRes.data);
        setAttendance(attendanceRes.data);
        setPermissions(permsRes.data);
      } catch {
        setError("Failed to fetch data");
      } finally {
        setLoading(false);
      }
    }
    fetchData();
  }, []);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === "Escape") setSelectedStudent(null);
    };
    const handleClickOutside = (e) => {
      if (modalRef.current && !modalRef.current.contains(e.target)) {
        setSelectedStudent(null);
      }
    };
    document.addEventListener("keydown", handleKeyDown);
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("keydown", handleKeyDown);
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  const handleLogout = async () => {
    localStorage.removeItem("access_token");
    localStorage.removeItem("role");
    await api.post("/logout");
    navigate("/login");
  };

  const hasPermission = (perm) => permissions.includes(perm);

  if (!hasPermission("view_dashboard")) {
    return (
      <div className="min-h-screen bg-red-50 flex items-center justify-center text-red-700 text-2xl font-bold">
        No Access
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-yellow-50 to-orange-100 p-8">
      <div className="max-w-6xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-3xl font-bold text-gray-800 flex items-center gap-2">
            👨‍🏫 Teacher Dashboard
          </h2>
          <button
            onClick={handleLogout}
            className="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-full hover:from-red-600 hover:to-red-700 transition duration-200 shadow-lg"
          >
            Logout
          </button>
        </div>

        {profile && (
          <div className="bg-white rounded-xl shadow p-6 mb-6">
            <h3 className="text-xl font-semibold text-blue-700 mb-4">👤 Profile</h3>
            <p><strong>Name:</strong> {profile.name}</p>
            <p><strong>Email:</strong> {profile.email}</p>
          </div>
        )}

        {loading ? (
          <div className="text-gray-600 text-center">Loading...</div>
        ) : error ? (
          <div className="text-red-600 text-center">{error}</div>
        ) : (
          <div>
            <h3 className="text-xl font-semibold text-green-700 mb-4">🎓 Students</h3>
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {[...new Set(students.map((s) => s.id))].map((studentId) => {
                const studentName = students.find((s) => s.id === studentId)?.name;
                return (
                  <div
                    key={studentId}
                    className="bg-white p-4 rounded-lg shadow hover:shadow-lg transition cursor-pointer border border-gray-100 hover:bg-blue-50"
                    onClick={() => setSelectedStudent(studentId)}
                  >
                    <h4 className="text-lg font-bold text-gray-800 mb-2">{studentName}</h4>
                    <p className="text-gray-500 text-sm">Click to view details</p>
                  </div>
                );
              })}
            </div>
          </div>
        )}
      </div>

      {selectedStudent && (
        <div className="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center px-4">
          <div
            ref={modalRef}
            className="bg-white w-full max-w-6xl max-h-[90vh] overflow-y-auto rounded-lg shadow-lg p-6 relative"
          >
            <button
              className="absolute top-2 right-3 text-gray-500 text-2xl hover:text-gray-800"
              onClick={() => setSelectedStudent(null)}
            >
              &times;
            </button>
            <h3 className="text-2xl font-bold mb-4 text-indigo-700">
              {students.find((s) => s.id === selectedStudent)?.name}
            </h3>
            <div className="flex flex-col lg:flex-row gap-6">
              <GradesSection
                students={students}
                selectedStudent={selectedStudent}
                setStudents={setStudents}
                canEdit={hasPermission("manage_grades")}
              />
              <AttendanceSection
                attendance={attendance}
                selectedStudent={selectedStudent}
                setAttendance={setAttendance}
                canEdit={hasPermission("manage_attendance")}
              />
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

function GradesSection({ students, selectedStudent, setStudents, canEdit }) {
  const [currentPage, setCurrentPage] = useState(1);
  const pageSize = 5;
  const studentGrades = students.filter((s) => s.id === selectedStudent);
  const totalPages = Math.ceil(studentGrades.length / pageSize);
  const paginatedGrades = studentGrades.slice((currentPage - 1) * pageSize, currentPage * pageSize);

  return (
    <div className="flex-1 min-w-[300px]">
      <h4 className="text-lg font-semibold text-blue-700 mb-2">📘 Grades</h4>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm border border-gray-200">
          <thead className="bg-gray-100">
            <tr>
              <th className="p-2 border">Subject</th>
              <th className="p-2 border">Grade</th>
              <th className="p-2 border">Edit</th>
            </tr>
          </thead>
          <tbody>
            {paginatedGrades.map((g, idx) => (
              <tr key={idx} className="hover:bg-gray-50">
                <td className="p-2 border">{g.subject}</td>
                <td className="p-2 border">
                  {g.isEditing ? (
                    <input
                      value={g.editValue}
                      onChange={(e) => setStudents((prev) =>
                        prev.map((s) =>
                          s.id === selectedStudent && s.subject === g.subject
                            ? { ...s, editValue: e.target.value }
                            : s
                        ))}
                      className="border p-1 w-16 rounded"
                    />
                  ) : (
                    g.grade
                  )}
                </td>
                <td className="p-2 border">
                  {canEdit ? (
                    g.isEditing ? (
                      <button
                        className="bg-green-500 text-white px-2 py-1 rounded"
                        onClick={async () => {
                          await api.post("/teacher/grades/update", {
                            student_id: selectedStudent,
                            subject: g.subject,
                            grade: g.editValue,
                          });
                          setStudents((prev) =>
                            prev.map((s) =>
                              s.id === selectedStudent && s.subject === g.subject
                                ? { ...s, grade: s.editValue, isEditing: false }
                                : s
                            ));
                        }}
                      >Save</button>
                    ) : (
                      <button
                        className="bg-blue-500 text-white px-2 py-1 rounded"
                        onClick={() =>
                          setStudents((prev) =>
                            prev.map((s) =>
                              s.id === selectedStudent && s.subject === g.subject
                                ? { ...s, isEditing: true, editValue: s.grade }
                                : s
                            ))
                        }
                      >Edit</button>
                    )
                  ) : (
                    <span className="text-gray-400">No Permission</span>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <div className="flex justify-between mt-3 text-sm">
        <button onClick={() => setCurrentPage((p) => Math.max(p - 1, 1))} disabled={currentPage === 1} className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Previous</button>
        <span>Page {currentPage} of {totalPages}</span>
        <button onClick={() => setCurrentPage((p) => Math.min(p + 1, totalPages))} disabled={currentPage === totalPages} className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Next</button>
      </div>
    </div>
  );
}

function AttendanceSection({ attendance, selectedStudent, setAttendance, canEdit }) {
  const [currentPage, setCurrentPage] = useState(1);
  const pageSize = 5;
  const studentAttendance = attendance.filter((a) => a.id === selectedStudent);
  const totalPages = Math.ceil(studentAttendance.length / pageSize);
  const paginatedAttendance = studentAttendance.slice((currentPage - 1) * pageSize, currentPage * pageSize);

  return (
    <div className="flex-1 min-w-[300px]">
      <h4 className="text-lg font-semibold text-green-700 mb-2">📅 Attendance</h4>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm border border-gray-200">
          <thead className="bg-gray-100">
            <tr>
              <th className="p-2 border">Date</th>
              <th className="p-2 border">Status</th>
              <th className="p-2 border">Edit</th>
            </tr>
          </thead>
          <tbody>
            {paginatedAttendance.map((a, idx) => (
              <tr key={idx} className="hover:bg-gray-50">
                <td className="p-2 border">{a.date}</td>
                <td className="p-2 border">
                  {a.isEditing ? (
                    <select
                      value={a.editValue}
                      onChange={(e) => setAttendance((prev) =>
                        prev.map((att) =>
                          att.id === selectedStudent && att.date === a.date
                            ? { ...att, editValue: e.target.value }
                            : att
                        ))}
                      className="border p-1 rounded"
                    >
                      <option value="Present">Present</option>
                      <option value="Absent">Absent</option>
                    </select>
                  ) : (
                    <span className={a.status === "Present" ? "text-green-600" : "text-red-600"}>{a.status}</span>
                  )}
                </td>
                <td className="p-2 border">
                  {canEdit ? (
                    a.isEditing ? (
                      <button
                        className="bg-green-500 text-white px-2 py-1 rounded"
                        onClick={async () => {
                          await api.post("/teacher/attendance/update", {
                            student_id: selectedStudent,
                            date: a.date,
                            status: a.editValue,
                          });
                          setAttendance((prev) =>
                            prev.map((att) =>
                              att.id === selectedStudent && att.date === a.date
                                ? { ...att, status: att.editValue, isEditing: false }
                                : att
                            ));
                        }}
                      >Save</button>
                    ) : (
                      <button
                        className="bg-blue-500 text-white px-2 py-1 rounded"
                        onClick={() =>
                          setAttendance((prev) =>
                            prev.map((att) =>
                              att.id === selectedStudent && att.date === a.date
                                ? { ...att, isEditing: true, editValue: att.status }
                                : att
                            ))
                        }
                      >Edit</button>
                    )
                  ) : (
                    <span className="text-gray-400">No Permission</span>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <div className="flex justify-between mt-3 text-sm">
        <button onClick={() => setCurrentPage((p) => Math.max(p - 1, 1))} disabled={currentPage === 1} className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Previous</button>
        <span>Page {currentPage} of {totalPages}</span>
        <button onClick={() => setCurrentPage((p) => Math.min(p + 1, totalPages))} disabled={currentPage === totalPages} className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50">Next</button>
      </div>
    </div>
  );
}
