// path: frontend/src/components/CarCard.jsx

import { Link } from "react-router-dom";
// import Link ของ React Router → ใช้ทำ navigation ไปหน้า CarDetail

export default function CarCard({ car }) {
  // Component สำหรับแสดง "การ์ดรถ" ทีละคัน
  // รับ props: car (object ที่มีข้อมูลรถ)

  return (
    <div className="bg-white rounded-xl shadow p-4 flex flex-col">
      {/* กล่องหลักของการ์ดรถ → พื้นหลังขาว, มุมโค้ง, เงา, padding, จัด layout แบบ column */}

      <div className="aspect-video bg-gray-100 rounded mb-3" />
      {/* กล่องรูปภาพ (ตอนนี้เป็น placeholder พื้นหลังเทา ขนาดอัตราส่วน 16:9) */}

      <div className="font-semibold">
        {car.brand} {car.model}
      </div>
      {/* แสดงชื่อรถ = ยี่ห้อ + รุ่น */}

      <div className="text-sm opacity-70">
        {car.type} • {car.gear} • {car.seats} seats
      </div>
      {/* แสดงรายละเอียดเสริม: ประเภท, เกียร์, จำนวนที่นั่ง */}

      <div className="mt-2 font-bold">
        {car.price_per_day} ฿ / day
      </div>
      {/* แสดงราคา/วัน (ตัวหนา) */}

      <Link 
        to={`/cars/${car.id}`} 
        className="mt-3 px-3 py-2 border rounded text-center"
      >
        View
      </Link>
      {/* ปุ่มลิงก์ไปหน้ารายละเอียดรถ (CarDetail) */}
    </div>
  );
}
