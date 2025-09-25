// path: frontend/src/pages/Account.jsx

import { useAuth } from "../hooks/useAuth";
// ใช้ context/hook สำหรับจัดการสถานะผู้ใช้ (login/register/logout)

import api from "../lib/api";
// HTTP client (ky instance) สำหรับเรียก backend API

import useFetch from "../hooks/useFetch";
// hook สำหรับดึงข้อมูล async (คืน {data, loading, err})

import { useState } from "react";
// ใช้ useState จัดการ state ฟอร์ม

export default function Account() {
  const { user, login, register, logout } = useAuth();
  // ดึงข้อมูลผู้ใช้ + ฟังก์ชัน auth จาก context

  const [email,setEmail] = useState("user@example.com");
  // state อีเมล (ค่าเริ่มต้นเพื่อทดสอบ)

  const [password,setPassword] = useState("password");
  // state รหัสผ่าน (ค่าเริ่มต้นเพื่อทดสอบ)

  const [name,setName] = useState("User");
  // state ชื่อ (สำหรับสมัครสมาชิก)

  const { data, loading } = useFetch(
    () => user ? api.get("bookings").json() : Promise.resolve(null), 
    [user]
  );
  // ถ้า login แล้ว → โหลดรายการจองของผู้ใช้
  // ถ้ายังไม่ login → ไม่โหลดอะไร (คืน null)

  if (!user) {
    // สถานะยังไม่ล็อกอิน → แสดงฟอร์ม Login และ Register
    return (
      <div className="grid md:grid-cols-2 gap-6">
        {/* layout สองคอลัมน์บนจอ md+, ช่องว่าง 1.5rem */}

        <div>
          <h3 className="font-semibold mb-2">เข้าสู่ระบบ</h3>
          <div className="space-y-2">
            <input 
              className="border rounded w-full px-2 py-1" 
              value={email} 
              onChange={e=>setEmail(e.target.value)} 
            />
            {/* ช่องกรอกอีเมล */}

            <input 
              type="password" 
              className="border rounded w-full px-2 py-1" 
              value={password} 
              onChange={e=>setPassword(e.target.value)} 
            />
            {/* ช่องกรอกรหัสผ่าน */}

            <button 
              onClick={()=>login(email,password)} 
              className="px-3 py-1 border rounded"
            >
              Login
            </button>
            {/* ปุ่มเข้าสู่ระบบ → เรียก useAuth().login */}
          </div>
        </div>

        <div>
          <h3 className="font-semibold mb-2">สมัครสมาชิก</h3>
          <div className="space-y-2">
            <input 
              placeholder="ชื่อ" 
              className="border rounded w-full px-2 py-1" 
              value={name} 
              onChange={e=>setName(e.target.value)} 
            />
            {/* ชื่อผู้ใช้ */}

            <input 
              placeholder="อีเมล" 
              className="border rounded w-full px-2 py-1" 
              value={email} 
              onChange={e=>setEmail(e.target.value)} 
            />
            {/* อีเมล */}

            <input 
              placeholder="รหัสผ่าน" 
              type="password" 
              className="border rounded w-full px-2 py-1" 
              value={password} 
              onChange={e=>setPassword(e.target.value)} 
            />
            {/* รหัสผ่าน */}

            <button 
              onClick={()=>register(name,email,password)} 
              className="px-3 py-1 border rounded"
            >
              Register
            </button>
            {/* ปุ่มสมัครสมาชิก → สมัครเสร็จระบบจะ login อัตโนมัติ */}
          </div>
        </div>
      </div>
    );
  }

  // กรณีล็อกอินแล้ว → แสดงข้อมูลบัญชี + รายการจอง + อัปโหลดเอกสาร
  return (
    <div>
      <div className="flex items-center justify-between mb-3">
        {/* แถวหัวข้อและปุ่ม logout */}
        <h2 className="text-xl font-semibold">บัญชีของฉัน</h2>
        <button onClick={logout} className="px-3 py-1 border rounded">Logout</button>
      </div>

      <h3 className="font-semibold mb-2">การจองล่าสุด</h3>
      <div className="space-y-2">
        {loading && <div>กำลังโหลด…</div>}
        {/* แสดงสถานะกำลังโหลดระหว่างเรียก API */}

        {data?.items?.map(b=>(
          <div key={b.id} className="bg-white border rounded p-3">
            #{b.id} • {b.start_at} → {b.end_at} • {b.status} • รวม {b.total} ฿
          </div>
        ))}
        {/* แสดงรายการการจองของผู้ใช้ (ล่าสุดก่อน) */}
      </div>

      <div className="mt-6">
        <h3 className="font-semibold mb-2">อัปโหลดเอกสาร</h3>
        <input 
          type="file" 
          onChange={async e=>{
            if (!e.target.files?.length) return;
            // ถ้าไม่ได้เลือกไฟล์ → ไม่ทำอะไร

            const fd = new FormData(); 
            fd.append("file", e.target.files[0]);
            // สร้าง FormData แล้วแนบไฟล์

            const res = await fetch(
              (import.meta.env.VITE_API_BASE || "http://localhost:8080/api") + "/uploads", 
              { 
                method:"POST", 
                headers:{ Authorization:`Bearer ${localStorage.getItem("token")}` }, 
                body: fd 
              }
            );
            // ใช้ fetch อัปโหลดไฟล์ไปยัง /api/uploads
            // แนบ header Authorization (JWT) และ body = FormData

            const j = await res.json();
            // อ่านผลลัพธ์เป็น JSON

            alert("อัปโหลดแล้ว: " + j.url);
            // แจ้งผู้ใช้ว่าอัปโหลดเสร็จ พร้อม URL ที่จัดเก็บ
          }} 
        />
        {/* ช่องเลือกไฟล์เพื่ออัปโหลดเอกสาร (ใบขับขี่/บัตรประชาชน ฯลฯ) */}
      </div>
    </div>
  );
}
