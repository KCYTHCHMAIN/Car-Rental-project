// path: frontend/src/pages/Admin.jsx

import api from "../lib/api";
// import HTTP client (ky instance) สำหรับเรียก backend

import useFetch from "../hooks/useFetch";
// import hook สำหรับดึงข้อมูลแบบ async (คืน { data, loading, err })

import { useState } from "react";
// import useState สำหรับจัดการฟอร์มเพิ่มรถ และ state อื่น ๆ

export default function Admin() {
  // หน้าแอดมิน: ดู/เปลี่ยนสถานะการจอง + เพิ่มรถใหม่

  const { data } = useFetch(()=>api.get("bookings").json(), []);
  // โหลดรายการจองทั้งหมด (สำหรับ owner/staff เท่านั้น)
  // data?.items = รายการ booking ล่าสุด

  const [car, setCar] = useState({
    // state ฟอร์มเพิ่มรถใหม่ (ค่าเริ่มต้น)
    type:"Sedan",
    brand:"Brand",
    model:"Model",
    gear:"AT",
    seats:5,
    color:"White",
    smoking:0,
    with_driver:0,
    price_per_day:1000,
    images:[]
  });

  const createCar = async () => {
    // ฟังก์ชันเรียก API เพื่อเพิ่มรถใหม่
    await api.post("cars", { json: car }).json();
    alert("เพิ่มรถแล้ว");
  };

  const setStatus = async (id, status) => {
    // ฟังก์ชันเปลี่ยนสถานะ booking (confirmed/cancelled/…)
    await api.post("bookings/status", { json: { booking_id:id, status } }).json();
    location.reload();
    // หลังอัปเดตสถานะ เสร็จแล้ว reload หน้าเพื่อดึงข้อมูลล่าสุด
  };

  return (
    <div className="grid md:grid-cols-2 gap-6">
      {/* layout สองคอลัมน์: ซ้าย = รายการจอง, ขวา = ฟอร์มเพิ่มรถ */}

      <div>
        <h3 className="font-semibold mb-2">การจองทั้งหมด</h3>
        <div className="space-y-2">
          {/* เว้นระยะระหว่างการ์ดจองแต่ละรายการ */}

          {data?.items?.map(b=>(
            <div key={b.id} className="bg-white border rounded p-3">
              {/* การ์ดแสดงรายละเอียดการจองแต่ละรายการ */}
              #{b.id} • car:{b.car_id} • {b.start_at} → {b.end_at} • <b>{b.status}</b>
              {/* แสดงหมายเลขจอง, รหัสรถ, ช่วงเวลา, และสถานะปัจจุบัน */}

              <div className="mt-2 flex gap-2">
                {/* ปุ่มเปลี่ยนสถานะ booking */}
                {["confirmed","cancelled","checked_in","checked_out","refunded"].map(s=>
                  <button 
                    key={s} 
                    onClick={()=>setStatus(b.id,s)} 
                    className="px-2 py-1 border rounded"
                  >
                    {s}
                  </button>
                )}
                {/* วนสร้างปุ่มสถานะจากรายการด้านบน */}
              </div>
            </div>
          ))}
        </div>
      </div>

      <div>
        <h3 className="font-semibold mb-2">เพิ่มรถ</h3>

        <div className="grid grid-cols-2 gap-2">
          {/* ฟอร์มเพิ่มรถแบบสองคอลัมน์ */}

          {Object.keys(car).filter(k=>k!=="images").map(k=>(
            <input
              key={k}
              placeholder={k}
              className="border rounded px-2 py-1"
              value={car[k]}
              onChange={e=>setCar({
                ...car,
                [k]: e.target.value
                // หมายเหตุ: ค่าที่ได้จาก input เป็น string ทั้งหมด
                // ถ้าต้องการเป็นตัวเลขจริง ๆ (seats, price_per_day, smoking, with_driver)
                // อาจต้องแปลงเป็น Number/parseInt ก่อนส่ง (ปรับเพิ่มได้ภายหลัง)
              })}
            />
          ))}
          {/* วนสร้าง input ตาม key ของ state car (ยกเว้น images) */}
        </div>

        <button 
          onClick={createCar} 
          className="mt-3 px-3 py-1 border rounded"
        >
          บันทึก
        </button>
        {/* ปุ่มบันทึกเพื่อเรียก API เพิ่มรถ */}
      </div>
    </div>
  );
}
