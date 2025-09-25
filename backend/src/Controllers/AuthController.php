<?php
# path: backend/src/Controllers/AuthController.php

namespace App\Controllers;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Controllers

use App\Core\Auth;  
# ใช้ class Auth สำหรับจัดการ JWT และ password
use App\Core\Database;  
# ใช้ Database class สำหรับเชื่อมต่อ PDO
use App\Core\Response;  
# ใช้ Response class สำหรับส่ง JSON
use App\Core\Validator;  
# ใช้ Validator class สำหรับตรวจสอบ input

final class AuthController {  
    # คลาส AuthController แบบ final (ห้ามสืบทอด)
    # ทำหน้าที่จัดการ authentication (register, login, me)

    public static function register(array $_, array $in): void {
        # ฟังก์ชันลงทะเบียนผู้ใช้ใหม่
        # $_ = query string (ไม่ใช้ที่นี่)
        # $in = body input (email, password, name)

        $err = Validator::require($in, ['email','password','name']);
        # ตรวจสอบว่ามีฟิลด์ email, password, name ครบหรือไม่

        if ($err || !Validator::email($in['email'])) 
            Response::json(['error'=>'Invalid'],422);
        # ถ้าข้อมูลไม่ครบ หรือ email ไม่ถูกต้อง → ตอบ 422 (Unprocessable Entity)

        $pdo = Database::pdo();  
        # ดึง PDO instance

        $exists = $pdo->prepare('SELECT id FROM customers WHERE email=?');
        $exists->execute([$in['email']]);
        # ตรวจสอบว่ามี email นี้อยู่ในระบบแล้วหรือไม่

        if ($exists->fetch()) 
            Response::json(['error'=>'Email used'],409);
        # ถ้ามีอยู่แล้ว → ตอบ 409 (Conflict)

        $ins = $pdo->prepare(
            'INSERT INTO customers (email,password_hash,name,role,created_at) VALUES (?,?,?,?,NOW())'
        );
        $ins->execute([
            $in['email'], 
            Auth::hash($in['password']),  # เข้ารหัส password ก่อนเก็บ
            $in['name'], 
            'user'                        # role เริ่มต้น = user
        ]);
        # บันทึกข้อมูลลูกค้าใหม่ลง DB

        Response::json(['ok'=>true],201);
        # ตอบกลับว่าสำเร็จ (201 Created)
    }

    public static function login(array $_, array $in): void {
        # ฟังก์ชันล็อกอิน
        # $_ = query string (ไม่ใช้ที่นี่)
        # $in = body input (email, password)

        $pdo = Database::pdo();  
        # ดึง PDO instance

        $st = $pdo->prepare('SELECT * FROM customers WHERE email=?');
        $st->execute([$in['email'] ?? '']);
        $u = $st->fetch();
        # ค้นหาผู้ใช้จาก email

        if (!$u || !Auth::verify($in['password'] ?? '', $u['password_hash'])) 
            Response::json(['error'=>'Bad credentials'],401);
        # ถ้าไม่เจอ user หรือ password ไม่ตรง → ตอบ 401 Unauthorized

        $token = Auth::token($u);
        # สร้าง JWT token สำหรับ user

        Response::json([
            'token' => $token,
            'user'  => [
                'id'    => $u['id'],
                'email' => $u['email'],
                'name'  => $u['name'],
                'role'  => $u['role']
            ]
        ]);
        # ตอบกลับ token + ข้อมูล user
    }

    public static function me(): void {
        # ฟังก์ชันคืนค่าข้อมูล user ปัจจุบัน (จาก JWT)

        $u = \App\Core\Auth::user();
        # ดึงข้อมูล user จาก JWT

        if (!$u) Response::json(['error'=>'Unauthorized'],401);
        # ถ้าไม่มี user (token ไม่ถูกต้อง/หมดอายุ) → ตอบ 401 Unauthorized

        Response::json(['user'=>$u]);
        # ส่งข้อมูล user กลับ
    }
}
