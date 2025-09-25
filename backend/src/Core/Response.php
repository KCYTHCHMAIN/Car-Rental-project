<?php
# path: backend/src/Core/Response.php

namespace App\Core;  
# กำหนด namespace ของคลาสนี้อยู่ใน App\Core

final class Response {  
    # ประกาศคลาส Response แบบ final (ห้ามสืบทอด/extends)

    public static function json(array $data, int $code = 200): void {
        # เมธอดแบบ static ใช้สำหรับส่ง response JSON
        # $data = ข้อมูลที่จะส่งกลับ (array)
        # $code = HTTP status code (ค่าเริ่มต้น = 200 OK)

        http_response_code($code);  
        # ตั้งค่า HTTP status code (เช่น 200, 400, 500)

        echo json_encode($data, JSON_UNESCAPED_UNICODE);  
        # แปลง array เป็น JSON string
        # ใช้ JSON_UNESCAPED_UNICODE เพื่อไม่ให้ escape ภาษาไทย/Unicode

        exit;  
        # จบการทำงานทันทีหลังส่ง response
    }
}
