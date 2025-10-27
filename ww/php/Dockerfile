FROM php:8.2-apache

# ติดตั้ง extension ที่จำเป็น เช่น mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# เปิด mod_rewrite (ถ้าจะใช้ .htaccess)
RUN a2enmod rewrite
