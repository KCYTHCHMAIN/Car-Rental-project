<?php
# path: backend/src/Controllers/CarsController.php

namespace App\Controllers;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Controllers

use App\Core\Auth;  
# ใช้ Auth class เพื่อตรวจสอบสิทธิ์ role
use App\Core\Database;  
# ใช้ Database class สำหรับเชื่อมต่อ PDO
use App\Core\Response;  
# ใช้ Response class สำหรับส่ง JSON
use App\Models\Car;  
# ใช้ Car model สำหรับ query ข้อมูลรถ

final class CarsController {  
    # คลาส CarsController แบบ final (ห้ามสืบทอด)
    # จัดการ API เกี่ยวกับรถ (list, show, create, availability)

    public static function index(array $q): void {
        # API: แสดงรายการรถ (มี filter + pagination)
        $page = max(1, (int)($q['page'] ?? 1));
        # หน้าปัจจุบัน (default = 1, ต้องไม่ต่ำกว่า 1)

        $size = min(50, max(1, (int)($q['size'] ?? 12)));
        # จำนวนข้อมูลต่อหน้า (default = 12, สูงสุด 50)

        $filters = array_intersect_key(
            $q, 
            array_flip(['type','brand','model','gear','seats','color','smoking','with_driver','price_min','price_max'])
        );
        # เลือกเฉพาะ query string ที่เป็น filter ที่อนุญาต

        $data = Car::list($filters, $page, $size);
        # เรียก Car::list() คืนค่ารถตาม filter + pagination

        Response::json($data);
        # ส่ง JSON response กลับ
    }

    public static function show(array $q): void {
        # API: แสดงรายละเอียดรถคันเดียว
        $id = (int)($q['id'] ?? 0);
        # รับค่า id จาก query string

        $car = Car::find($id);
        # ค้นหารถจาก id

        if (!$car) Response::json(['error'=>'Not found'],404);
        # ถ้าไม่พบรถ → ส่ง error 404

        Response::json($car);
        # ส่งข้อมูลรถกลับ
    }

    public static function create(array $_, array $in): void {
        # API: เพิ่มรถใหม่ (ต้องมีสิทธิ์ owner หรือ staff)

        $admin = Auth::requireRole(['owner','staff']);
        # ตรวจสอบสิทธิ์ role → ถ้าไม่ใช่ owner/staff จะโดน 401

        $pdo = Database::pdo();
        # ดึง PDO instance

        $st = $pdo->prepare("INSERT INTO cars 
            (type,brand,model,gear,seats,color,smoking,with_driver,price_per_day,images_json,created_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,NOW())");
        # เตรียม SQL insert รถใหม่

        $st->execute([
            $in['type'],$in['brand'],$in['model'],$in['gear'],(int)$in['seats'],$in['color'],
            (int)$in['smoking'],(int)$in['with_driver'],(int)$in['price_per_day'], json_encode($in['images'] ?? [])
        ]);
        # execute SQL พร้อม binding ค่า input
        # images เก็บเป็น JSON array

        Response::json(['id'=>$pdo->lastInsertId()],201);
        # ส่งกลับ id ของรถที่สร้างใหม่ (201 Created)
    }

    public static function availability(array $q): void {
        # API: ตรวจสอบว่ารถคันนี้ว่างหรือไม่ในช่วงวันที่กำหนด

        $id = (int)($q['id'] ?? 0);
        $from = $q['from'] ?? '';
        $to = $q['to'] ?? '';
        # รับค่ารถและช่วงวันจาก query string

        if (!$id || !$from || !$to) Response::json(['error'=>'Invalid'],422);
        # ถ้าข้อมูลไม่ครบ → error 422

        $st = Database::pdo()->prepare("SELECT start_at,end_at FROM bookings 
            WHERE car_id=? 
              AND status IN ('pending','confirmed') 
              AND NOT (end_at <= ? OR start_at >= ?)");
        # SQL: หาช่วงเวลาที่มี booking ของรถคันนี้
        # เฉพาะสถานะ pending/confirmed
        # เอาที่ทับกับช่วงวันที่ต้องการตรวจสอบ

        $st->execute([$id,$from,$to]);
        # execute พร้อม binding id, from, to

        Response::json(['busy'=>$st->fetchAll()]);
        # คืนค่ารายการ booking ที่ทับช่วง (busy slots)
    }
}
