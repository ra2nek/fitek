<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Controllers\FileController;

use function PHPUnit\Framework\isEmpty;

class CourseController extends Controller
{

    public function get(){
        return Course::all();
    }

    public function get_one($id){
        return Course::where('id', $id)->first();
    }

    public function get_type($type){

        return Course::all()->where('type', $type);
        
    }

    public function store(Request $request){

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|string',
            'type' => 'required|string',
            'category' => 'required|string',
            'tags' => 'nullable|array',
            'level' => 'required|string',
            'time' => 'required|string'
        ]);

        $thumbnail = $validatedData['thumbnail_url'];

        $course = new Course;
        $file = new FileController;

        if($file->getImageFromStorage($thumbnail)) $thumbnail = $file->getImageFromStorage($thumbnail);

        $course->title = $validatedData['title'];
        $course->description = $validatedData['description'];
        $course->video_url = $validatedData['video_url'];
        $course->thumbnail_url = $thumbnail ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7PbZ1W6OwebI2OxX1EZKnBCl7wtqYz8OTaQ&s';
        $course->type = $validatedData['type'];
        $course->category = $validatedData['category'];
        $course->level = $validatedData['level'];
        $course->tags = json_encode($validatedData['tags'] ?? []); // Store tags as JSON
        $course->time = $validatedData['time'];

        $course->save();

        return response()->json(['message' => 'Course created successfully', 'course' => $course], 201);
    }

    public function update($id ,Request $request){
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|string',
            'type' => 'required|string',
            'category' => 'required|string',
            'tags' => 'nullable|array',
            'level' => 'required|string',
            'time' => 'required|string',
        ]);

        $thumbnail = $validatedData['thumbnail_url'];

        $file = new FileController;

        if($file->getImageFromStorage($thumbnail)) $thumbnail = $file->getImageFromStorage($thumbnail);

        $course = Course::findOrFail($id);
        $course->title = $validatedData['title'];
        $course->description = $validatedData['description'];
        $course->video_url = $validatedData['video_url'];
        $course->thumbnail_url = $thumbnail ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7PbZ1W6OwebI2OxX1EZKnBCl7wtqYz8OTaQ&s';
        $course->type = $validatedData['type'];
        $course->category = $validatedData['category'];
        $course->level = $validatedData['level'];
        $course->tags = json_encode($validatedData['tags'] ?? []); // Store tags as JSON
        $course->time = $validatedData['time'];

        $course->save();

        return response()->json(['message' => 'Course updated successfully', 'course' => $course], 200);
    }

    public function delete($id){
        Course::destroy($id);
        return response()->json(['message' => 'Course deleted successfully', 'course id:' => $id], 200);
    }

    public function like($id){

        $course = Course::where('id', $id)->first();

        $course->likes++;

        $course->save();

        return response($course);
    }

    public function dislike($id){

        $course = Course::where('id', $id)->first();

        $course->likes--;

        $course->save();

        return response($course);
    }

    public function viewed($id){

        $course = Course::where('id', $id)->first();

        $course->views++;

        $course->save();

        return response($course);
    }

    public function search($title, Request $request){

        $courses = Course::where('title', 'LIKE', '%'.trim($title).'%')->get();

        $levels = $request->query('levels', []);
        $time = $request->query('time', '');
        $sort = $request->query('sort', '');
        $offset = $request->query('offset', 0);

        if(!empty($levels)){
            // switch(count($levels)) {
            //     case 1: 
            //         $courses = $courses->where('level', '=', $levels[0] );
            //         break;
            //     case 2:
            //         $coursesF = $courses->where('level', '=', $levels[0] )->all();
            //         $coursesS = $courses->where('level', '=', $levels[1] )->all();
            //         $courses = array_merge($coursesF, $coursesS);
            //         break;
            // }

            $filtered_array = [];
            
            for($i = 0; $i < count($levels); $i++){
                $filtered = $courses->where('level', '=', $levels[$i])->all();
                $filtered_array = array_merge($filtered_array, $filtered);
            }

            if(!empty($filtered_array)) $courses = $filtered_array;
        }

        switch($time){
            case 'below':
                $courses = $courses->where('time', '<=', 30)->all();
                break;
            case 'between':
                $courses = $courses->where('time', 'BETWEEN', '30, 60')->all();
                break;
            case 'above':
                $courses = $courses->where('time', '>=', 60)->all();
                break;
        }

        $sorted = [];

        switch($sort){
            case 'date-asc':
                $sorted = $courses->sortBy('created_at', options: SORT_STRING)->values();
                break;
            case 'date-desc':
                $sorted = $courses->sortByDesc('created_at', options: SORT_STRING)->values();
                break;
            case 'likes-asc':
                $sorted = $courses->sortBy('likes')->values();
                break;
            case 'likes-desc':
                $sorted = $courses->sortByDesc('likes')->values();
                break;
        }

        if(!empty($sorted)) $courses = $sorted;

        $courses = $courses->skip($offset)->take(5)->all();       

        return $courses;

    }

    public function search_names($title, Request $request){

        $offset = $request->query('offset', 0);

        $courses = Course::where('title', 'LIKE', '%'.$title.'%')
            ->skip(intval($offset))->take(7)
            ->get(['title', 'id']);

        return $courses;
    }

}
