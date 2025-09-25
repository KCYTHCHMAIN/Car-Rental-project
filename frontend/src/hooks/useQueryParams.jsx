// path: frontend/src/hooks/useQueryParams.jsx

import { useLocation, useNavigate } from "react-router-dom";
// import hook จาก React Router
// - useLocation = ใช้ดึงข้อมูล path และ query string ปัจจุบัน
// - useNavigate = ใช้เปลี่ยน URL (navigation)

export default function useQueryParams() {
  const { search, pathname } = useLocation();
  // search = ส่วน query string (เช่น "?page=2&filter=car")
  // pathname = path ปัจจุบัน (เช่น "/cars")

  const nav = useNavigate();
  // ฟังก์ชันสำหรับเปลี่ยน URL (redirect / push state)

  const params = new URLSearchParams(search);
  // แปลง query string → object ของ URLSearchParams (ใช้ get(), set(), delete())

  const set = (obj) => {
    // ฟังก์ชัน set() ใช้แก้ไข query string แล้วอัปเดต URL
    const p = new URLSearchParams(search);
    // clone query string ปัจจุบัน

    Object.entries(obj).forEach(([k,v]) => 
      v == null ? p.delete(k) : p.set(k, String(v))
    );
    // loop obj ที่ส่งเข้ามา
    // - ถ้า value = null/undefined → ลบ key ออกจาก query string
    // - ถ้ามีค่า → set key=value

    nav({ pathname, search: p.toString() });
    // เปลี่ยน URL เป็น path เดิม + query string ใหม่
  };

  return { params, set };
  // คืนค่า object: params (อ่านค่า query) + set (อัปเดต query)
}
