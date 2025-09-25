// path: frontend/src/hooks/useAuth.jsx

import { createContext, useContext, useEffect, useState } from "react";
// import React hooks และ context API
import api from "../lib/api";
// import api instance (ที่ตั้งค่า base URL + token ไว้แล้ว)

const Ctx = createContext(null);
// สร้าง context สำหรับเก็บข้อมูล auth (user, login, logout, register)

export function AuthProvider({ children }) {
  // Component ที่ห่อแอป (Provider) เพื่อแจก state auth ให้ทุก component ใช้ได้
  const [user, setUser] = useState(null);
  // state เก็บข้อมูล user ที่ login อยู่ (null = ยังไม่ login)

  useEffect(() => {
    // รันครั้งเดียวตอน mount → ตรวจสอบว่ามี token เก็บอยู่หรือไม่
    const token = localStorage.getItem("token");
    if (!token) return; // ถ้าไม่มี token → ข้าม

    api.get("auth/me").json()
      .then(res => setUser(res.user))
      .catch(()=>{});
    // ถ้ามี token → เรียก API auth/me เพื่อดึงข้อมูล user
    // ถ้าสำเร็จ → setUser ด้วยข้อมูล user
    // ถ้าล้มเหลว → ไม่ทำอะไร (token อาจหมดอายุ)
  }, []);

  const login = async (email, password) => {
    // ฟังก์ชัน login → รับ email + password
    const res = await api.post("auth/login", { json: { email, password } }).json();
    // เรียก API login → ได้ token + user
    localStorage.setItem("token", res.token);
    // เก็บ token ใน localStorage
    setUser(res.user);
    // อัปเดต state user
  };

  const logout = () => {
    // ฟังก์ชัน logout → เคลียร์ token + user
    localStorage.removeItem("token");
    setUser(null);
  };

  const register = async (name, email, password) => {
    // ฟังก์ชัน register → สมัครสมาชิกใหม่
    await api.post("auth/register", { json: { name, email, password } }).json();
    // สมัครสำเร็จแล้ว → login อัตโนมัติ
    await login(email, password);
  };

  return (
    <Ctx.Provider value={{ user, login, logout, register }}>
      {children}
    </Ctx.Provider>
  );
  // ให้ component ลูก ๆ สามารถเข้าถึง user, login, logout, register ผ่าน useAuth()
}

export function useAuth() { 
  // hook ใช้ดึง context auth
  return useContext(Ctx) || { 
    user: null, 
    login: async()=>{}, 
    logout: ()=>{}, 
    register: async()=>{} 
  }; 
  // fallback → ถ้าอยู่นอก AuthProvider จะได้ object เปล่า ๆ (ป้องกัน error)
}
