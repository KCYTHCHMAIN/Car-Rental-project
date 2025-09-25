// path: frontend/src/pages/Cars.jsx

import api from "../lib/api";
// import api client (ky instance) สำหรับเรียก backend

import useQueryParams from "../hooks/useQueryParams";
// hook จัดการ query string (เช่น ?page=2)

import useFetch from "../hooks/useFetch";
// hook ดึงข้อมูลแบบ async (data, loading, err)

import Pagination from "../components/Pagination";
// component แสดง pagination (Prev / Next)

import CarCard from "../components/CarCard";
// component แสดงการ์ดรถทีละคัน

export default function Cars() {
  // Component แสดงหน้ารายการรถทั้งหมด

  const { params, set } = useQueryParams();
  // ใช้ hook query params
  // - params = อ่าน query string
  // - set = เปลี่ยน query string

  const page = Number(params.get("page") || 1);
  // อ่านค่า page จาก query string (default = 1)

  const size = 12;
  // จำนวนข้อมูลต่อหน้า (12 คัน/หน้า)

  const { data } = useFetch(
    () => api.get(`cars?page=${page}&size=${size}`).json(),
    [page]
  );
  // ดึงข้อมูลรถจาก API
  // ถ้า page เปลี่ยน → useFetch จะโหลดใหม่
  // data = { items: [...], total: ... }

  return (
    <div>
      <h2 className="text-xl font-semibold mb-3">รถทั้งหมด</h2>
      {/* หัวข้อหน้า (ขนาด xl, ตัวหนา, margin-bottom 0.75rem) */}

      <div className="grid md:grid-cols-3 gap-4">
        {/* แสดงรายการรถแบบ grid → 3 คอลัมน์บนจอ md+, ช่องว่าง 1rem */}
        {data?.items?.map((c) => (
          <CarCard key={c.id} car={c} />
        ))}
        {/* วนลูปข้อมูลรถ → แสดง CarCard สำหรับแต่ละคัน */}
      </div>

      <Pagination 
        page={page} 
        total={data?.total || 0} 
        size={size} 
        onPage={(p)=>set({ page: p })} 
      />
      {/* แสดง pagination ด้านล่าง
          - page = หน้าปัจจุบัน
          - total = จำนวนรถทั้งหมด
          - size = 12 ต่อหน้า
          - onPage = เปลี่ยน query string page */}
    </div>
  );
}
