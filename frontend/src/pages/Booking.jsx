// path: frontend/src/pages/Booking.jsx

import { useParams, useSearchParams, useNavigate } from "react-router-dom";
// useParams → ดึง parameter จาก path (/booking/:id)
// useSearchParams → ดึง query string (?start=...&end=...)
// useNavigate → ใช้เปลี่ยนหน้า (redirect)

import { useAuth } from "../hooks/useAuth";
// hook ใช้เช็ค user ปัจจุบัน (login อยู่หรือไม่)

import api from "../lib/api";
// api client (ky instance)

import { useState } from "react";
// React state

export default function Booking() {
  const { id } = useParams();
  // id รถที่ถูกเลือกมาจาก path (/booking/:id)

  const [sp] = useSearchParams();
  // ใช้ search params อ่านค่า start, end จาก URL

  const { user } = useAuth();
  // ดึงข้อมูล user ปัจจุบัน (ถ้าไม่ login จะเป็น null)

  const nav = useNavigate();
  // ใช้สำหรับ redirect ไปหน้าต่าง ๆ

  const [pickup, setPickup] = useState("สนามบิน");
  // state: สถานที่รับรถ (default = สนามบิน)

  const [dropoff, setDropoff] = useState("ตัวเมือง");
  // state: สถานที่คืนรถ (default = ตัวเมือง)

  const [coupon, setCoupon] = useState("");
  // state: รหัสคูปอง (default = ว่าง)

  const start = sp.get("start");
  const end = sp.get("end");
  // ดึงค่า start, end จาก query string

  const days = Math.max(
    1,
    Math.ceil((new Date(end) - new Date(start)) / 86400000)
  );
  // คำนวณจำนวนคืนที่เช่า
  // (end - start) / 86400000 = จำนวนวัน
  // ใช้ Math.ceil → ปัดขึ้น
  // อย่างน้อยต้องเช่า 1 คืน

  const [subtotal, setSubtotal] = useState(1500 * days);
  // state: ราคารวมก่อนหักส่วนลด
  // (default: 1500 บาท/วัน * จำนวนคืน)

  const submit = async () => {
    // ฟังก์ชันยืนยันการจอง
    if (!user) { 
      nav("/account"); 
      return; 
    }
    // ถ้าไม่ได้ login → พาไปหน้า Account (login)

    const res = await api.post("bookings", {
      json: {
        car_id: Number(id),
        start_at: `${start} 10:00:00`,
        end_at: `${end} 10:00:00`,
        pickup_location: pickup,
        dropoff_location: dropoff,
        options: {},
        coupon_code: coupon || null,
        subtotal
      }
    }).json();
    // ส่ง request ไป backend เพื่อสร้าง booking
    // - car_id = id รถ
    // - start/end = วันที่เลือก (fix เวลา 10:00)
    // - pickup/dropoff = สถานที่รับ/คืน
    // - coupon_code = ถ้ามีคูปอง
    // - subtotal = ยอดก่อนหักส่วนลด

    alert(`จองสำเร็จ #${res.booking_id} รวม ${res.total} บาท`);
    // แจ้งเตือนว่าจองสำเร็จ พร้อมแสดง booking_id และราคารวม

    nav("/account");
    // ย้ายไปหน้า Account (ดูรายละเอียดการจอง)
  };

  return (
    <div className="max-w-xl">
      {/* กล่องหลักของฟอร์ม booking, จำกัดความกว้างสุด = xl */}

      <h2 className="text-xl font-semibold mb-3">ยืนยันการจอง</h2>
      {/* หัวข้อ */}

      <div className="space-y-3">
        {/* ฟอร์ม input แต่ละช่อง → มีระยะห่าง 0.75rem */}

        <div>
          <b>ช่วงเวลา</b>: {start} → {end} ({days} คืน)
        </div>
        {/* แสดงช่วงเวลาที่เลือก + จำนวนคืน */}

        <label className="block">
          สถานที่รับ 
          <input 
            className="border rounded w-full px-2 py-1" 
            value={pickup} 
            onChange={e=>setPickup(e.target.value)} 
          />
        </label>

        <label className="block">
          สถานที่คืน 
          <input 
            className="border rounded w-full px-2 py-1" 
            value={dropoff} 
            onChange={e=>setDropoff(e.target.value)} 
          />
        </label>

        <label className="block">
          คูปอง 
          <input 
            className="border rounded w-full px-2 py-1" 
            value={coupon} 
            onChange={e=>setCoupon(e.target.value)} 
            placeholder="WELCOME10" 
          />
        </label>

        <label className="block">
          ยอดก่อนส่วนลด 
          <input 
            type="number" 
            className="border rounded w-full px-2 py-1" 
            value={subtotal} 
            onChange={e=>setSubtotal(Number(e.target.value))} 
          />
        </label>

        <button 
          onClick={submit} 
          className="px-4 py-2 border rounded"
        >
          จ่ายด้วยการโอน/สลิป (MVP)
        </button>
        {/* ปุ่มยืนยันการจอง (ใน MVP ใช้การโอน/สลิปแทนระบบจ่ายเงินจริง) */}
      </div>
    </div>
  );
}
