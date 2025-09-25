<?php
# path: backend/src/Services/EmailService.php

namespace App\Services;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Services

final class EmailService {  
    # คลาส EmailService แบบ final (ห้ามสืบทอด)
    # ใช้สำหรับส่งอีเมล (abstract ไว้เพื่อเปลี่ยน provider ได้ง่าย)

    public static function send(string $to, string $subject, string $body): void {
        # ฟังก์ชันส่งอีเมล
        # $to = ผู้รับ
        # $subject = หัวข้ออีเมล
        # $body = เนื้อหาอีเมล

        // Dev: Mailhog via SMTP. Production: swap to real provider.
        # คอมเมนต์: 
        # - ตอน Dev ใช้ Mailhog (ดักอีเมลไม่ส่งจริง)
        # - ตอน Production ต้องเปลี่ยนไปใช้ SMTP provider จริง (เช่น Gmail, SendGrid, SES)

        $headers = "From: " . (getenv('SMTP_FROM') ?: 'noreply@example.com');
        # ตั้งค่า header ของอีเมล (From:)
        # ถ้ามีการตั้งค่า SMTP_FROM ใน .env → ใช้ค่านั้น
        # ถ้าไม่มี → ใช้ noreply@example.com

        @mail($to, $subject, $body, $headers); 
        # ใช้ฟังก์ชัน mail() ของ PHP เพื่อส่งอีเมล
        # สัญลักษณ์ @ = กด suppress error (ไม่แสดง warning ถ้าส่งไม่สำเร็จ)
        # หมายเหตุ: 
        # ใน container mail() จะไปเรียก ssmtp/msmtp (ต้อง config ไว้)
        # ถ้าใช้ Mailhog ใน dev → จะดักจับอีเมลทั้งหมดไปดูใน web UI
    }
}
    