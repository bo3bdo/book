# استخدم أحدث إصدار PHP 8.3 الرسمي
FROM php:8.3-fpm-alpine

# تثبيت الاعتماديات الأساسية بالإضافة إلى Node.js و npm
RUN apk add --no-cache \
    build-base \
    mysql-client \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    icu-dev

# تكوين و تثبيت إضافات PHP الضرورية لـ Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
       gd \
       intl \
       exif \
       pdo_mysql \
       zip \
       bcmath \
       pcntl \
    # إزالة اعتماديات البناء لتقليل حجم الصورة
    && apk del build-base icu-dev

# تثبيت Composer عالميًا
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إنشاء مستخدم ومجموعة مخصصة لتجنب مشاكل الصلاحيات
RUN addgroup -g 1000 laravel && adduser -u 1000 -G laravel -s /bin/sh -D laravel
USER laravel

# تعيين مجلد العمل الافتراضي
WORKDIR /var/www/html

# فتح منفذ FPM
EXPOSE 9000

# الأمر الافتراضي
CMD ["php-fpm"]