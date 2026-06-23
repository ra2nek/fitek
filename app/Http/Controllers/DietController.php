<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DietController extends Controller
{
    public function store(Request $request): RedirectResponse{
        $user_info = json_decode($request->user()->data);
        $user_diet = json_decode($request->user()->diet);

        $user_info->age = $request->age;
        $user_info->gender = $request->gender;
        $user_info->height = $request->height;
        $user_info->weight = $request->weight;
        $user_info->activity_level = $request->activity;
        $user_info->work_type = $request->work;
        $user_info->goal = $request->goal;
        $user_info->diet = $request->diet;

        $user_diet->PPM = $request->PPM;
        $user_diet->PAL = $request->PAL;
        $user_diet->BMI = $request->BMI;
        $user_diet->calorie_intake = $request->calorieIntake;


        $request->user()->update([
            'data' => json_encode($user_info),
            'diet' => json_encode($user_diet),
            'diet_form_filled' => 1
        ]);

        return redirect('/dashboard')->with('success', '');
    }

    public function store_short(Request $request){
        $user = json_decode($request->user()->data);
        $diet = json_decode($request->user()->diet);

        $age = isset($request->age) ? $request->age : $user->age;
        $weight = isset($request->weight) ? $request->weight : $user->weight;
        $height = isset($request->height) ? $request->height : $user->height;

        $PAL = $diet->PAL;

        $PPM = $user->gender == 'woman' ? $PPM = 655.1 + (9.563 * $weight) + (1.85 * $height) - (4.676 * $age) : $PPM = 66.47 + (13.75 * $weight) + (5.003 * $height) - (6.775 * $age);

        $PPM = round($PPM * 100) / 100;

        $BMI = $BMI = round($weight / pow($height / 100, 2), 1);

        $diet->calorie_intake = round($PAL * $PPM);
        

        switch($user->goal){
            case 'loss':
                if($BMI > 25){
                    $diet->calorie_intake -= 500;
                } else if ($BMI < 25){
                    $diet->calorie_intake -= 300;
                } else {
                    $diet->calorie_intake -= 400;
                }
                break;
            case 'gain':
                $diet->calorie_intake += 300;
                break;
            default:
        }

        $user->age = $age;
        $user->weight = $weight;
        $user->height = $height;

        $diet->PPM = $PPM;
        $diet->BMI = $BMI;

        $request->user()->update([
            'data' => json_encode($user),
            'diet' => json_encode($diet)
        ]);

    }


}
