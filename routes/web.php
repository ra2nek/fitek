<?php

use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DietController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\DietPlanController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SearchController;


use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Middleware

use App\Http\Middleware\AdminOnly;
use App\Models\DietPlan;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('\\', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/home', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//! Site renderings

Route::middleware(['auth', 'verified'])->group(function(){

    Route::get('/twoje-kursy', function () {
        return Inertia::render('Dashboard/UserCourses');
    })->name('user.courses');

    Route::get('/posilki', function() {
        return Inertia::render('Dashboard/Meals');
    })->name('meals');

    Route::get('/dashboard/kursy/{id}', function(){
        return Inertia::render('Dashboard/Course');
    })->name('course');

    Route::get('/dashboard/posilki/{id}', function(){
        return Inertia::render('Dashboard/Meal');
    })->name('meal');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Dashboard');
    })->name('dashboard');

    // Route::get('/dashboard/treningi', function(){
    //     return Inertia::render('Dashboard/Trainings');
    // })->name('trainings');

    Route::get('/dieta-formularz', function(){
        return Inertia::render('Dashboard/DietForm');
    })->name('diet.form');

    Route::get('/wyszukiwarka', function(){
        return Inertia::render('Dashboard/Search');
    })->name('search');

    Route::get('/dzisiejsza_dieta', function() {
        return Inertia::render('Dashboard/DailyDiet');
    })->name('dailyDiet');

});

//! ADMIN ONLY

Route::middleware([AdminOnly::class])->group(function (){
    Route::get('/admin/dashboard', function() {
        return Inertia::render('Admin/AdminDashboard');
    })->name('admin.dashboard');

    Route::get('/admin/kursy', function() {
        return Inertia::render('Admin/ManageCourses');
    })->name('admin.courses');

    Route::get('/admin/posiłki', function() {
        return Inertia::render('Admin/ManageMeals');
    })->name('admin.meals');

    Route::get('/admin/użytkownicy', function() {
        return Inertia::render('Admin/ManageUsers');
    })->name('admin.users');

    Route::get('/admin/menedzer_diet', function() {
        return Inertia::render('Admin/ManageDietCalendar');
    })->name('admin.dietCalendar');

    Route::get('/admin/pliki', function() {
        return Inertia::render('Admin/ManageFiles');
    })->name('admin.files');
});

//! USER RELATED

Route::middleware(['auth', 'verified'])->group(function(){

    Route::put('/user/updateCourses', [UserController::class, 'update_courses'])->name("user.updateCourses");
    Route::put('/user/updateMeals', [UserController::class, 'update_meals'])->name("user.updateMeals");
    Route::get('/user/courses', [UserController::class, 'get_courses'])->name("user.getCourses");
    Route::get('/user/meals', [UserController::class, 'get_meals'])->name("user.getMeals");
    Route::get('/user/has_filled_form', [UserController::class, 'has_filled_form'])->name('user.has_filled_form');


});

//! AUTH

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

//! CALORIES FORMS ETC.

Route::middleware('auth')->group(function () {

    Route::post('/diet/submit', [DietController::class, 'store'])->name('diet.store');
    Route::post('/diet/update', [DietController::class, 'store_short'])->name('diet.store_short');



});

//! COURSES 

Route::middleware(['auth', 'verified'])->group(function(){

    Route::get('/courses/all', [CourseController::class, 'get'])->name('courses.all');
    Route::get('/courses/course/{id}', [CourseController::class, 'get_one'])->name('courses.one');
    Route::get('/courses/trainings/{type}', [CourseController::class,'get_type'])->name('courses.type');

    Route::get('/courses/search/{title}', [CourseController::class,'search'])->name('courses.search');
    Route::get('/courses/search/titles/{title}', [CourseController::class,'search_names'])->name('courses.searchTitles');

    // Post

    Route::middleware(AdminOnly::class)->group(function(){
        Route::post('/courses/addNew', [CourseController::class, 'store'])->name('courses.store');
        Route::put('/courses/update/${id}', [CourseController::class, 'update'])->name('courses.update');
        Route::put('/courses/delete/${id}', [CourseController::class, 'delete'])->name('courses.delete');
    });


    
});

//! MEALS

Route::middleware('auth')->group(function () {

    Route::get('/meals', [MealController::class, 'get'])->name('meals.all');
    Route::get('/meals/one/{id}', [MealController::class, 'get_one'])->name('meals.one');
    Route::get('/meals/time/{meal_time}', [MealController::class, 'get_meal_time'])->name('meals.time');

    Route::get('/meals/search/{title}', [MealController::class,'search'])->name('meals.search');
    Route::get('/meals/search/meal_time/{title}', [MealController::class,'search_by_meal_time'])->name('meals.searchMealTime');
    Route::get('/meals/search/titles/{title}', [MealController::class,'search_names'])->name('meals.searchTitles');

    Route::middleware(AdminOnly::class)->group(function(){
        Route::post('/meals/addNew', [MealController::class, 'store'])->name('meals.store');
        Route::put('/meals/update/{id}', [MealController::class, 'update'])->name('meals.update');
        Route::put('/meals/delete/{id}', [MealController::class, 'delete'])->name('meals.delete');
    });
    
});

//! SEARCH ENGINE

// The searchquery requires params: title (title), sorting (sorting), course difficulty ( levels ), meal time for meals ( meal_times )
Route::get('/searchengine/search', [SearchController::class, 'search'])->name('searchengine.search');
Route::get('/searchengine/test', [SearchController::class, 'test'])->name('searchengine.test');

//! PLANNED DIETS

Route::middleware('auth')->group(function (){
    
    Route::get('/daily_diet', [DietPlanController::class, 'get_todays'])->name('plan.today');
    Route::get('/diet_plan/{date}', [DietPlanController::class, 'get_day'])->name('plan.day');

    Route::post('/diet_plan/add_new', [DietPlanController::class, 'store'])->name('plan.add');
    Route::put('/diet_plan/update/{id}', [DietPlanController::class, 'update'])->name('plan.update');
    
});

//! MAIL

Route::get('/send-ingredients-list', [MailController::class, 'sendIngredientsList'])->name('mail.ingredients-list');



//! Files

Route::post('/storage/create_test_file', [FileUploadController::class, 'test'])->name('storage.create_test');
Route::post('/storage/upload_image', [FileUploadController::class, 'upload_image'])->name('storage.upload_image');

Route::get('/storage/search/{query}', [FileController::class, 'search'])->name('storage.search');
Route::delete('/storage/destroy/id={id}&name={filename}', [FileController::class, 'destroy'])->name('storage.destroy');

Route::get('/test-image', function () {
    return response()->file(storage_path('app/public/images/ada.png'));
});




require __DIR__.'/auth.php';





//! Subscriptions unused

// Route::middleware(['auth'])->group(function(){

//     Route::get('/choose-your-plan', function () {
//         return Inertia::render('User_setup/SubscriptionSelection');
//     })->name('subscription-page');

//     Route::get('/avaiable-plans', [SubscriptionController::class, 'get_data'])->name('subscription-plans');

//     Route::put('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');

// });
