<?php
# path: backend/src/Core/Validator.php

namespace App\Core;  
# กำหนด namespace ของคลาสนี้ให้อยู่ใน App\Core

final class Validator {  
    # คลาส Validator แบบ final (ห้าม extends)
    # ใช้รวมฟังก์ชันสำหรับตรวจสอบความถูกต้องของข้อมูล (Validation)

    public static function require(array $data, array $fields): array {
        # ฟังก์ชันตรวจสอบว่าฟิลด์ที่กำหนด ต้องมีค่า (required)
        # $data = ข้อมูลที่รับเข้ามา (เช่น body ของ request)
        # $fields = รายชื่อฟิลด์ที่ต้องตรวจสอบ
        # return = array ของ error (ถ้าไม่มี error → return [] ว่าง)

        $errors = [];  
        # สร้างตัวแปรเก็บ error

        foreach ($fields as $f)  
            if (!isset($data[$f]) || $data[$f]==='')  
                $errors[$f] = 'required';  
        # ถ้า key ไม่ถูกส่งมา หรือเป็นค่าว่าง → บันทึกว่า "required"

        return $errors;  
        # ส่ง error กลับ
    }

    public static function email(string $email): bool {
        # ตรวจสอบว่า email ถูกต้องตามรูปแบบหรือไม่
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
        # ใช้ filter_var กับ FILTER_VALIDATE_EMAIL → คืนค่า true/false
    }

    public static function date(string $s): bool {
        # ตรวจสอบว่า string สามารถแปลงเป็นวันที่ได้หรือไม่
        return (bool)strtotime($s);
        # ใช้ strtotime แปลง string → timestamp
        # ถ้าแปลงไม่ได้ → คืน false
    }
}
