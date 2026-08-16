<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboard;
use App\Http\Controllers\Parent\ProfileController;
use App\Http\Controllers\Parent\ChildController;
use App\Http\Controllers\Parent\OnboardingController;
use App\Http\Controllers\Parent\LearningPathController;
use App\Http\Controllers\Parent\ScheduleController;
use App\Http\Controllers\Parent\ReviewController;
use App\Http\Controllers\Parent\PaymentController;
use App\Http\Controllers\Parent\ExamController as ParentExamController;
use App\Http\Controllers\Parent\CertificateController;
use App\Http\Controllers\Parent\WishlistController;
use App\Http\Controllers\Parent\CartController;
use App\Http\Controllers\Parent\CheckoutController;
use App\Http\Controllers\Parent\OrderController;
use App\Http\Controllers\Parent\MyCourseController;
use App\Http\Controllers\Parent\LearnController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\Mentor\EarningsController as MentorEarningsController;
use App\Http\Controllers\Mentor\ScheduleController as MentorScheduleController;
use App\Http\Controllers\Mentor\RescheduleRequestController as MentorRescheduleController;
use App\Http\Controllers\Mentor\AttendanceController;
use App\Http\Controllers\Mentor\ExamController as MentorExamController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Mentor\ProfileController as MentorProfileController;
use App\Http\Controllers\Mentor\StudentController as MentorStudentController;
use App\Http\Controllers\Mentor\ReviewController as MentorReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/explore', [CourseController::class,'index'])->name('explore.index');
Route::get('/courses/{course}', [CourseController::class,'show'])->name('courses.show');
Route::get('/mentors', [MentorController::class,'index'])->name('mentors.index');
Route::get('/mentors/{mentor}', [MentorController::class,'show'])->name('mentors.show');
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
 Route::get('/profile',[ProfileController::class,'show'])->name('profile');
 Route::put('/profile',[ProfileController::class,'update'])->name('profile.update');
 Route::get('/children',[ChildController::class,'index'])->name('children');
 Route::put('/children/{child}',[ChildController::class,'update'])->name('children.update');
 Route::get('/onboarding',[OnboardingController::class,'create'])->name('onboarding');
 Route::post('/onboarding',[OnboardingController::class,'store'])->name('onboarding.store');
 Route::get('/children/{child}/learning-path',[LearningPathController::class,'show'])->name('learning-path');
 Route::get('/schedule',[ScheduleController::class,'index'])->name('schedule');
 Route::put('/schedule/{enrollment}',[ScheduleController::class,'update'])->name('schedule.update');
 Route::post('/platform-review',[ReviewController::class,'storePlatform'])->name('platform-review.store');
 Route::post('/enrollments/{enrollment}/mentor-review',[ReviewController::class,'storeMentor'])->name('mentor-reviews.store');
 Route::post('/enrollments/{enrollment}/review',[ReviewController::class,'store'])->name('reviews.store');
 Route::get('/exams',[ParentExamController::class,'index'])->name('exams');
 Route::put('/learning-path/items/{item}/voice',[LearningPathController::class,'updateVoice'])->name('learning-path.voice');
 Route::get('/certificates/{certificate}',[CertificateController::class,'show'])->name('certificates.show');
 Route::get('/wishlist',[WishlistController::class,'index'])->name('wishlist');
 Route::post('/wishlist/{course}',[WishlistController::class,'toggle'])->name('wishlist.toggle');
 Route::get('/cart',[CartController::class,'index'])->name('cart');
 Route::post('/cart',[CartController::class,'store'])->name('cart.store');
 Route::put('/cart/{cartItem}',[CartController::class,'update'])->name('cart.update');
 Route::delete('/cart/{cartItem}',[CartController::class,'destroy'])->name('cart.destroy');
 Route::get('/checkout',[CheckoutController::class,'index'])->name('checkout');
 Route::post('/checkout',[CheckoutController::class,'store'])->name('checkout.store');
 Route::get('/orders',[OrderController::class,'index'])->name('orders');
 Route::get('/payment',[PaymentController::class,'index'])->name('payment');
 Route::get('/payment/{transaction}',[PaymentController::class,'show'])->name('payment.show');
 Route::post('/payment/{transaction}/pay',[PaymentController::class,'pay'])->name('payment.pay');
 Route::post('/payment/{transaction}/cancel',[PaymentController::class,'cancel'])->name('payment.cancel');
 Route::get('/transactions/{transaction}',[PaymentController::class,'show'])->name('transactions.show');
 Route::post('/transactions/{transaction}/pay',[PaymentController::class,'pay'])->name('transactions.pay');
 Route::post('/transactions/{transaction}/cancel',[PaymentController::class,'cancel'])->name('transactions.cancel');
 Route::get('/my-courses',[MyCourseController::class,'index'])->name('my-courses');
 Route::get('/enrollments/{enrollment}/learn',[LearnController::class,'show'])->name('learn');
});
Route::prefix('mentor')->name('mentor.')->middleware(['auth','role:mentor'])->group(function(){
 Route::get('/dashboard',MentorDashboard::class)->name('dashboard');
 Route::get('/profile',[MentorProfileController::class,'show'])->name('profile');
 Route::put('/profile',[MentorProfileController::class,'update'])->name('profile.update');
 Route::get('/enrollments/{enrollment}/student',[MentorStudentController::class,'show'])->name('students.show');
 Route::get('/reviews',[MentorReviewController::class,'index'])->name('reviews');
 Route::get('/earnings',[MentorEarningsController::class,'index'])->name('earnings');
 Route::get('/schedules',[MentorScheduleController::class,'index'])->name('schedules.index');
 Route::post('/schedules',[MentorScheduleController::class,'store'])->name('schedules.store');
 Route::put('/schedules/{schedule}',[MentorScheduleController::class,'update'])->name('schedules.update');
 Route::delete('/schedules/{schedule}',[MentorScheduleController::class,'destroy'])->name('schedules.destroy');
 Route::post('/schedules/{schedule}/sessions',[MentorScheduleController::class,'generateSessions'])->name('schedules.sessions.store');
 Route::get('/reschedules',[MentorRescheduleController::class,'index'])->name('reschedules.index');
 Route::post('/reschedules/{rescheduleRequest}/approve',[MentorRescheduleController::class,'approve'])->name('reschedules.approve');
 Route::post('/reschedules/{rescheduleRequest}/reject',[MentorRescheduleController::class,'reject'])->name('reschedules.reject');
 Route::post('/reschedules/mark-read',[MentorRescheduleController::class,'markAllRead'])->name('reschedules.mark-read');
 Route::post('/attendance',[AttendanceController::class,'store'])->name('attendance.store');
 Route::post('/exam-attempts',[MentorExamController::class,'storeAttempt'])->name('exam-attempts.store');
});
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function(){
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    // Manajemen Pengguna
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Manajemen Course
    Route::get('/courses', [App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [App\Http\Controllers\Admin\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
    Route::patch('/courses/{course}/toggle-status', [App\Http\Controllers\Admin\CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::delete('/courses/{course}', [App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');

    // Manajemen Pesanan
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{transaction}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Manajemen Ulasan
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Progress Siswa
    Route::get('/students', [App\Http\Controllers\Admin\StudentProgressController::class, 'index'])->name('students.index');

    // Jadwal Pengajar
    Route::get('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedules.store');
    Route::delete('/schedules/{schedule}', [App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Laporan Pendapatan
    Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');

    // Sertifikat
    Route::get('/certificates', [App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'show'])->name('certificates.show');

    // Statistik Platform
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    // Recycle Bin
    Route::get('/recycle-bin', [App\Http\Controllers\Admin\RecycleBinController::class, 'index'])->name('recycle-bin.index');
    Route::patch('/recycle-bin/courses/{course}/restore', [App\Http\Controllers\Admin\RecycleBinController::class, 'restoreCourse'])->name('recycle-bin.restore-course');
    Route::post('/recycle-bin/empty', [App\Http\Controllers\Admin\RecycleBinController::class, 'emptyTrash'])->name('recycle-bin.empty');
});


