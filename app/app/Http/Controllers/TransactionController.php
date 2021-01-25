<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Card;
use App\Models\Transaction;

class TransactionController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::user() ?: null;
        if (!$this->user){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function addTransaction(Request $request){
        $user = $this->user;
        $data = $this->decryptedData($request);

        // Check if auth user have card

        if ($user->card->code != $data['from']['code'] || $user->card->key != $data['from']['key']){
            return response()->json(['error' => 'Its not your card'], 401);
        }

        if(!$this->checkIfcardExist($data['to'])){
            return response()->json(['error' => 'Reciever card not found'], 401);
        }

        if($user->card->code == $data['to']['code'] && $user->card->key == $data['to']['key']){
            return response()->json(['error' => 'Very smart ... -_-'], 401);
        }

        if($user->card->amount < $request['amount']){
            return response()->json(['error' => "Your poor XD"], 401);
        }

        $transaction = [
            'from_id' => $this->getCardId($data['from']),
            'to_id'   => $this->getCardId($data['to']),
            // 'amount'  => (float)$request['amount']
        ];

        Transaction::create($transaction);

        return $transaction;
    }

    private function checkIfcardExist($card){
        return Card::where('key', $card['key'])
            ->where('code', $card['code'])
            ->get()
            ->count() == 1;
    }

    private function getCardId($card){
        return Card::where('key', $card['key'])
            ->where('code', $card['code'])
            ->first()
            ->id;
    }

    private function updateCard($card, $amount){ //@TOFIX
        Card::where('key', $card['key'])
            ->where('code', $card['code'])
            ->first()
            ->id;
    }

    private function decryptedData($data){
        return [
            "from" => [
                "code" => $this->decryptRSA($data['from']['code']),
                "key" => $this->decryptRSA($data['from']['key']),
            ],
            "to" => [
                "code" => $this->decryptRSA($data['to']['code']),
                "key" => $this->decryptRSA($data['to']['key']),
            ]
        ];
    }

    private function decryptRSA($data){
        $user = $this->user;
        $prvKey = Storage::disk('local')->get('/rsa/'. $user->id . 'rsakey');
        $data = base64_decode($data);
        openssl_private_decrypt ( $data , $decrypted , $prvKey);
        return $decrypted;
    }

    public function getPubKey(){
        $user = Auth::user() ?: null;
        if (!$user){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!$user->rsakey){
            return response()->json(['error' => 'No key'], 401);
        }

        return ($user->rsakey->key);
    }
}