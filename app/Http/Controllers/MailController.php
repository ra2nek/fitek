<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\mailList;
use App\Models\User;

class MailController extends Controller

{
    public function sendIngredientsList(Request $request){

        $user = $request->user();
        $list = $request->query('ingredients', '');
        $meal = $request->query('meal', '');
        $subject = 'Lista składników';
        $response = Mail::to($user->email)->send(new mailList($user->name, $subject, $list, $meal));

        return response()->json([$user, $list, $meal]);

    }
}
