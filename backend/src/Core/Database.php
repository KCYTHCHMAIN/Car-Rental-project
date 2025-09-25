<?php
# path: backend/src/Core/Database.php

namespace App\Core;  
# กำหนด namespace ของคลาสนี้เป็น App\Core

use PDO;  
# import class PDO จาก PHP (ใช้สำหรับเชื่อมต่อฐานข้อมูล)

final class Database {  
    # คลาส Database แบบ final (ห้าม extends) สำหรับจัดการการเชื่อมต่อ PDO

    private static ?PDO $pdo = null;  
    # ตัวแปร static เก็บ instance ของ PDO (ค่าเริ่มต้น = null)
    # ใช้ pattern แบบ Singleton → เชื่อมต่อ DB ครั้งเดียวแล้ว reuse

    public static function pdo(): PDO {  
        # เมธอด static สำหรับคืนค่า instance ของ PDO

        if (self::$pdo) return self::$pdo;  
        # ถ้ามี PDO อยู่แล้ว → ส่งกลับทันที (ไม่ต้องสร้างใหม่)

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST'),                  # host DB (จาก .env)
            getenv('DB_PORT') ?: '3306',        # port DB (default = 3306)
            getenv('DB_NAME')                   # ชื่อฐานข้อมูล
        );  
        # สร้าง Data Source Name (DSN) สำหรับ MySQL

        $pdo = new PDO(
            $dsn,
            getenv('DB_USER'),                  # user ของ DB
            getenv('DB_PASS'),                  # password ของ DB
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,     # โยน Exception ถ้า query error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC # ให้ fetch() คืนค่าเป็น associative array
            ]
        );  
        # สร้าง instance PDO พร้อม options

        self::$pdo = $pdo;  
        # เก็บ instance ไว้ใน static property

        return $pdo;  
        # ส่ง PDO object กลับไปให้ใช้งาน
    }
}
