-- path: backend/sql/001_schema.sql

SET NAMES utf8mb4;
-- กำหนด character set ของฐานข้อมูลเป็น utf8mb4 (รองรับภาษาไทย + emoji)

CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,              -- รหัสลูกค้า (PK, auto increment)
  email VARCHAR(191) UNIQUE NOT NULL,             -- อีเมล (ห้ามซ้ำ, ห้ามว่าง)
  password_hash VARCHAR(255) NOT NULL,            -- รหัสผ่าน (เก็บแบบ hash)
  name VARCHAR(191) NOT NULL,                     -- ชื่อลูกค้า
  role ENUM('user','staff','owner') NOT NULL DEFAULT 'user', -- บทบาท (ค่าเริ่มต้น user)
  created_at DATETIME NOT NULL                    -- วันที่สร้างบัญชี
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cars (
  id INT AUTO_INCREMENT PRIMARY KEY,              -- รหัสรถ (PK)
  type VARCHAR(50),                               -- ประเภทรถ (SUV, Sedan ฯลฯ)
  brand VARCHAR(100),                             -- ยี่ห้อรถ
  model VARCHAR(100),                             -- รุ่นรถ
  gear ENUM('AT','MT') DEFAULT 'AT',              -- เกียร์ (ออโต้/ธรรมดา)
  seats TINYINT,                                  -- จำนวนที่นั่ง
  color VARCHAR(50),                              -- สีรถ
  smoking TINYINT(1) DEFAULT 0,                   -- อนุญาตสูบบุหรี่หรือไม่ (0=ไม่,1=ใช่)
  with_driver TINYINT(1) DEFAULT 0,               -- มีคนขับหรือไม่ (0=ไม่,1=ใช่)
  price_per_day INT NOT NULL,                     -- ราคาต่อวัน
  images_json JSON,                               -- เก็บรูปภาพ/วิดีโอเป็น JSON array
  created_at DATETIME NOT NULL                    -- วันที่เพิ่มเข้าระบบ
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,              -- รหัสการจอง (PK)
  car_id INT NOT NULL,                            -- อ้างอิงรถที่จอง (FK → cars.id)
  customer_id INT NOT NULL,                       -- อ้างอิงลูกค้า (FK → customers.id)
  start_at DATETIME NOT NULL,                     -- วันที่-เวลาเริ่มจอง
  end_at DATETIME NOT NULL,                       -- วันที่-เวลาสิ้นสุดการจอง
  pickup_location VARCHAR(191),                   -- สถานที่รับรถ
  dropoff_location VARCHAR(191),                  -- สถานที่คืนรถ
  options_json JSON,                              -- ตัวเลือกเพิ่มเติม (options) เช่น ที่นั่งเด็ก
  coupon_code VARCHAR(50),                        -- รหัสคูปองที่ใช้ (nullable)
  subtotal INT NOT NULL,                          -- ราคารวมก่อนหักส่วนลด
  discount INT NOT NULL DEFAULT 0,                -- ส่วนลด
  total INT NOT NULL,                             -- ราคารวมสุทธิ
  status ENUM('pending','confirmed','cancelled','checked_in','checked_out','refunded') NOT NULL DEFAULT 'pending',
  -- สถานะการจอง: pending (รอดำเนินการ), confirmed (ยืนยัน), cancelled (ยกเลิก), checked_in, checked_out, refunded

  created_at DATETIME NOT NULL,                   -- วันที่สร้างการจอง
  INDEX idx_car_time (car_id, start_at, end_at),  -- index สำหรับค้นหาเวลาการจองของรถ
  CONSTRAINT fk_car FOREIGN KEY (car_id) REFERENCES cars(id),         -- FK → cars
  CONSTRAINT fk_customer FOREIGN KEY (customer_id) REFERENCES customers(id) -- FK → customers
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,              -- รหัสคูปอง (PK)
  code VARCHAR(50) UNIQUE NOT NULL,               -- รหัสคูปอง (ห้ามซ้ำ)
  type ENUM('percent','fixed') NOT NULL,          -- ประเภทคูปอง: percent (เปอร์เซ็นต์), fixed (จำนวนเงิน)
  value INT NOT NULL,                             -- ค่าลด (เช่น 10% หรือ 500 บาท)
  min_amount INT NOT NULL DEFAULT 0,              -- ยอดขั้นต่ำที่ใช้คูปองได้
  active TINYINT(1) NOT NULL DEFAULT 1,           -- เปิดใช้งานหรือไม่ (1=ใช่,0=ไม่)
  starts_at DATETIME NOT NULL,                    -- วันเริ่มใช้คูปอง
  ends_at DATETIME NOT NULL                       -- วันหมดอายุคูปอง
) ENGINE=InnoDB;

-- ---------------- ตัวอย่างข้อมูลเริ่มต้น (seed) ----------------

INSERT INTO customers (email,password_hash,name,role,created_at)
VALUES (
  'owner@example.com',
  '$2y$10$3x6e8oQ2iV7bXx6QvS6hcuWZ0m6j9f1H2qF0Z8f7zUPa8X3r5b7F2', 
  'Owner',
  'owner',
  NOW()
);
-- เพิ่ม user เจ้าของระบบ (owner) พร้อม password hash

INSERT INTO cars (type,brand,model,gear,seats,color,smoking,with_driver,price_per_day,images_json,created_at)
VALUES 
  ('SUV','Toyota','Fortuner','AT',7,'Black',0,0,2200,JSON_ARRAY(),NOW()),
  ('Sedan','Honda','Civic','AT',5,'White',0,0,1500,JSON_ARRAY(),NOW());
-- เพิ่มรถ 2 คัน: Toyota Fortuner (SUV) และ Honda Civic (Sedan)

INSERT INTO coupons (code,type,value,min_amount,active,starts_at,ends_at)
VALUES 
  ('WELCOME10','percent',10,1000,1,NOW(),DATE_ADD(NOW(), INTERVAL 1 YEAR));
-- เพิ่มคูปองต้อนรับ: WELCOME10 ลด 10% เมื่อยอด >= 1000 ใช้ได้ 1 ปี
