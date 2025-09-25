<?php
# path: backend/src/Models/Customer.php

namespace App\Models;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Models

use App\Core\Database;  
# ใช้คลาส Database สำหรับเชื่อมต่อ PDO

final class Customer {  
    # คลาส Customer แบบ final (ห้ามสืบทอด)
    # ใช้เป็น Model สำหรับจัดการข้อมูลลูกค้า

    public static function findByEmail(string $email): ?array {
        # ค้นหาลูกค้าจากอีเมล
        # $email = อีเมลของลูกค้าที่ต้องการค้นหา
        # return = array (ข้อมูลลูกค้า) หรือ null ถ้าไม่พบ

        $st = Database::pdo()->prepare('SELECT * FROM customers WHERE email=?');
        # เตรียม statement สำหรับค้นหา customer โดยใช้อีเมลเป็นเงื่อนไข

        $st->execute([$email]);
        # รัน query พร้อม bind ค่าอีเมล

        return $st->fetch() ?: null;
        # ถ้าพบ → ส่งข้อมูลลูกค้า (row เดียว)
        # ถ้าไม่พบ → คืนค่า null
    }
}
