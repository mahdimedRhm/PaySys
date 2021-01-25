<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Rsakey;
use Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CardController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::user() ?: null;
        if (!$this->user){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function addCard(){
        $user = $this->user;

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
            'amount'=> 1000.0
        ];
    }

    private function generateRSAKeys(){
        $user = Auth::user() ?: null;
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