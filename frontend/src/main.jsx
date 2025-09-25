// path: frontend/src/main.jsx

import React from "react";
// import React (จำเป็นสำหรับ JSX)

import { createRoot } from "react-dom/client";
// ใช้ createRoot API ของ React 18 เพื่อ mount แอป

import { BrowserRouter } from "react-router-dom";
// ใช้ BrowserRouter สำหรับจัดการ routing ของ React

import App from "./App";
// import Component หลักของแอป (App.jsx)

import "./index.css";
// import CSS หลัก (รวม TailwindCSS ด้วย)

// สร้าง root element จาก div#root ใน index.html
createRoot(document.getElementById("root")).render(
    <React.StrictMode>
        {/* StrictMode = ใช้ตรวจสอบ warning/deprecation ช่วง dev */}
        <BrowserRouter>
            {/* ครอบ App ด้วย BrowserRouter เพื่อให้ใช้ routing ได้ */}
            <App />
        </BrowserRouter>
    </React.StrictMode>
);
