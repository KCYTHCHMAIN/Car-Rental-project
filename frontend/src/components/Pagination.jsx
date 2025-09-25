// path: frontend/src/components/Pagination.jsx

export default function Pagination({ page, total, size, onPage }) {
    // Component สำหรับทำ pagination (เปลี่ยนหน้า)
    // props:
    // - page = หน้าปัจจุบัน
    // - total = จำนวนข้อมูลทั้งหมด
    // - size = จำนวนข้อมูลต่อหน้า
    // - onPage = callback function เวลาเปลี่ยนหน้า
  
    const pages = Math.max(1, Math.ceil(total / size));
    // คำนวณจำนวนหน้าทั้งหมด
    // Math.ceil(total/size) = ปัดขึ้นเป็นจำนวนหน้าเต็ม
    // อย่างน้อยต้องมี 1 หน้า
  
    return (
      <div className="flex gap-2 items-center justify-center my-4">
        {/* กล่อง pagination → ใช้ flex จัดกลางแนวนอน, เว้นช่อง 0.5rem, margin บน-ล่าง */}
  
        <button
          disabled={page <= 1}
          onClick={() => onPage(page - 1)}
          className="px-3 py-1 border rounded disabled:opacity-50"
        >
          Prev
        </button>
        {/* ปุ่มย้อนกลับ (Prev)
            - disabled ถ้าอยู่หน้าที่ 1
            - กดแล้วเรียก onPage(page-1) */}
  
        <span>{page} / {pages}</span>
        {/* แสดง "หน้าปัจจุบัน / จำนวนหน้าทั้งหมด" */}
  
        <button
          disabled={page >= pages}
          onClick={() => onPage(page + 1)}
          className="px-3 py-1 border rounded disabled:opacity-50"
        >
          Next
        </button>
        {/* ปุ่มถัดไป (Next)
            - disabled ถ้าอยู่หน้าสุดท้าย
            - กดแล้วเรียก onPage(page+1) */}
      </div>
    );
  }
  