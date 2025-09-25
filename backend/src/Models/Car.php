<?php
# path: backend/src/Models/Car.php

namespace App\Models;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Models

use App\Core\Database;  
# ใช้คลาส Database สำหรับเชื่อมต่อ PDO
use PDO;  
# ใช้คลาส PDO โดยตรง (เช่น binding ค่า parameter)

final class Car {  
    # คลาส Car แบบ final (ห้ามสืบทอด) ใช้เป็น Model จัดการข้อมูลรถ

    public static function list(array $filters, int $page, int $size): array {
        # คืนค่ารายการรถทั้งหมดแบบมี filter + pagination
        # $filters = เงื่อนไขค้นหา (type, brand, price_min, price_max ฯลฯ)
        # $page = หน้าที่ต้องการ
        # $size = จำนวนข้อมูลต่อหน้า

        $pdo = Database::pdo();  
        # ดึง instance PDO มาใช้งาน

        $where = []; $params=[];  
        # เตรียม array สำหรับเก็บเงื่อนไข WHERE และค่าของ parameter

        foreach (['type','brand','model','gear','seats','color','smoking','with_driver'] as $f) {
            if (isset($filters[$f])) { 
                $where[] = "$f = ?";          # เพิ่มเงื่อนไข เช่น brand = ?
                $params[] = $filters[$f];     # เก็บค่าของ filter ลง params
            }
        }

        if (isset($filters['price_min'])) { 
            $where[] = "price_per_day >= ?"; 
            $params[] = (int)$filters['price_min']; 
        }
        # ถ้ามี price_min → เพิ่มเงื่อนไข "ราคาต่อวัน >= ค่าที่กำหนด"

        if (isset($filters['price_max'])) { 
            $where[] = "price_per_day <= ?"; 
            $params[] = (int)$filters['price_max']; 
        }
        # ถ้ามี price_max → เพิ่มเงื่อนไข "ราคาต่อวัน <= ค่าที่กำหนด"

        $sqlWhere = $where ? ('WHERE '.implode(' AND ', $where)) : '';
        # ถ้ามีเงื่อนไข → รวมเป็น WHERE ... AND ...
        # ถ้าไม่มี → $sqlWhere = ''

        $sql = "SELECT * FROM cars $sqlWhere ORDER BY id DESC LIMIT ? OFFSET ?";
        # สร้าง SQL ดึงข้อมูลรถทั้งหมด ตามเงื่อนไข + จัดเรียง id จากใหม่ไปเก่า
        # ใช้ LIMIT และ OFFSET สำหรับแบ่งหน้า (pagination)

        $stmt = $pdo->prepare($sql);
        # เตรียม statement

        foreach ($params as $i => $v) $stmt->bindValue($i+1, $v);
        # bind ค่า filter ตามลำดับที่เจอใน WHERE

        $stmt->bindValue(count($params)+1, $size, PDO::PARAM_INT);
        # bind ค่า LIMIT (จำนวนข้อมูลต่อหน้า)

        $stmt->bindValue(count($params)+2, ($page-1)*$size, PDO::PARAM_INT);
        # bind ค่า OFFSET (จำนวนข้อมูลที่ต้องข้ามก่อนหน้า)

        $stmt->execute();
        # รัน SQL

        $items = $stmt->fetchAll();
        # ดึงข้อมูลรถ (array ของ row ทั้งหมด)

        $count = (int)$pdo->query("SELECT COUNT(*) FROM cars $sqlWhere")->fetchColumn();
        # นับจำนวนรถทั้งหมด (ใช้ query แยก) → สำหรับ pagination

        return ['items'=>$items,'total'=>$count];
        # คืนค่ารายการรถ + จำนวนรวมทั้งหมด
    }

    public static function find(int $id): ?array {
        # ค้นหารถจาก id (คืนค่ารถ 1 คัน หรือ null)

        $stmt = Database::pdo()->prepare('SELECT * FROM cars WHERE id=?');
        # เตรียม statement

        $stmt->execute([$id]);
        # bind ค่า id แล้ว execute

        return $stmt->fetch() ?: null;
        # ถ้ามีผลลัพธ์ → คืนค่า array ของรถ
        # ถ้าไม่เจอ → คืน null
    }
}
