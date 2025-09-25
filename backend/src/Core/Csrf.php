<?php
# path: backend/src/Core/Csrf.php

namespace App\Core;  
# กำหนด namespace ของคลาสนี้อยู่ใน App\Core

final class Csrf {  
    # คลาส Csrf แบบ final (ห้ามสืบทอด)
    # ใช้จัดการ CSRF Token (Cross-Site Request Forgery Protection)

    public static function token(): string {
        # สร้างหรือคืนค่า CSRF token

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        # ถ้า session ยังไม่เริ่ม → สั่ง session_start()

        $_SESSION['csrf'] = $_SESSION['csrf'] ?? bin2hex(random_bytes(16));
        # ถ้ามี token ใน session อยู่แล้ว → ใช้ของเดิม
        # ถ้ายังไม่มี → สร้างใหม่แบบสุ่ม 16 bytes แล้วแปลงเป็น hex

        return $_SESSION['csrf'];
        # คืนค่า token กลับไป
    }

    public static function verify(?string $token): void {
        # ตรวจสอบความถูกต้องของ CSRF token
        # $token = ค่า token ที่ส่งมาจาก client (เช่น form hidden input)

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') return;
        # ถ้าเป็น GET request → ข้าม (ไม่ตรวจ CSRF)

        if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) return;
        # ถ้าเป็น JSON request → ข้าม (ปกติ CSRF ใช้กับ form POST เท่านั้น)

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        # ถ้า session ยังไม่เริ่ม → สั่ง session_start()

        if (
            !$token ||                             # ถ้า token ที่ส่งมาไม่มี
            !isset($_SESSION['csrf']) ||           # หรือ session ไม่มี csrf token
            !hash_equals($_SESSION['csrf'],$token) # หรือ token ไม่ตรงกัน (ป้องกัน timing attack)
        ) {
            Response::json(['error'=>'Bad CSRF'], 419);
            # ส่ง response error 419 (CSRF token invalid) แล้วหยุดทำงาน
        }
    }
}
