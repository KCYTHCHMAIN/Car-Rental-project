<?php
# path: backend/src/Services/BookingService.php

namespace App\Services;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Services

use App\Core\Database;  
# ใช้ Database class สำหรับเชื่อมต่อ PDO
use App\Core\Response;  
# ใช้ Response class สำหรับส่ง JSON response
use PDO;  
# ใช้ PDO โดยตรงสำหรับ query และ transaction

final class BookingService {  
    # คลาส BookingService แบบ final (ห้ามสืบทอด)
    # มีหน้าที่จัดการ logic การจอง (Booking) ระดับ service layer

    /** Prevent double-booking with transaction + range overlap check */
    # คอมเมนต์ภาษาอังกฤษ (อธิบายว่า logic นี้ป้องกันการจองซ้อนด้วย transaction + ตรวจช่วงเวลา)

    public static function create(array $data): array {
        # ฟังก์ชันสร้าง booking ใหม่
        # $data = ข้อมูลการจอง (car_id, customer_id, start_at, end_at, ...)

        $pdo = Database::pdo();
        # ดึง instance PDO

        $pdo->beginTransaction();
        # เริ่มต้น transaction → เพื่อให้การจองนี้ atomic (สำเร็จหรือยกเลิกทั้งหมด)

        try {
            // Lock car row to serialize bookings for that car
            $lock = $pdo->prepare('SELECT id FROM cars WHERE id=? FOR UPDATE');
            $lock->execute([$data['car_id']]);
            # ดึง row ของรถคันที่ต้องการจองมา "ล็อค" (FOR UPDATE)
            # เพื่อป้องกันไม่ให้มี transaction อื่นจองซ้อนในเวลาเดียวกัน

            // Overlap check
            $overlap = $pdo->prepare("SELECT id FROM bookings
                WHERE car_id=? AND status IN ('pending','confirmed')
                AND NOT (end_at <= ? OR start_at >= ?)
                LIMIT 1");
            $overlap->execute([$data['car_id'], $data['start_at'], $data['end_at']]);
            # ตรวจสอบว่ามี booking อื่นในช่วงเวลาที่ทับกันหรือไม่
            # เงื่อนไข NOT (end_at <= start OR start_at >= end)
            # หมายถึง: ถ้า booking ไม่หมดก่อน หรือไม่เริ่มหลัง → มีทับช่วงเวลา

            if ($overlap->fetch()) {
                $pdo->rollBack();
                # ถ้ามี booking ทับกัน → ยกเลิก transaction

                Response::json(['error'=>'Overlap booking'], 409);
                # ส่ง error JSON กลับไป (HTTP 409 Conflict)
            }

            $ins = $pdo->prepare("INSERT INTO bookings
                (car_id, customer_id, start_at, end_at, pickup_location, dropoff_location, options_json, coupon_code, subtotal, discount, total, status, created_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
            # สร้าง query สำหรับเพิ่ม booking ใหม่
            # เก็บรายละเอียด: รถ, ลูกค้า, วันเวลา, สถานที่รับ-คืน, options, คูปอง, ยอดเงิน, สถานะ, วันที่สร้าง

            $ins->execute([
                $data['car_id'], $data['customer_id'], $data['start_at'], $data['end_at'],
                $data['pickup_location'], $data['dropoff_location'], json_encode($data['options'] ?? []),
                $data['coupon_code'] ?? null, $data['subtotal'], $data['discount'] ?? 0, $data['total'], 'pending'
            ]);
            # execute คำสั่ง insert พร้อมข้อมูลจริง
            # options แปลงเป็น JSON ก่อนเก็บ
            # coupon_code อาจเป็น null ได้
            # discount default = 0
            # สถานะเริ่มต้น = "pending"

            $id = (int)$pdo->lastInsertId();
            # ดึง id ของ booking ที่เพิ่งสร้างขึ้นมา

            $pdo->commit();
            # commit transaction → ยืนยันการบันทึก

            return ['id'=>$id];
            # คืนค่า booking id กลับไป
        } catch (\Throwable $e) {
            $pdo->rollBack();
            # ถ้ามี error เกิดขึ้น → rollback ยกเลิก transaction ทั้งหมด

            throw $e;
            # โยน error ต่อไปให้ชั้นบนจัดการ
        }
    }
}
