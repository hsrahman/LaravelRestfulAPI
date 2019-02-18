<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function token (Request $request) {

        $query = http_build_query([
            'client_id' => DB::table('oauth_clients')->where('user_id', Auth::user()->id)->first()->id,
            'redirect_uri' => 'http://restapi.test/authtoken',
            'response_type' => 'code',
            'scope' => '',
        ]);

        return redirect('http://restapi.test/oauth/authorize?'.$query);
    }

    function authtoken (Request $request) {
        $http = new Client();

        $response = $http->post('http://restapi.test/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => DB::table('oauth_clients')->where('user_id', Auth::user()->id)->first()->id,
                'client_secret' => DB::table('oauth_clients')->where('user_id', Auth::user()->id)->first()->secret,
                'redirect_uri' => 'http://restapi.test/authtoken',
                'code' => $request->code,
            ],
        ]);
        $json = json_decode((string) $response->getBody());
        $user = User::find(Auth::user()->id);
        $user->access_token = $json->access_token;
        $user->refresh_token = $json->refresh_token;
        if($user->save()){
            return redirect('/home');
        }
        
    }

    function refreshtoken (Request $request) {
        $http = new Client();
        $oauth_client = DB::table('oauth_clients')->where('user_id', Auth::user()->id)->first();
        $response = $http->post('http://restapi.test/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => Auth::user()->refresh_token,
               'client_id' =>  $oauth_client->id,
                'client_secret' => $oauth_client->secret,
                'scope' => '',
            ],
        ]);

        $json = json_decode((string) $response->getBody());
        $user = User::find(Auth::user()->id);
        $user->access_token = $json->access_token;
        $user->refresh_token = $json->refresh_token;
        if($user->save()){
            return redirect('/home');
        }
    }
}
