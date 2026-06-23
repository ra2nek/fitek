<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Http\Controllers\FileController;

class MealController extends Controller
{
    public function get(){
        return Meal::all();
    }

    public function get_one($id){
        return Meal::where('id', $id)->first();
    }

    public function get_meal_time($time){
        return Meal::all()->where('meal_time', $time);
    }

        public function store(Request $request){
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|string',
            'meal_time' => 'required|string',
            'calories' => 'required|integer',
            'suitable_diets' => 'nullable|array',
            'ingredients' => 'required|array',
            'preparation' => 'required|array',
            'time' => 'required|integer', 
            'difficulty' => 'required|string',
        ]);

        $thumbnail = $validatedData['thumbnail'];

        $file = new FileController;

        if($file->getImageFromStorage($thumbnail)) $thumbnail = $file->getImageFromStorage($thumbnail);

        $meal = new Meal;
        $meal->title = $validatedData['title'];
        $meal->description = $validatedData['description'];
        $meal->thumbnail = $thumbnail ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7PbZ1W6OwebI2OxX1EZKnBCl7wtqYz8OTaQ&s';
        $meal->meal_time = $validatedData['meal_time'];
        $meal->calories = $validatedData['calories'];
        $meal->suitable_diets = json_encode($validatedData['suitable_diets'] ?? []); 
        $meal->ingredients = json_encode($validatedData['ingredients']); 
        $meal->preparation = json_encode($validatedData['preparation']); 
        $meal->time = $validatedData['time'];
        $meal->difficulty = $validatedData['difficulty'];

        $meal->likes = 0; 
        $meal->views = 0; 

        $meal->save();

        return response()->json(['message' => 'Meal created successfully', 'meal' => $meal], 201);
    }

    public function update($id, Request $request){
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'meal_time' => 'required|string',
            'calories' => 'required|integer',
            'suitable_diets' => 'nullable|array',
            'ingredients' => 'required|array',
            'preparation' => 'required|array',
            'time' => 'required|integer', 
            'difficulty' => 'required|string',
        ]);

        $thumbnail = $validatedData['thumbnail'];

        $file = new FileController;

        if($file->getImageFromStorage($thumbnail)) $thumbnail = $file->getImageFromStorage($thumbnail);

        $meal = Meal::findOrFail($id);
        $meal->title = $validatedData['title'];
        $meal->description = $validatedData['description'];
        $meal->thumbnail = $thumbnail ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7PbZ1W6OwebI2OxX1EZKnBCl7wtqYz8OTaQ&s';
        $meal->meal_time = $validatedData['meal_time'];
        $meal->calories = $validatedData['calories'];
        $meal->suitable_diets = json_encode($validatedData['suitable_diets'] ?? []); 
        $meal->ingredients = json_encode($validatedData['ingredients']);
        $meal->preparation = json_encode($validatedData['preparation']); 
        $meal->time = $validatedData['time'];
        $meal->difficulty = $validatedData['difficulty'];

        $meal->save();

        return response()->json(['message' => 'Meal updated successfully', 'meal' => $meal], 200);
    }

    public function delete($id){
        Meal::destroy($id);
        return response()->json(['message' => 'Meal deleted successfully', 'meal_id' => $id], 200);
    }


    public function like($id){

        $meal= Meal::where('id', $id)->first();

        $meal->likes++;

        $meal->save();

        return response($meal);
    }

    public function dislike($id){

        $meal= Meal::where('id', $id)->first();

        $meal->likes--;

        $meal->save();

        return response($meal);
    }

    public function viewed($id){

        $meal= Meal::where('id', $id)->first();

        $meal->views++;

        $meal->save();

        return response($meal);
    }
    
        public function search($title, Request $request){
        // Base query
        $meals = Meal::where('title', 'LIKE', '%' . trim($title) . '%');

        // Filters
        $difficulties = $request->query('difficulty', []);
        $time = $request->query('time', '');
        $sort = $request->query('sort', '');
        $offset = intval($request->query('offset', 0));

        // Sorting
        if ($sort) {
            switch ($sort) {
                case 'date-asc':
                    $meals = $meals->orderBy('created_at', 'asc');
                    break;
                case 'date-desc':
                    $meals = $meals->orderBy('created_at', 'desc');
                    break;
                case 'likes-asc':
                    $meals = $meals->orderBy('likes', 'asc');
                    break;
                case 'likes-desc':
                    $meals = $meals->orderBy('likes', 'desc');
                    break;
            }
        }

        // Filter by difficulty levels
        if (!empty($difficulties)) {
            $meals = $meals->whereIn('difficulty', $difficulties);
        }

        // Filter by time
        if (!empty($time)) {
            switch ($time) {
                case 'below':
                    $meals = $meals->where('time', '<=', 30);
                    break;
                case 'between':
                    $meals = $meals->whereBetween('time', [30, 60]);
                    break;
                case 'above':
                    $meals = $meals->where('time', '>=', 60);
                    break;
            }
        }

        // Apply pagination (offset and limit)
        $meals = $meals->skip($offset)->take(10)->get();

        return $meals;
    }

    public function search_names($title, Request $request){

        $offset = $request->query('offset', 0);

        $meals = Meal::where('title', 'LIKE', '%' . $title . '%')
            ->skip($offset)
            ->take(7)
            ->get(['title', 'id']);

        return $meals;
    }


    public function search_by_meal_time($title ,Request $request){
        $meal_time = $request->query("meal_time", '');

        $meals = Meal::where('title', 'LIKE', '%' . $title . '%')->where('meal_time', '=', $meal_time)
        ->take(6)
        ->get(['title', 'id']);

        return $meals;
    }


    
}
