<?php
# path: backend/src/Models/Booking.php

namespace App\Models;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Models

use App\Core\Database;  
# ใช้คลาส Database สำหรับเชื่อมต่อ PDO

final class Booking {  
    # คลาส Booking แบบ final (ห้ามสืบทอด)
    # ใช้เป็น Model จัดการข้อมูลการจองรถ

    public static function byCarBetween(int $carId, string $from, string $to): array {
        # ดึงข้อมูลการจองของรถคันที่กำหนด ($carId)
        # ในช่วงเวลาที่ระบุ ($from → $to)
        # ใช้ตรวจสอบว่ารถคันนั้น "ติดจอง" หรือ "ว่าง"

        $sql = "SELECT * FROM bookings 
                WHERE car_id=? 
                  AND status IN ('pending','confirmed')
                  AND NOT (end_at <= ? OR start_at >= ?)";
        # เงื่อนไข:
        # car_id = รถที่กำหนด
        # status = อยู่ในสถานะ pending หรือ confirmed เท่านั้น
        # AND NOT (end_at <= ? OR start_at >= ?)
        # = ตัดจองที่ "ไม่ทับช่วงเวลา" ออก
        # สรุป = เหลือเฉพาะจองที่ "ทับช่วงเวลา" จริง ๆ

        $stmt = Database::pdo()->prepare($sql);
        # เตรียม statement

        $stmt->execute([$carId, $from, $to]);
        # bind ค่า carId, from, to แล้ว execute

        return $stmt->fetchAll();
        # คืนค่าทุก booking ที่เจอ (array ของ row)
    }
}
