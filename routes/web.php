<?php

use App\Events\SendMessage;
use BeyondCode\LaravelWebSockets\Apps\AppProvider;
use BeyondCode\LaravelWebSockets\Dashboard\DashboardLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (AppProvider $appProvider) {
    return view('livechat.chat',[
        "port" => env('LARAVEL_WEBSOCKET_PORT'),
        "host" => env('LARAVEL_WEBSOCKET_HOST'),
        "cluster" => env('PUSHER_APP_CLUSTER'),
        "authEndpoint" => "/api/sockets/connect",
        "logChannel" => DashboardLogger::LOG_CHANNEL_PREFIX,
        "apps" => $appProvider->all()
    ]);
});

Route::post("/chat/send", function(Request $request){
    $message = $request->input("message", null);
    $name = $request->input("name", null);
    $time = (new DateTime(now()))->format(DateTime::ATOM);
    if ($name == null) {
        $name = "Anonymous";
    }

    SendMessage::dispatch($name,$message,$time);

Route::get('/sockets/connect', 'SocketController@connect');

});
