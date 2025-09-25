# path: README.md

## Run (dev)
1) `cp infra/.env.example infra/.env` (or export envs)
   <!-- ขั้นตอนแรก: คัดลอกไฟล์ environment ตัวอย่างไปเป็น .env ที่ infra/ (หรือจะตั้งค่า env ผ่าน export ก็ได้) -->

2) `docker compose -f infra/docker-compose.yml up --build`
   <!-- ขั้นตอนสอง: รัน docker compose โดยใช้ไฟล์ config ที่ infra/docker-compose.yml และ build image ใหม่ -->

3) Frontend: http://localhost:5173 • Backend: http://localhost:8080/api • Mailhog: http://localhost:8025
   <!-- ขั้นตอนสาม: หลังรันเสร็จจะเข้าใช้งานได้ตามนี้ 
        - Frontend ที่ http://localhost:5173
        - Backend API ที่ http://localhost:8080/api
        - Mailhog (เครื่องมือดักจับอีเมล dev) ที่ http://localhost:8025 -->

## Notes
- กันจองซ้อน: Transaction + `SELECT ... FOR UPDATE` + `idx_car_time`.
  <!-- ระบบป้องกันการจองซ้ำซ้อน ด้วยการใช้ Transaction + Lock แถว (SELECT ... FOR UPDATE) + Index สำหรับ car_id + ช่วงเวลา -->

- เวลา: Asia/Bangkok.
  <!-- ระบบตั้ง timezone หลักเป็น Asia/Bangkok -->

- Security: JWT Bearer, CORS allow configured origin, basic rate limit, MIME/type check uploads.
  <!-- ด้านความปลอดภัย: 
       - ใช้ JWT Bearer สำหรับ auth 
       - CORS จำกัด origin ที่อนุญาต 
       - มี rate limit เบื้องต้น 
       - ตรวจสอบ MIME/type ของไฟล์อัปโหลด -->

- ต่อ Payment Gateway ภายหลัง; ตอนนี้ใช้ “โอน/สลิป” เป็น placeholder.
  <!-- ระบบจ่ายเงินยังไม่เชื่อมต่อ Payment Gateway จริง 
       ตอนนี้ใช้วิธี "โอนเงิน/อัปโหลดสลิป" เป็น placeholder -->
