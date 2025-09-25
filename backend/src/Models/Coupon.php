<?php
# path: backend/src/Models/Coupon.php

namespace App\Models;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Models

use App\Core\Database;  
# ใช้คลาส Database สำหรับเชื่อมต่อ PDO

final class Coupon {  
    # คลาส Coupon แบบ final (ห้ามสืบทอด)
    # ใช้เป็น Model สำหรับจัดการข้อมูลคูปองส่วนลด

    public static function findActive(string $code, int $subtotal): ?array {
        # ค้นหาคูปองที่ยังใช้งานได้ (active) จาก code ที่กำหนด
        # $code = รหัสคูปองที่ผู้ใช้กรอก
        # $subtotal = ยอดรวมก่อนใช้คูปอง (ใช้เช็ค min_amount)

        $sql = "SELECT * FROM coupons 
                WHERE code=? 
                  AND starts_at <= NOW()   # วันที่เริ่ม <= วันนี้
                  AND ends_at >= NOW()     # วันที่สิ้นสุด >= วันนี้
                  AND active=1             # ต้องเปิดใช้งาน (active = 1)
                  AND min_amount <= ?";    # ต้องมียอดขั้นต่ำตรงตามเงื่อนไข

        $st = Database::pdo()->prepare($sql);
        # เตรียม statement สำหรับ query คูปอง

        $st->execute([$code, $subtotal]);
        # bind ค่า code และ subtotal แล้ว execute

        return $st->fetch() ?: null;
        # ถ้าพบคูปอง → ส่งข้อมูล row กลับ (array)
        # ถ้าไม่พบ → คืนค่า null
    }
}
