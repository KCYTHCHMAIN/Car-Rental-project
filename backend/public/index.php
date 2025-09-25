<?php
# path: backend/public/index.php
declare(strict_types=1);  
# เปิด strict typing mode (บังคับการใช้ type hint ให้ตรง)

require __DIR__ . '/../src/bootstrap.php';  
# โหลดไฟล์ bootstrap.php (ตั้งค่า autoload, env, DB connection, etc.)

use App\Core\Router;  
# import class Router (จัดการเส้นทาง API)
use App\Core\Response;  
# import class Response (จัดการ response JSON)

header('Content-Type: application/json; charset=utf-8');  
# กำหนดให้ทุก response เป็น JSON UTF-8

$router = new Router($_SERVER, $_GET);  
# สร้าง instance ของ Router โดยส่งค่า server request และ query string เข้าไป

require __DIR__ . '/../src/routes.php';  
# โหลดไฟล์ routes.php (ประกาศเส้นทาง API เช่น GET /cars, POST /booking)

try {
    $router->dispatch();  
    # สั่ง router ให้ทำงาน → จับคู่ route และเรียก controller/action ที่ตรงกับ request
} catch (Throwable $e) {
    error_log($e);  
    # ถ้ามี error เกิดขึ้น log ข้อผิดพลาดลง error log ของ PHP/Apache

    Response::json(['error' => 'Server error'], 500);  
    # ส่ง response JSON กลับไปพร้อมสถานะ 500 (Internal Server Error)
}
