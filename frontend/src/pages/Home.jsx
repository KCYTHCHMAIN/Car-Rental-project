// path: frontend/src/pages/Home.jsx

import { Link } from "react-router-dom";
// import Link จาก React Router → ใช้ทำ navigation ไปหน้ารถ

export default function Home() {
  // Component หน้าแรก (Homepage)

  return (
    <section className="grid md:grid-cols-2 gap-6 items-center">
      {/* section หลัก → layout แบบ grid 
          - บนมือถือ = 1 คอลัมน์
          - บนจอ md ขึ้นไป = 2 คอลัมน์
          - gap-6 = ระยะห่าง 1.5rem
          - items-center = จัดแนวตั้งให้อยู่ตรงกลาง */}

      <div>
        {/* ฝั่งซ้าย: ข้อความ + ปุ่ม */}
        <h1 className="text-3xl font-bold mb-3">เช่ารถ ง่าย เร็ว โปร่งใส</h1>
        {/* หัวข้อใหญ่ (3xl), ตัวหนา, margin-bottom 0.75rem */}

        <p className="mb-4">
          ค้นหา เปรียบเทียบ จอง และอัปโหลดเอกสารได้ทันที
        </p>
        {/* คำอธิบายสั้น ๆ, margin-bottom 1rem */}

        <Link to="/cars" className="px-4 py-2 border rounded">
          เริ่มค้นหา
        </Link>
        {/* ปุ่ม → ลิงก์ไปหน้ารายการรถ */}
      </div>

      <div className="aspect-video bg-gray-100 rounded" />
      {/* ฝั่งขวา: placeholder สำหรับรูปภาพ/วิดีโอโปรโมท
          - aspect-video = อัตราส่วน 16:9
          - bg-gray-100 = พื้นหลังเทาอ่อน
          - rounded = มุมโค้ง */}
    </section>
  );
}
