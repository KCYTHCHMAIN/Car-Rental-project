// path: frontend/src/routes.jsx

import { Routes, Route, Navigate } from "react-router-dom";
// import component ของ React Router สำหรับจัดการ routing
// - Routes = กล่องรวมเส้นทาง
// - Route = เส้นทางแต่ละอัน
// - Navigate = ใช้เปลี่ยนเส้นทาง (redirect)

import Home from "./pages/Home";           // หน้าแรก (homepage)
import Cars from "./pages/Cars";           // หน้าแสดงรายการรถ
import CarDetail from "./pages/CarDetail"; // หน้าแสดงรายละเอียดรถ
import Booking from "./pages/Booking";     // หน้าเช่ารถ (จอง)
import Account from "./pages/Account";     // หน้าโปรไฟล์/บัญชีผู้ใช้
import Admin from "./pages/Admin";         // หน้า admin panel

import { useAuth } from "./hooks/useAuth"; 
// hook ใช้ดึงข้อมูลการล็อกอินปัจจุบัน

export default function RoutesView() {
  const { user } = useAuth();  
  // ดึง user จาก context/auth state

  const isAdmin = user && (user.role === "owner" || user.role === "staff");
  // ตรวจสอบสิทธิ์ admin (owner, staff เท่านั้น)

  return (
    <Routes>
      {/* เส้นทางทั้งหมดของเว็บ */}

      <Route path="/" element={<Home />} />
      {/* path "/" → หน้าแรก */}

      <Route path="/cars" element={<Cars />} />
      {/* path "/cars" → แสดงรายการรถทั้งหมด */}

      <Route path="/cars/:id" element={<CarDetail />} />
      {/* path "/cars/:id" → แสดงรายละเอียดรถตาม id */}

      <Route path="/booking/:id" element={<Booking />} />
      {/* path "/booking/:id" → หน้าจองรถ (เลือก option/ยืนยัน) */}

      <Route path="/account" element={<Account />} />
      {/* path "/account" → หน้าโปรไฟล์ผู้ใช้ (my bookings, settings) */}

      <Route 
        path="/admin" 
        element={isAdmin ? <Admin /> : <Navigate to="/" />} 
      />
      {/* path "/admin" → ถ้าเป็น admin (owner/staff) ให้เข้าได้ 
          ถ้าไม่ใช่ → redirect ไปหน้า "/" */}

      <Route path="*" element={<Navigate to="/" />} />
      {/* path อื่น ๆ ที่ไม่ match → redirect ไป "/" */}
    </Routes>
  );
}
