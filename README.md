	git clone https://github.com/WalyLin/interview.git

	composer install

	cp .env.example .env

	修改.env  配置数据库 和 APP_URL , 

执行命令：
```
php artisan migrate
php artisan key:generate
php artisan passport:install
php artisan db:seed
```


执行命令：
 ```
php artisan passport:client --password
```
将生成的 Client ID 和 Client secret 设置到.env
```
CLIENT_ID=Client ID
CLIENT_SECRET= Client secret
```


设置权限
```
sudo chown -R www-data:www-data /var/www/项目根目录

sudo chmod -R 775 /var/www/项目根目录/storage

sudo chmod -R 775 /var/www/项目根目录/bootstrap/cache
```


nginx配置：
```
server {
    listen 3999;
    server_name _;
    root /var/www/项目根目录/public;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock; # 根据你的 PHP 版本调整
    }

    location ~ /\.ht {
        deny all;
    }
}
```

地址：

```
 登陆页： http://app_url/login
 api文档页：http://app_url/api/documentation
 customer列表页： http://app_url/api/customer
```
