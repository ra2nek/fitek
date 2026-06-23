<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function get_data(){

        return Subscription::all();
        
    }

    public function update(Request $request) {

        $request->user()->update([
            'subscription_type' => $request['subscription'] // Update the user's subscription
        ]);    
    }
    
}
