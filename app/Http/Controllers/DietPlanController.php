<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DietPlan;

use App\Http\Controllers\MealController;

use function PHPUnit\Framework\isEmpty;

class DietPlanController extends Controller
{
    public function get(){
        return DietPlan::all();
    }

    public function get_one($id){
        return DietPlan::findOrFail($id)->first();
    }

    public function get_todays(Request $request){
        $user = json_decode($request->user()->data);
        // return DietPlan::where('date', '=', (string) date('Y-m-d'))->where('diet', '=', $diet)->get();
        $diet = DietPlan::where('date', '=', (string) date('Y-m-d'))->where('diet', '=', $user->diet)->get();

        if( count($diet) == 0 ){
            return $diet;
        } else {
            $diet = $diet[0];
        }

        $controller = new MealController;

        $breakfast = $diet->breakfast_id === 0 ? null : $controller->get_one($diet->breakfast_id);
        $brunch = $diet->brunch_id === 0 ? null : $controller->get_one($diet->brunch_id);
        $lunch = $diet->lunch_id === 0 ? null : $controller->get_one($diet->lunch_id);
        $snack = $diet->snack_id === 0 ? null : $controller->get_one($diet->snack_id);
        $dinner = $diet->dinner_id === 0 ? null : $controller->get_one($diet->dinner_id);

        return response()->json($data = [
            "breakfast" => $breakfast,
            "brunch" => $brunch,
            "lunch" => $lunch,
            "snack" => $snack,
            "dinner" => $dinner,
        ]);

    }

    public function get_day( $date = "default" ){
        return DietPlan::where('date', '=', (string) $date)->get();
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'date' => 'required|string',
            'diet' => 'required|string',

            'breakfast' => 'required|int',
            'brunch' => 'nullable|int',
            'lunch' => 'required|int',
            'snack' => 'nullable|int',
            'dinner' => 'required|int',

        ]);

        $dietPlan = new DietPlan;

        $dietPlan->date = $validatedData['date'];
        $dietPlan->diet = $validatedData['diet'];

        $dietPlan->breakfast_id = $validatedData['breakfast'];
        $dietPlan->brunch_id = $validatedData['brunch'];
        $dietPlan->lunch_id = $validatedData['lunch'];
        $dietPlan->snack_id = $validatedData['snack'];
        $dietPlan->dinner_id = $validatedData['dinner'];

        $dietPlan->save();

        return response()->json(['message' => 'Plan created successfully', 'diet plan' => $dietPlan], 201);

    }

    public function update($id, Request $request) {
        $validatedData = $request->validate([
            'date' => 'required|string',
            'diet' => 'required|string',

            'breakfast' => 'required|int',
            'brunch' => 'nullable|int',
            'lunch' => 'required|int',
            'snack' => 'nullable|int',
            'dinner' => 'required|int',

        ]);

        $dietPlan = DietPlan::findOrFail($id);

        $dietPlan->date = $validatedData['date'];
        $dietPlan->diet = $validatedData['diet'];

        $dietPlan->breakfast_id = $validatedData['breakfast'];
        $dietPlan->brunch_id = $validatedData['brunch'];
        $dietPlan->lunch_id = $validatedData['lunch'];
        $dietPlan->snack_id = $validatedData['snack'];
        $dietPlan->dinner_id = $validatedData['dinner'];

        $dietPlan->save();

        return response()->json(['message' => 'Plan updated successfully', 'diet plan' => $dietPlan], 201);
    }
    
}
