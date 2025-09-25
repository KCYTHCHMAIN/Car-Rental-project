<?php
# path: backend/src/Core/RateLimiter.php

namespace App\Core;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Core

final class RateLimiter {  
    # คลาส RateLimiter แบบ final (ห้าม extends)
    # ใช้สำหรับจำกัดจำนวน request (เช่น ป้องกัน brute force / spam)

    public static function allow(string $key, int $limit=60, int $window=60): bool {
        # ฟังก์ชันตรวจสอบว่าควร "อนุญาต" request หรือไม่
        # $key = คีย์อ้างอิง (เช่น login, api)
        # $limit = จำนวน request สูงสุดที่อนุญาตในช่วงเวลา
        # $window = ระยะเวลา (วินาที) ที่นับรวม

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        # ใช้ IP ของผู้ร้องขอเป็นตัวช่วยจำกัด
        # ถ้าไม่มีข้อมูล → ใช้ค่า default 0.0.0.0

        $k = "rl:{$key}:{$ip}:" . intdiv(time(), $window);
        # สร้าง key แบบเฉพาะ (ตาม key + ip + window time slot)
        # เช่น rl:login:192.168.1.1:28575711

        $file = sys_get_temp_dir() . "/$k";
        # เก็บค่าลงไฟล์ชั่วคราวใน system temp dir

        $count = file_exists($file) ? (int)file_get_contents($file) : 0;
        # ถ้ามีไฟล์อยู่แล้ว → อ่านค่าปัจจุบัน (จำนวน request)
        # ถ้าไม่มีไฟล์ → ค่าเริ่มต้น = 0

        if ($count >= $limit) return false;
        # ถ้า request เกิน limit ที่กำหนด → ไม่อนุญาต (return false)

        file_put_contents($file, (string)($count+1));
        # ถ้ายังไม่ถึง limit → เพิ่ม count แล้วบันทึกกลับลงไฟล์

        return true;
        # อนุญาต request (return true)
    }
}
