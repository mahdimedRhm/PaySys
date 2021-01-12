<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Card;
use App\Models\Rsakey;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    //Register
    public function register(Request $request){
        $user = $request->all();
        $user['password'] = Hash::make($request->password);
        User::create($user);

        return $user;
    }
    public function test(){
        return response()->json([ 'valid' => auth('api')->check() ]);
    }
    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    public function addCard(){
        $user = Auth::user() ?: null;
        if (!$user){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user->card){
            return response()->json(['error' => 'You have a card'], 401);
        }

        $keys = $this->generateRSAKeys();
        $rsapubkey = Rsakey::create(['key' => $keys['pub']]);
        $rsapubkey->user()->save($user);
        Storage::disk('local')->put('/rsa/'.$user->id . 'rsakey', $keys['prv']);

        $card = Card::create($this->generateCard());
        $card->user()->save($user);


        return $card;
    }

    private function generateCard(){
        return [
            'key'   => str_pad(rand(0, pow(10, 3)-1), 3, '0', STR_PAD_LEFT),
            'code'  => (string) Carbon::now()->timestamp,
            'amount'=> 0.0
        ];
    }

    private function generateRSAKeys(){
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        // Create the keypair
        $res=openssl_pkey_new($config);

        // Get private key
        openssl_pkey_export($res, $privkey);

        // Get public key
        $pubkey=openssl_pkey_get_details($res);
        $pubkey=$pubkey["key"];

        return [
            "pub" => $pubkey,
            "prv" => $privkey
        ];

    }
}