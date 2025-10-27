FROM php:8.2-apache

# ติดตั้ง mysqli (สำหรับเชื่อม MySQL)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# เปิด mod_rewrite (ถ้าใช้ .htaccess)
RUN a2enmod rewrite

# คัดลอกไฟล์เว็บทั้งหมดเข้า Apache (html root)
COPY ./php /var/www/html/
COPY ./css /var/www/html/css/

# เปิดพอร์ต 80
EXPOSE 80
