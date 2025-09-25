<?php
# path: backend/src/bootstrap.php
declare(strict_types=1);  
# เปิด strict typing (บังคับการใช้ type hint และ type checking)

spl_autoload_register(function($class){
    # ลงทะเบียน autoloader (จะถูกเรียกอัตโนมัติเมื่อมีการเรียกใช้ class)

    $prefix = 'App\\';  
    # กำหนด namespace หลักของโปรเจกต์เป็น "App\"

    $base = __DIR__ . '/';  
    # base directory = โฟลเดอร์ src (ตำแหน่งปัจจุบันของไฟล์นี้)

    if (str_starts_with($class, $prefix)) {
        # ถ้า class ที่เรียกขึ้นต้นด้วย "App\"

        $rel = substr($class, strlen($prefix));  
        # ตัด prefix "App\" ออก → ได้ path ย่อยของ class

        $file = $base . str_replace('\\','/',$rel) . '.php';  
        # แปลง namespace "\" เป็น path "/" แล้วต่อเป็นไฟล์ PHP  
        # เช่น App\Core\Router → src/Core/Router.php

        if (file_exists($file)) require $file;  
        # ถ้าไฟล์มีอยู่จริง ให้ require เข้ามา
    }
});

date_default_timezone_set(getenv('TZ') ?: 'Asia/Bangkok');  
# ตั้งค่า timezone ของระบบตาม environment variable TZ  
# ถ้าไม่มีค่า → ใช้ค่าเริ่มต้น Asia/Bangkok
