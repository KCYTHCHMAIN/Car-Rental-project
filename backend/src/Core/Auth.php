<?php
# path: backend/src/Core/Auth.php

namespace App\Core;  
# กำหนด namespace ของคลาสนี้อยู่ใน App\Core

use App\Core\Database;  
# ใช้งาน class Database (สำหรับ query DB)
use Firebase\JWT\JWT;  
# ใช้ class JWT สำหรับ encode token
use Firebase\JWT\Key;  
# ใช้ class Key สำหรับ decode token
use PDO;  
# ใช้งาน PDO สำหรับ query DB

final class Auth {  
    # คลาส Auth สำหรับจัดการเรื่อง Authentication & Authorization

    public static function hash(string $pw): string {
        # สร้าง password hash (เข้ารหัสรหัสผ่านก่อนเก็บ DB)
        return password_hash($pw, PASSWORD_DEFAULT);
        # ใช้ algorithm เริ่มต้นของ PHP (ตอนนี้คือ bcrypt)
    }

    public static function verify(string $pw, string $hash): bool {
        # ตรวจสอบว่ารหัสผ่านตรงกับ hash ที่เก็บไว้หรือไม่
        return password_verify($pw, $hash);
    }

    public static function token(array $user): string {
        # สร้าง JWT token สำหรับ user ที่ล็อกอินสำเร็จ
        $now = time();  
        # เวลาปัจจุบัน (timestamp)

        $ttl = (int)(getenv('JWT_TTL_SECONDS') ?: 86400);  
        # อายุของ token (ดึงจาก env, default = 24 ชั่วโมง)

        $payload = [
            'sub'   => $user['id'],      # subject (id ของผู้ใช้)
            'email' => $user['email'],   # อีเมลผู้ใช้
            'role'  => $user['role'],    # บทบาท (role) ของผู้ใช้
            'iat'   => $now,             # issued at (เวลาออก token)
            'exp'   => $now + $ttl       # expired at (หมดอายุ)
        ];

        return JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
        # สร้าง JWT token ด้วย HS256 และ secret key
    }

    public static function user(): ?array {
        # คืนค่าข้อมูล user ปัจจุบันจาก JWT (หรือ null ถ้าไม่ถูกต้อง)

        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';  
        # อ่าน header Authorization (เช่น "Bearer <token>")

        if (!str_starts_with($hdr,'Bearer ')) return null;  
        # ถ้าไม่ใช่ Bearer token → คืนค่า null

        $jwt = substr($hdr,7);  
        # ตัดคำว่า "Bearer " ออก เหลือเฉพาะ token

        try {
            $dec = JWT::decode($jwt, new Key(getenv('JWT_SECRET'),'HS256'));
            # decode JWT ด้วย secret key และ HS256

            $stmt = Database::pdo()->prepare(
                'SELECT id,email,name,role FROM customers WHERE id = ?'
            );  
            # เตรียม SQL ดึงข้อมูล user จาก DB (ตาราง customers)

            $stmt->execute([$dec->sub]);  
            # execute โดยใช้ user id จาก token (sub)

            $u = $stmt->fetch();  
            # ดึงข้อมูล user (array) จาก DB

            return $u ?: null;  
            # ถ้ามี user → ส่งกลับ, ถ้าไม่มี → null
        } catch (\Throwable $e) { 
            return null;  
            # ถ้า decode token พัง หรือ query error → ส่ง null
        }
    }

    public static function requireRole(array $roles): array {
        # ใช้ตรวจสอบว่า user ที่ล็อกอิน มี role ที่อนุญาตหรือไม่
        $u = self::user();

        if (!$u || !in_array($u['role'],$roles,true)) {
            # ถ้าไม่มี user หรือ role ไม่อยู่ใน list ที่อนุญาต
            Response::json(['error'=>'Unauthorized'],401);
            # ส่ง error 401 Unauthorized กลับทันที
        }

        return $u;  
        # ถ้า role ถูกต้อง → ส่งข้อมูล user กลับ
    }
}
