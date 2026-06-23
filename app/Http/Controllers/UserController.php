<?php

// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\CourseController;
use App\Http\Controllers\MealController;

class UserController extends Controller
{

    public function get_courses(Request $request){

        $JSON = $request->user()->courses;
        
        return $JSON;
    }

    public function get_meals(Request $request){

        $JSON = $request->user()->meals;
        
        return $JSON;
    }

    public function get_role(Request $request){
        return $request->user()->role;
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created successfully']);
    }

    public function update_courses(Request $request){

        $courseController = new CourseController();

        $courses = json_decode($request->user()->courses);

        $id = (string)$request->get('value');
        $place = (string)$request->get('to');

        if($place != 'RecentlyWatched' &&  $place != 'Liked' && $place != 'ToWatch' ) return response('Error: No tab');

        $arr = $courses->$place;
        $value = $id;

        if($place == 'RecentlyWatched'){

            if(in_array($value, $arr)){
                $key = array_search($value, $arr);
                array_splice($arr, $key, 1);
            }
            
            array_push($arr, $id);

            $courseController->viewed($id);

        }

        if($place == 'Liked' || $place == 'ToWatch'){
            if(in_array($value, $arr)){
                $key = array_search($value, $arr);
                array_splice($arr, $key, 1);

                $courseController->dislike($id);
            } else {
                array_push($arr, $id);

                $courseController->like($id);
            }
        }
 
        $courses->$place = $arr;

        $request->user()->update([
            'courses' => json_encode($courses)
        ]);

    }

    public function update_meals(Request $request){

        $mealController = new MealController();

        $meal = json_decode($request->user()->meals);

        $id = (string)$request->get('value');
        $place = (string)$request->get('to');

        

        if($place != 'RecentlyWatched' &&  $place != 'Liked' && $place != 'ToWatch' ) return response('Error: No tab');

        $arr = $meal->$place;
        $value = $id;

        if($place == 'RecentlyWatched'){

            if(in_array($value, $arr)){
                $key = array_search($value, $arr);
                array_splice($arr, $key, 1);
            }
            
            array_push($arr, $id);

            $mealController->viewed($id);

        }

        if($place == 'Liked' || $place == 'ToWatch'){
            if(in_array($value, $arr)){
                $key = array_search($value, $arr);
                array_splice($arr, $key, 1);

                $mealController->dislike($id);
            } else {
                array_push($arr, $id);

                $mealController->like($id);
            }
        }
 
        $meal->$place = $arr;

        $request->user()->update([
            'meals' => json_encode($meal)
        ]);

    }

    public function has_filled_form(Request $request){
        $has_filled = $request->user()->diet_form_filled;
        // $has_filled = $has_filled[0]["diet_form_filled"];
        
        if($has_filled == "0") return "false";
        else return "true";
    }

    

}

