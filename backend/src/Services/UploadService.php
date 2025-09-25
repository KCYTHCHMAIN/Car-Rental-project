<?php
# path: backend/src/Services/UploadService.php

namespace App\Services;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Services

use App\Core\Response;  
# ใช้ Response class สำหรับส่ง JSON error response

final class UploadService {  
    # คลาส UploadService แบบ final (ห้ามสืบทอด)
    # ใช้จัดการการอัปโหลดไฟล์

    public static function save(array $file): string {
        # ฟังก์ชันหลัก: บันทึกไฟล์ที่อัปโหลด
        # $file = array ข้อมูลไฟล์จาก $_FILES['...']
        # return = path ของไฟล์ที่เก็บแล้ว (string)

        $max = ((int)getenv('UPLOAD_MAX_MB') ?: 5) * 1024 * 1024;
        # ขนาดไฟล์สูงสุดที่อนุญาต (ดึงจาก env UPLOAD_MAX_MB, default = 5 MB)

        if ($file['error'] !== UPLOAD_ERR_OK) 
            Response::json(['error'=>'Upload failed'],400);
        # ถ้ามี error ระหว่างอัปโหลด → ส่ง error 400

        if ($file['size'] > $max) 
            Response::json(['error'=>'File too large'],413);
        # ถ้าไฟล์ใหญ่เกิน limit → ส่ง error 413 (Payload Too Large)

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        # ตรวจสอบ MIME type ของไฟล์ (เพื่อป้องกันไฟล์แฝงอันตราย)

        $allowed = ['image/jpeg','image/png','application/pdf'];
        if (!in_array($mime, $allowed, true)) 
            Response::json(['error'=>'Invalid type'],415);
        # อนุญาตเฉพาะ JPG, PNG, PDF
        # ถ้าไม่ตรง → ส่ง error 415 (Unsupported Media Type)

        $dir = getenv('UPLOAD_DIR') ?: __DIR__ . '/../../storage/uploads';
        # โฟลเดอร์ที่จะเก็บไฟล์ (จาก env UPLOAD_DIR หรือ storage/uploads)

        if (!is_dir($dir)) mkdir($dir, 0775, true);
        # ถ้าโฟลเดอร์ยังไม่มี → สร้างใหม่ (สิทธิ์ 0775)

        $name = bin2hex(random_bytes(8)) . '-' . preg_replace('/[^a-z0-9\.\-]/i','_',$file['name']);
        # สร้างชื่อไฟล์ใหม่: random prefix + ชื่อไฟล์เดิม (sanitize เฉพาะ a-z,0-9,.-)

        $path = rtrim($dir,'/') . '/' . $name;
        # path แบบเต็มของไฟล์ที่จะบันทึก

        move_uploaded_file($file['tmp_name'], $path);
        # ย้ายไฟล์จาก temp ไปยัง path ที่กำหนด

        return '/uploads/' . $name;
        # คืน path (relative) สำหรับ frontend เรียกใช้งานไฟล์
    }
}
