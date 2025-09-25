// path: frontend/src/hooks/useFetch.jsx

import { useEffect, useState } from "react";
// import React hooks: useState และ useEffect

export default function useFetch(fn, deps = []) {
  // custom hook: ใช้สำหรับ fetch ข้อมูลแบบ async
  // fn = ฟังก์ชัน async ที่ return promise (เช่น api call)
  // deps = dependencies array (เหมือน useEffect)

  const [data, setData] = useState(null),
        [loading, setLoading] = useState(true),
        [err, setErr] = useState(null);
  // state:
  // data = ข้อมูลที่ได้จาก fn()
  // loading = true ระหว่างกำลังโหลด
  // err = เก็บ error ถ้าเกิดข้อผิดพลาด

  useEffect(() => {
    let on = true;  
    // flag สำหรับตรวจสอบว่า component ยัง mount อยู่

    setLoading(true);
    // เริ่มโหลด → ตั้ง loading = true

    fn()
      .then(d => on && setData(d))
      // ถ้าสำเร็จ → setData เฉพาะตอน component ยังไม่ถูก unmount

      .catch(e => on && setErr(e))
      // ถ้า error → setErr (เฉพาะตอนยัง mount)

      .finally(() => on && setLoading(false));
      // เสร็จแล้ว → setLoading(false)

    return () => { on = false };
    // cleanup function: ถ้า component unmount → set flag เป็น false
    // ป้องกัน memory leak (เช่น state update หลัง unmount)
  }, deps);
  // rerun effect เมื่อค่าใน deps เปลี่ยน

  return { data, loading, err };
  // คืน object → ใช้งานใน component
}
