// path: frontend/src/lib/api.js

import ky from "ky";
// import ky (HTTP client เบา ๆ ใช้แทน fetch)

const api = ky.create({
  // สร้าง instance ของ ky ไว้ใช้เรียก API

  prefixUrl: import.meta.env.VITE_API_BASE || "http://localhost:8080/api",
  // base URL ของ API (ดึงจาก env: VITE_API_BASE)
  // ถ้าไม่มีค่า → ใช้ค่า default http://localhost:8080/api

  headers: { "Content-Type": "application/json" },
  // ตั้งค่า header เริ่มต้น: ส่งข้อมูลเป็น JSON

  hooks: {
    beforeRequest: [
      req => {
        const token = localStorage.getItem("token");
        // อ่าน JWT token จาก localStorage (เก็บตอน login)

        if (token) 
          req.headers.set("Authorization", `Bearer ${token}`);
        // ถ้ามี token → ใส่ Authorization header: Bearer <token>
      },
    ],
  },
});

export default api;
// export instance api ไปใช้เรียก API อื่น ๆ ได้ง่ายขึ้น
