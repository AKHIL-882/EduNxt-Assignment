import React, { useEffect, useState } from "react";
import api from "../../services/api";
import { useNavigate } from "react-router-dom";

export default function StudentDashboard() {
  const [profile, setProfile] = useState(null);
  const [grades, setGrades] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    Promise.all([api.get("/student/profile"), api.get("/student/grades")])
      .then(([profileRes, gradesRes]) => {
        setProfile(profileRes.data);
        setGrades(gradesRes.data);
      })
      .catch((err) => {
        setError(
          err.response?.data?.error || "Failed to fetch profile or grades"
        );
      })
      .finally(() => setLoading(false));
  }, []);

  const handleLogout = async () => {
    localStorage.removeItem("access_token");
    localStorage.removeItem("role");
    await api.post("/logout");
    navigate("/login");
  };

  return (
    <div className="min-h-screen bg-gradient-to-tr from-blue-50 to-purple-100 p-6">
      <div className="max-w-4xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h2 className="text-3xl font-extrabold text-gray-800 flex items-center gap-2">
            🎓 Student Dashboard
          </h2>
          <button
            onClick={handleLogout}
            className="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-full hover:from-red-600 hover:to-red-700 transition duration-200 shadow-lg"
          >
            Logout
          </button>
        </div>

        {loading ? (
          <div className="text-center text-lg text-gray-700">Loading...</div>
        ) : error ? (
          <div className="text-red-600 text-center">{error}</div>
        ) : (
          <>
            {/* Profile Card */}
            {profile && (
              <div className="bg-white rounded-2xl shadow-md p-6 mb-8">
                <h3 className="text-xl font-semibold mb-4 text-blue-800">
                  👤 Profile Info
                </h3>
                <div className="text-gray-700 space-y-2">
                  <p>
                    <span className="font-medium">Name:</span> {profile.name}
                  </p>
                  <p>
                    <span className="font-medium">Email:</span> {profile.email}
                  </p>
                </div>
              </div>
            )}

            {/* Grades Table */}
            <div className="bg-white rounded-2xl shadow-md p-6">
              <h3 className="text-xl font-semibold mb-4 text-green-800">
                📚 Grades Overview
              </h3>
              <div className="overflow-x-auto">
                <table className="min-w-full text-left border border-gray-200 rounded-md overflow-hidden">
                  <thead>
                    <tr className="bg-gray-100 text-gray-700">
                      <th className="p-3 border-b">Subject</th>
                      <th className="p-3 border-b">Grade</th>
                    </tr>
                  </thead>
                  <tbody>
                    {grades.length === 0 ? (
                      <tr>
                        <td
                          colSpan={2}
                          className="p-4 text-center text-gray-500 italic"
                        >
                          No grades available
                        </td>
                      </tr>
                    ) : (
                      grades.map((g, idx) => (
                        <tr
                          key={idx}
                          className="hover:bg-gray-50 transition duration-200"
                        >
                          <td className="p-3 border-b">{g.subject}</td>
                          <td className="p-3 border-b font-semibold text-indigo-600">
                            {g.grade}
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
