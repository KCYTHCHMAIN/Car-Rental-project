// path: frontend/src/pages/CarDetail.jsx

import { useParams, Link, useNavigate } from "react-router-dom";
// useParams → ใช้ดึง parameter จาก URL (เช่น /cars/:id)
// useNavigate → ใช้สำหรับเปลี่ยนหน้า (redirect)
// Link → ลิงก์ไปหน้าอื่น (ตอนนี้ยังไม่ได้ใช้ แต่ import ไว้)

import api from "../lib/api";
// API client (ky instance)

import useFetch from "../hooks/useFetch";
// custom hook สำหรับดึงข้อมูลจาก API (มี data, loading, err)

import DateRangePicker from "../components/DateRangePicker";
// component สำหรับเลือกช่วงวันที่

import { useState } from "react";
// React state

export default function CarDetail() {
  const { id } = useParams();
  // ดึงค่า id จาก path เช่น /cars/5

  const nav = useNavigate();
  // ฟังก์ชันใช้เปลี่ยนหน้า (ไป booking)

  const { data: car } = useFetch(
    () => api.get(`cars/show?id=${id}`).json(),
    [id]
  );
  // ดึงข้อมูลรายละเอียดรถจาก API `/cars/show?id=...`

  const [range, setRange] = useState();
  // เก็บช่วงวันที่เลือก (start, end)

  const [busy, setBusy] = useState([]);
  // เก็บรายการช่วงที่รถถูกจองแล้ว (busy slots)

  const check = async (r) => {
    // ฟังก์ชันตรวจสอบความว่างของรถในช่วงวัน
    setRange(r);
    const res = await api
      .get(`cars/availability?id=${id}&from=${r.start} 00:00:00&to=${r.end} 23:59:59`)
      .json();
    // เรียก API ตรวจสอบ availability

    setBusy(res.busy);
    // เก็บผลลัพธ์ busy slots ลง state
  };

  return (
    <div>
      <h2 className="text-xl font-semibold mb-2">
        {car?.brand} {car?.model}
      </h2>
      {/* ชื่อรถ: ยี่ห้อ + รุ่น */}

      <p className="opacity-70 mb-3">
        {car?.type} • {car?.gear} • {car?.seats} ที่นั่ง
      </p>
      {/* รายละเอียดเสริม: ประเภท, เกียร์, จำนวนที่นั่ง */}

      <DateRangePicker value={range} onChange={check} />
      {/* component เลือกช่วงวัน → เรียก check() ทุกครั้งที่เปลี่ยน */}

      <div className="mt-2 text-sm">
        {busy.length ? "ช่วงที่ซ้อน: " + busy.length : "ว่างในช่วงที่เลือก"}
      </div>
      {/* แสดงข้อความว่ารถว่าง หรือมีการจองทับช่วง (จำนวนกี่ช่วง) */}

      <button
        onClick={() =>
          nav(`/booking/${id}?start=${range?.start}&end=${range?.end}`)
        }
        className="mt-4 px-4 py-2 border rounded"
        disabled={!range}
      >
        จองคันนี้
      </button>
      {/* ปุ่มจอง → จะพาไปหน้า /booking/:id พร้อม query string start, end
          disabled ถ้ายังไม่เลือกช่วงวัน */}

      <div className="mt-6 aspect-video bg-gray-100 rounded" />
      {/* placeholder สำหรับรูป/วิดีโอรถ */}
    </div>
  );
}
