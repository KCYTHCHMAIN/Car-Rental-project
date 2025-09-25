<?php
# path: backend/src/Core/Router.php

namespace App\Core;  
# กำหนด namespace ของคลาสนี้เป็น App\Core

final class Router {  
    # คลาส Router แบบ final (ห้ามสืบทอด) สำหรับจัดการเส้นทาง API

    private array $routes = [];  
    # เก็บรายการเส้นทางทั้งหมด (method, path, handler)

    private array $server;  
    # เก็บข้อมูล $_SERVER (เช่น REQUEST_URI, REQUEST_METHOD)

    private array $query;  
    # เก็บข้อมูล query string ($_GET)

    public function __construct(array $server, array $query) {
        $this->server = $server;   # กำหนดค่า $_SERVER
        $this->query = $query;     # กำหนดค่า $_GET
    }

    public function add(string $method, string $path, callable $handler): void {
        # เพิ่มเส้นทางใหม่
        # $method = HTTP method (GET, POST, PUT, DELETE)
        # $path = pattern ของ URI
        # $handler = ฟังก์ชันที่ต้องเรียกเมื่อ match

        $this->routes[] = [$method, "#^{$path}$#", $handler];
        # เก็บ route ลง array โดยแปลง path ให้เป็น regex
    }

    public function dispatch(): void {
        # ฟังก์ชันหลัก → หาว่า request ปัจจุบันตรงกับ route ไหน แล้วเรียก handler

        $uri = parse_url($this->server['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        # ตัด query string ออก เอาเฉพาะ path (เช่น /api/cars)

        $method = $this->server['REQUEST_METHOD'] ?? 'GET';
        # method ที่ใช้ (ค่า default = GET)

        foreach ($this->routes as [$m,$pattern,$handler]) {
            # loop เช็ค route ที่บันทึกไว้ทั้งหมด

            if ($m === $method && preg_match($pattern, $uri, $mch)) {
                # ถ้า HTTP method ตรงกัน และ URI match กับ pattern ที่ประกาศ

                $payload = json_decode(file_get_contents('php://input') ?: '[]', true) ?: [];
                # ดึงข้อมูล body (JSON) จาก request
                # ถ้าไม่มี → ใช้ array ว่าง []

                $handler($this->query, $payload);
                # เรียกฟังก์ชัน handler โดยส่ง query string ($_GET) และ payload ($_POST/JSON body)
                return;
            }
        }

        Response::json(['error'=>'Not Found'],404);
        # ถ้าไม่เจอ route → ตอบกลับ JSON {"error":"Not Found"} และ status 404
    }
}
