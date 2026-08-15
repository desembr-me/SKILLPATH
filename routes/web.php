<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboard;
use App\Http\Controllers\Parent\OnboardingController;
use App\Http\Controllers\Parent\BookingController;
use App\Http\Controllers\Parent\LearningPathController;
use App\Http\Controllers\Parent\CreditController;
use App\Http\Controllers\Parent\ReviewController;
use App\Http\Controllers\Parent\PaymentController;
use App\Http\Controllers\Parent\ExamController as ParentExamController;
use App\Http\Controllers\Mentor\AttendanceController;
use App\Http\Controllers\Mentor\ExamController as MentorExamController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/explore', [CourseController::class,'index'])->name('explore.index');
Route::get('/courses/{course}', [CourseController::class,'show'])->name('courses.show');
Route::view('/how-it-works','how-it-works')->name('how-it-works');

Route::middleware('guest')->group(function(){
 Route::get('/login',[AuthController::class,'loginForm'])->name('login');
 Route::post('/login',[AuthController::class,'login'])->name('login.store');
 Route::get('/register',[AuthController::class,'registerForm'])->name('register');
 Route::post('/register',[AuthController::class,'register'])->name('register.store');
});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::prefix('parent')->name('parent.')->middleware(['auth','role:parent'])->group(function(){
 Route::get('/dashboard',ParentDashboard::class)->name('dashboard');
 Route::get('/onboarding',[OnboardingController::class,'create'])->name('onboarding');
 Route::post('/onboarding',[OnboardingController::class,'store'])->name('onboarding.store');
 Route::post('/bookings',[BookingController::class,'store'])->name('bookings.store');
 Route::get('/children/{child}/learning-path',[LearningPathController::class,'show'])->name('learning-path');
 Route::get('/credits',[CreditController::class,'index'])->name('credits');
 Route::post('/credits/{credit}/redeem',[CreditController::class,'redeem'])->name('credits.redeem');
 Route::post('/enrollments/{enrollment}/review',[ReviewController::class,'store'])->name('reviews.store');
 Route::post('/transactions/{transaction}/pay',[PaymentController::class,'pay'])->name('transactions.pay');
 Route::get('/exams',[ParentExamController::class,'index'])->name('exams');
});
Route::prefix('mentor')->name('mentor.')->middleware(['auth','role:mentor'])->group(function(){
 Route::get('/dashboard',MentorDashboard::class)->name('dashboard');
 Route::post('/attendance',[AttendanceController::class,'store'])->name('attendance.store');
 Route::post('/exam-attempts',[MentorExamController::class,'storeAttempt'])->name('exam-attempts.store');
});
Route::get('/admin/dashboard',AdminDashboard::class)->middleware(['auth','role:admin'])->name('admin.dashboard');
