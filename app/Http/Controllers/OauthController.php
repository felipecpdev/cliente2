<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OauthController extends Controller
{
    public function redirect(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => config('services.codersfree.client_id'),
            'redirect_uri' => route('callback'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
        ]);


        return redirect('http://127.0.0.1:8000/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {

        $state = $request->session()->get('state');

      /*  throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );*/

        $response = Http::asForm()->post('http://127.0.0.1:8000/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.codersfree.client_id'),
            'client_secret' => config('services.codersfree.client_secret'),
            'redirect_uri' => route('callback'),
            'code' => $request->code
        ]);

        return $response->json();
    }
}
