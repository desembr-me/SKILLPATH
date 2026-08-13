<?php

use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminPlatformStatisticsController;
use App\Http\Controllers\Admin\AdminCertificateController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInstructorController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminTeachingScheduleController;
use App\Http\Controllers\Admin\AdminRevenueReportController;
use App\Http\Controllers\Admin\AdminRecycleBinController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseQuestionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HowItWorksController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\InstructorCourseController;
use App\Http\Controllers\InstructorDashboardController;
use App\Http\Controllers\InstructorScheduleController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/kategori/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/kelas', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/kelas/{learningPath}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/pengajar', [InstructorController::class, 'index'])->name('instructors.index');
Route::get('/pengajar/{instructor}', [InstructorController::class, 'show'])->name('instructors.show');
Route::get('/cara-kerja', HowItWorksController::class)->name('how-it-works');
Route::get('/untuk-orang-tua', ParentController::class)->name('parents');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::get('/kelas', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::patch('/kelas/{learningPath}/publikasi', [AdminCourseController::class, 'togglePublish'])->name('courses.toggle-publish');
    Route::delete('/kelas/{learningPath}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('/pengajar', [AdminInstructorController::class, 'index'])->name('instructors.index');
    Route::patch('/pengajar/{instructor}/verifikasi', [AdminInstructorController::class, 'toggleVerify'])->name('instructors.toggle-verify');
    Route::delete('/pengajar/{instructor}', [AdminInstructorController::class, 'destroy'])->name('instructors.destroy');

    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/kehadiran-peserta', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/kehadiran-peserta/export', [AdminAttendanceController::class, 'export'])->name('attendance.export');
    Route::get('/kehadiran-peserta/{childProfile}', [AdminAttendanceController::class, 'show'])->name('attendance.show');

    Route::get('/jadwal-kelas', [AdminTeachingScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/jadwal-kelas/export', [AdminTeachingScheduleController::class, 'export'])->name('schedules.export');
    Route::get('/jadwal-kelas/tambah', [AdminTeachingScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/jadwal-kelas', [AdminTeachingScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/jadwal-kelas/{classSession}/edit', [AdminTeachingScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/jadwal-kelas/{classSession}', [AdminTeachingScheduleController::class, 'update'])->name('schedules.update');
    Route::patch('/jadwal-kelas/{classSession}/batalkan', [AdminTeachingScheduleController::class, 'cancel'])->name('schedules.cancel');

    Route::get('/laporan-pendapatan', [AdminRevenueReportController::class, 'index'])->name('revenue.index');
    Route::get('/laporan-pendapatan/export', [AdminRevenueReportController::class, 'export'])->name('revenue.export');

    Route::get('/sertifikat', [AdminCertificateController::class, 'index'])->name('certificates.index');
    Route::get('/sertifikat/export', [AdminCertificateController::class, 'export'])->name('certificates.export');
    Route::get('/sertifikat/terbitkan', [AdminCertificateController::class, 'create'])->name('certificates.create');
    Route::post('/sertifikat', [AdminCertificateController::class, 'store'])->name('certificates.store');
    Route::get('/sertifikat/{certificate}', [AdminCertificateController::class, 'show'])->name('certificates.show');
    Route::patch('/sertifikat/{certificate}/cabut', [AdminCertificateController::class, 'revoke'])->name('certificates.revoke');
    Route::patch('/sertifikat/{certificate}/aktifkan', [AdminCertificateController::class, 'reactivate'])->name('certificates.reactivate');

    Route::get('/statistik-platform', [AdminPlatformStatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistik-platform/export', [AdminPlatformStatisticsController::class, 'export'])->name('statistics.export');

    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/pesanan/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('/kategori', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/kategori', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/review', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/review/{courseReview}/moderasi', [AdminReviewController::class, 'toggleApprove'])->name('reviews.toggle-approve');
    Route::delete('/review/{courseReview}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/recycle-bin', [AdminRecycleBinController::class, 'index'])->name('recycle-bin.index');
    Route::patch('/recycle-bin/restore-all', [AdminRecycleBinController::class, 'restoreAll'])->name('recycle-bin.restore-all');
    Route::patch('/recycle-bin/{type}/{id}/restore', [AdminRecycleBinController::class, 'restore'])->name('recycle-bin.restore');
    Route::delete('/recycle-bin/{type}/{id}', [AdminRecycleBinController::class, 'forceDelete'])->name('recycle-bin.force-delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'edit'])->name('onboarding.edit');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::post('/kelas/{learningPath}/daftar-gratis', [CourseController::class, 'enrollFree'])->name('courses.enroll-free');
    Route::post('/kelas/{learningPath}/wishlist', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{learningPath}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/keranjang/{learningPath}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/bayar-demo', [CheckoutController::class, 'pay'])->name('orders.pay-demo');

    Route::get('/kelas-saya', [MyCourseController::class, 'index'])->name('my-courses.index');
    Route::get('/jadwal-kelas', [ClassScheduleController::class, 'index'])->name('class-schedules.index');
    Route::get('/jadwal-kelas/{classSession}', [ClassScheduleController::class, 'show'])->name('class-schedules.show');
    Route::post('/jadwal-kelas/{classSession}/booking', [ClassScheduleController::class, 'book'])->name('class-schedules.book');
    Route::patch('/jadwal-kelas/{classSession}/batalkan', [ClassScheduleController::class, 'cancel'])->name('class-schedules.cancel');

    Route::post('/kelas/{learningPath}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/kelas/{learningPath}/pertanyaan', [CourseQuestionController::class, 'store'])->name('questions.store');
    Route::get('/sertifikat/{learningPath}', [CertificateController::class, 'show'])->name('certificates.show');

    Route::get('/pengajar-dashboard', InstructorDashboardController::class)->name('instructor.dashboard');
    Route::get('/pengajar-dashboard/kelas/{learningPath}/edit', [InstructorCourseController::class, 'edit'])->name('instructor.courses.edit');
    Route::put('/pengajar-dashboard/kelas/{learningPath}', [InstructorCourseController::class, 'update'])->name('instructor.courses.update');
    Route::post('/pengajar-dashboard/kelas/{learningPath}/jadwal', [InstructorScheduleController::class, 'store'])->name('instructor.schedules.store');
    Route::get('/pengajar-dashboard/jadwal/{classSession}', [InstructorScheduleController::class, 'show'])->name('instructor.schedules.show');
    Route::patch('/pengajar-dashboard/jadwal/{classSession}/selesai', [InstructorScheduleController::class, 'complete'])->name('instructor.schedules.complete');
    Route::patch('/pengajar-dashboard/jadwal/{classSession}/booking/{sessionBooking}', [InstructorScheduleController::class, 'updateAttendance'])->name('instructor.schedules.attendance');
    Route::delete('/pengajar-dashboard/jadwal/{classSession}', [InstructorScheduleController::class, 'destroy'])->name('instructor.schedules.destroy');
    Route::post('/pengajar-dashboard/pertanyaan/{courseQuestion}/jawab', [CourseQuestionController::class, 'answer'])->name('instructor.questions.answer');
});
