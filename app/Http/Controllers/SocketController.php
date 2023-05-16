<?php
declare(strict_types=1);
namespace App\Http\Controllers;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Http\Request;
use Pusher\Pusher;


class SocketController extends Controller
{
    public function connect(Request $request){

      
        $broadcaster = new PusherBroadcaster(
            new Pusher(
                env("PUSHER_APP_KEY",'staging'),
                env("PUSHER_APP_SECRET",'staging'),
                env("PUSHER_APP_ID",'staging')
                ,[]
            )
        );

        return $broadcaster->validAuthenticationResponse($request, []);
    //   dd("ok");
    }
}
