<?php
# path: backend/src/Controllers/BookingsController.php

namespace App\Controllers;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Controllers

use App\Core\Auth;  
# ใช้ Auth class สำหรับตรวจสอบสิทธิ์ / JWT
use App\Core\Database;  
# ใช้ Database class เชื่อมต่อ PDO
use App\Core\Response;  
# ใช้ Response class สำหรับส่ง JSON
use App\Core\Validator;  
# ใช้ Validator class สำหรับตรวจสอบ input
use App\Models\Coupon;  
# ใช้ Coupon model สำหรับตรวจสอบคูปอง
use App\Services\BookingService;  
# ใช้ BookingService สำหรับสร้าง booking (มี transaction + overlap check)
use App\Services\UploadService;  
# ใช้ UploadService สำหรับอัปโหลดไฟล์เอกสาร

final class BookingsController {  
    # คลาส BookingsController แบบ final (ห้ามสืบทอด)
    # รวม API เกี่ยวกับการจองรถ (create, list, status, uploadDocs)

    public static function create(array $_, array $in): void {
        # API: สร้าง booking ใหม่
        $user = Auth::requireRole(['user','owner','staff']);
        # ต้องเป็น user ที่ล็อกอิน (role user/owner/staff เท่านั้น)

        $err = Validator::require($in, ['car_id','start_at','end_at','pickup_location','dropoff_location','subtotal']);
        if ($err) Response::json(['error'=>'Invalid'],422);
        # ตรวจสอบ input ว่าข้อมูลบังคับครบหรือไม่

        $discount = 0;
        if (!empty($in['coupon_code'])) {
            $c = Coupon::findActive($in['coupon_code'], (int)$in['subtotal']);
            # ถ้ามีคูปอง → ตรวจสอบว่ายัง active อยู่หรือไม่

            if ($c) 
                $discount = (int)($c['type']==='percent' 
                    ? ($in['subtotal']*$c['value']/100) 
                    : $c['value']);
            # ถ้า valid → คำนวณส่วนลด (percent หรือ fix amount)
        }

        $total = max(0, (int)$in['subtotal'] - $discount);
        # คำนวณราคารวมหลังหักส่วนลด (ห้ามติดลบ)

        $id = BookingService::create([
            'car_id'          => (int)$in['car_id'],
            'customer_id'     => $user['id'],
            'start_at'        => $in['start_at'],
            'end_at'          => $in['end_at'],
            'pickup_location' => $in['pickup_location'],
            'dropoff_location'=> $in['dropoff_location'],
            'options'         => $in['options'] ?? [],
            'coupon_code'     => $in['coupon_code'] ?? null,
            'subtotal'        => (int)$in['subtotal'],
            'discount'        => $discount,
            'total'           => $total,
        ]);
        # เรียก BookingService::create() เพื่อสร้าง booking ใหม่ (พร้อมตรวจ overlap)

        Response::json(['booking_id'=>$id['id'],'total'=>$total],201);
        # ตอบกลับ booking id + ราคารวม
    }

    public static function list(array $q): void {
        # API: แสดงรายการ booking ของผู้ใช้
        $user = Auth::requireRole(['user','owner','staff']);
        # ต้องเป็น user ที่ล็อกอิน

        $pdo = Database::pdo();

        if (in_array($user['role'], ['owner','staff'], true)) {
            $st = $pdo->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 200");
            # owner/staff → เห็น booking ทั้งหมด (limit 200 ล่าสุด)
        } else {
            $st = $pdo->prepare("SELECT * FROM bookings WHERE customer_id=? ORDER BY id DESC");
            $st->execute([$user['id']]);
            # user ปกติ → เห็นเฉพาะ booking ของตัวเอง
        }

        Response::json(['items'=>$st->fetchAll()]);
        # ส่งรายการ booking กลับ
    }

    public static function status(array $_, array $in): void {
        # API: อัปเดตสถานะ booking
        $admin = Auth::requireRole(['owner','staff']);
        # เฉพาะ owner/staff เท่านั้น

        $id = (int)($in['booking_id'] ?? 0);
        $to = $in['status'] ?? '';
        $allowed = ['pending','confirmed','cancelled','checked_in','checked_out','refunded'];
        # สถานะที่อนุญาตให้เปลี่ยน

        if (!$id || !in_array($to,$allowed,true)) 
            Response::json(['error'=>'Invalid'],422);
        # ถ้า id ไม่ถูกต้อง หรือ status ไม่ถูกต้อง → error 422

        $st = Database::pdo()->prepare("UPDATE bookings SET status=? WHERE id=?");
        $st->execute([$to,$id]);
        # อัปเดตสถานะ booking ใน DB

        Response::json(['ok'=>true]);
        # ส่ง response ว่าสำเร็จ
    }

    public static function uploadDocs(): void {
        # API: อัปโหลดเอกสาร (เช่น บัตร ปชช, ใบขับขี่)
        $user = Auth::requireRole(['user','owner','staff']);
        # ต้องเป็น user ที่ล็อกอิน

        if (!isset($_FILES['file'])) 
            Response::json(['error'=>'No file'],400);
        # ถ้าไม่มีไฟล์อัปโหลดมา → error 400

        $url = UploadService::save($_FILES['file']);
        # บันทึกไฟล์ด้วย UploadService (ตรวจ type/size)

        Response::json(['url'=>$url],201);
        # ส่ง URL ของไฟล์กลับ (201 Created)
    }
}
