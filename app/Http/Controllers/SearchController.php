<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Meal;
class SearchController extends Controller
{

    public function test(){
        return response("OwO");
    }

    public function search(Request $request){
        // Globals
        $title = $request->get('title', '');
        $type = $request->get('type', '');
        $sorting = $request->get('sorting', '');
        $levels = $request->get('levels', []);
        $time = $request->get('time', '');

        $offset = $request->get('offset', 0);

        // For Courses

        // For Meals
        $meal_times = $request->get('meal_times', []);

        $courses = $type !== 'meals' ? $this->getCourses($title, $levels, $time) : collect() ;
        $meals = $type !== 'courses' ? $this->getMeals($title, $levels, $time, $meal_times) : collect() ;

        $results = $courses->concat($meals)->values()->all();

        $sorting = empty($sorting) ? ['', ''] : explode("-", $sorting);

        if($sorting[0] === 'likes'){
            usort($results, function($a, $b){
                return $a->likes > $b->likes;
            });
        } else if($sorting[0] === 'date'){
            usort($results, function($a, $b){
                return strcmp($a->created_at, $b->created_at);
            });
        }

        if($sorting[1] === 'desc'){
            $results = array_reverse($results);
        }
        
        return collect($results)->take(6 + $offset);
        
    }
    private function getCourses($query, $levels, $time){
        $courses = collect();
        $courses = trim($query) != '' ? Course::where("title", "LIKE", '%' . trim($query) . '%')->get() : Course::all();

        $courses = match( trim($time) ) {
            'below' => $courses->where('time', '<=', 30),
            'between' => $courses->whereBetween('time', [30, 60]),
            'above' => $courses->where('time', '>=', 60),
            '' => $courses,
        };

        if (!empty($levels)) {
            $filtered = collect();

            foreach ($levels as $level) {
                $filtered = $filtered->merge($courses->where('level', $level));
            }

            $courses = $filtered->values();
        }

        return $courses;
    }

    private function getMeals($query, $levels, $time, $meal_times){
        $meals = collect();
        $meals = trim($query) != '' ? Meal::where('title', 'LIKE', '%'.trim($query).'%')->get()  : Meal::all();

        $meals = match( trim($time) ) {
            'below' => $meals->where('time', '<=', 30),
            'between' => $meals->whereBetween('time', [30, 60]),
            'above' => $meals->where('time', '>=', 60),
            '' => $meals,
        };

        if(!empty($levels)){
            $filtered = collect();

            foreach($levels as $level){
                $filtered = $filtered->merge($meals->where('difficulty', $level));
            }

            $meals = $filtered->values();
        }

        if(!empty($meal_times)){
            $filtered = collect();

            foreach($meal_times as $meal_time){
                $filtered = $filtered->merge($meals->where('meal_time', $meal_time));
            }

            $meals = $filtered->values();
        }

        return $meals;

    }
    
}
