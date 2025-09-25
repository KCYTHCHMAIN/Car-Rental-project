// path: frontend/src/components/Navbar.jsx

import { Link } from "react-router-dom";
// import Link ของ React Router สำหรับลิงก์ภายใน SPA

import { useAuth } from "../hooks/useAuth";
// import hook useAuth → ใช้ตรวจสอบ user ปัจจุบัน + ฟังก์ชัน logout

export default function Navbar() {
  const { user, logout } = useAuth();
  // ดึง user และ logout function จาก context (AuthProvider)

  return (
    <nav className="bg-white border-b">
      {/* navbar พื้นหลังสีขาว + เส้นขอบด้านล่าง */}

      <div className="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        {/* container กำหนดความกว้างสูงสุด 6xl, จัดกึ่งกลาง, padding, flex สำหรับจัด layout */}

        <Link to="/" className="font-bold text-lg">CarRental</Link>
        {/* โลโก้/ชื่อเว็บ กดแล้วกลับหน้าแรก */}

        <div className="flex items-center gap-4">
          {/* กล่องเก็บเมนูด้านขวา → จัดเรียงแบบ flex, เว้นระยะ 1rem */}

          <Link to="/cars">Cars</Link>
          {/* ลิงก์ไปหน้ารถทั้งหมด */}

          {user ? (
            <>
              {/* ถ้า user login แล้ว */}
              <Link to="/account">Account</Link>
              {/* ลิงก์ไปหน้า Account (โปรไฟล์/การจอง) */}

              {(user.role === "owner" || user.role === "staff") && (
                <Link to="/admin">Admin</Link>
              )}
              {/* ถ้า role = owner หรือ staff → แสดงลิงก์ Admin */}

              <button onClick={logout} className="px-3 py-1 border rounded">
                Logout
              </button>
              {/* ปุ่มออกจากระบบ */}
            </>
          ) : (
            <Link to="/account" className="px-3 py-1 border rounded">
              Login
            </Link>
            /* ถ้า user ยังไม่ login → แสดงปุ่ม Login (พาไปหน้า Account) */
          )}
        </div>
      </div>
    </nav>
  );
}
