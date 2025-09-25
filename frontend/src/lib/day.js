// path: frontend/src/lib/day.js

import dayjs from "dayjs";
// import ไลบรารี dayjs (ใช้จัดการวันที่/เวลา)

import utc from "dayjs/plugin/utc";
// import plugin สำหรับจัดการเวลาแบบ UTC

import timezone from "dayjs/plugin/timezone";
// import plugin สำหรับจัดการ timezone

dayjs.extend(utc);
// เปิดใช้งาน plugin utc

dayjs.extend(timezone);
// เปิดใช้งาน plugin timezone

dayjs.tz.setDefault("Asia/Bangkok");
// ตั้งค่า timezone เริ่มต้นเป็น Asia/Bangkok (เวลาไทย)

export default dayjs;
// export dayjs ที่ถูก config แล้วไปใช้ทั่วแอป
