## How to install 

npm install
composer install


Step 1
>   composer require beyondcode/laravel-websockets
Or
>   composer require beyondcode/laravel-websockets -w

Step 2
>   php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"

Step 3
>   php artisan migrate

Step 4
>   php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"

Check the congig/websockets.php

Step 5
>   composer require pusher/pusher-php-server

Step 6 Run the websocket
>   php artisan queue:work


>   php artisan websockets:serve


http://127.0.0.1:8000/laravel-websockets

## youtube tutorial
https://www.youtube.com/watch?v=qdhnC_FUBbs
