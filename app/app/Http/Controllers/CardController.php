<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Card;

class CardController extends Controller
{
    public function show(Request $request){
        $id = $request->id;
        if (!$id) {
            $data['title'] = '404';
            $data['name'] = 'Card not found';
        
            return response()->view('errors.404',$data,404);
        }

        $card = Card::where('id', $id)->first();

        if (!$card){
            $data['title'] = '404';
            $data['name'] = 'Card not found';
        
            return response()->view('errors.404',$data,404);
        }

        return $card;
    }

    public function delete(Request $request){
        Card::where('id',$request->id)->first()->delete();
        
        return 'card deleted';
    }

    public function create(Request $request){
        $user = User::create($request->all());
        return $user;
    }
    
    public function update(Request $request){
        $affected = DB::table('cards')
              ->where('id', $request->id)
              ->update($request->all());
    }
}