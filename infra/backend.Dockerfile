FROM php:8.2-apache  
# ใช้ base image PHP 8.2 พร้อม Apache เป็น web server

RUN apt-get update \
 && apt-get install -y libzip-dev zip unzip libonig-dev \
 && docker-php-ext-install pdo pdo_mysql
# อัปเดต package list → ติดตั้ง library ที่จำเป็น (zip, unzip, oniguruma)  
# และติดตั้ง PHP extensions: pdo, pdo_mysql (สำหรับเชื่อมต่อ MySQL)

RUN a2enmod rewrite  
# เปิดใช้งาน Apache mod_rewrite (จำเป็นสำหรับ Laravel/ระบบ route-based)

WORKDIR /var/www/html  
# กำหนดโฟลเดอร์ทำงานหลักใน container เป็น /var/www/html

# คัดลอกเฉพาะของที่จำเป็นเพื่อ cache ขั้นตอน composer
COPY composer.json /var/www/html/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# คัดลอกโค้ดทั้ง backend (context ปัจจุบัน)
COPY . /var/www/html

# ติดตั้ง dependencies
RUN composer install --no-interaction || true

# ปรับสิทธิ์และเตรียมโฟลเดอร์อัปโหลด
RUN chown -R www-data:www-data /var/www/html \
 && mkdir -p storage/uploads \
 && chown -R www-data:www-data storage