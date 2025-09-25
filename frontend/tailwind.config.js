// path: frontend/tailwind.config.js

/** @type {import('tailwindcss').Config} */
// บอก VSCode/IDE ว่าไฟล์นี้เป็น config ของ Tailwind (ช่วย auto-complete)

export default {
    content: ["./index.html","./src/**/*.{js,jsx,ts,tsx}"],
    // บอก Tailwind ว่าไฟล์ไหนต้องสแกนเพื่อ generate class
    // - index.html (ไฟล์หลัก)
    // - src/** ทุกไฟล์ .js, .jsx, .ts, .tsx (โค้ด React)
  
    theme: { 
      extend: {} 
      // ส่วนปรับแต่ง theme (สี, ฟอนต์, spacing, ฯลฯ)
      // ตอนนี้ยังว่าง → ใช้ค่า default ของ Tailwind
    },
  
    plugins: [],
    // ติดตั้ง plugin ของ Tailwind เพิ่มได้ เช่น forms, typography, aspect-ratio
    // ตัวอย่าง: require('@tailwindcss/forms')
  }
  