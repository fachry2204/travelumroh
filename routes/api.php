<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\HomeController;
use App\Http\Controllers\Api\Public\PackageController as PublicPackageController;
use App\Http\Controllers\Api\Public\ArticleController as PublicArticleController;
use App\Http\Controllers\Api\Public\GalleryController as PublicGalleryController;
use App\Http\Controllers\Api\Public\BookingController as PublicBookingController;
use App\Http\Controllers\Api\Public\SitemapController;
use App\Http\Controllers\Api\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Api\Member\BookingController as MemberBookingController;
use App\Http\Controllers\Api\Member\DuitkuController;
use App\Http\Controllers\Api\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\Admin\PilgrimController as AdminPilgrimController;
use App\Http\Controllers\Api\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\AgentController as AdminAgentController;
use App\Http\Controllers\Api\Admin\RepresentativeController as AdminRepresentativeController;
use App\Http\Controllers\Api\Admin\CmsController as AdminCmsController;
use App\Http\Controllers\Api\Admin\DepartureController as AdminDepartureController;
use App\Http\Controllers\Api\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Api\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Api\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Api\ExportController;
use App\Models\Faq;
use App\Models\Testimonial;

// ===================== PUBLIC ROUTES =====================
Route::prefix('public')->group(function () {
    Route::get('/settings', [HomeController::class, 'settings']);
    Route::get('/sitemap.xml', [SitemapController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/packages', [PublicPackageController::class, 'index']);
    Route::get('/packages/{slug}', [PublicPackageController::class, 'show']);
    Route::get('/articles', [PublicArticleController::class, 'index']);
    Route::get('/articles/{slug}', [PublicArticleController::class, 'show']);
    Route::get('/galleries', [PublicGalleryController::class, 'index']);
    Route::get('/pages/{type}', [HomeController::class, 'getPage']);
    Route::get('/faqs', fn() => response()->json(['success' => true, 'data' => Faq::where('status', 'active')->orderBy('sort_order')->get()]));
    Route::get('/testimonials', fn() => response()->json(['success' => true, 'data' => Testimonial::where('status', 'active')->orderBy('sort_order')->get()]));
    Route::post('/register-booking', [PublicBookingController::class, 'store']);
});

// ===================== AUTH ROUTES =====================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// ===================== MEMBER ROUTES =====================
Route::prefix('member')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index']);
    Route::get('/bookings', [MemberBookingController::class, 'index']);
    Route::get('/bookings/{booking}', [MemberBookingController::class, 'show']);
    Route::put('/pilgrims/{pilgrim}', [MemberBookingController::class, 'updatePilgrim']);
    Route::post('/pilgrims/{pilgrim}/documents', [MemberBookingController::class, 'uploadDocument']);
    Route::post('/payments/{bookingId}/upload-proof', [MemberBookingController::class, 'uploadPaymentProof']);

    // Duitku Payment
    Route::post('/duitku/create-invoice', [DuitkuController::class, 'createInvoice']);
});

// Duitku Callback (Unprotected)
Route::post('/duitku/callback', [DuitkuController::class, 'callback']);

// ===================== EXPORT ROUTES =====================
Route::prefix('export')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/invoice/{booking}', [ExportController::class, 'invoice']);
    Route::get('/receipt/{payment}', [ExportController::class, 'receipt']);
    Route::get('/manifest/{departure}', [ExportController::class, 'manifest']);
});

// ===================== AGENT ROUTES =====================
Route::prefix('agent')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index']);
    Route::get('/bookings', [AgentDashboardController::class, 'bookings']);
    Route::get('/commissions', [AgentDashboardController::class, 'commissions']);
});

// ===================== ADMIN ROUTES =====================
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Packages
    Route::apiResource('packages', AdminPackageController::class);
    Route::post('/packages/{package}/image', [AdminPackageController::class, 'uploadImage']);

    // Bookings
    Route::apiResource('bookings', AdminBookingController::class);

    // Pilgrims
    Route::apiResource('pilgrims', AdminPilgrimController::class);

    // Documents
    Route::get('/documents', [AdminDocumentController::class, 'index']);
    Route::post('/documents/{document}/validate', [AdminDocumentController::class, 'validate']);
    Route::post('/documents/pilgrims/{pilgrim}', [AdminDocumentController::class, 'uploadForPilgrim']);

    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index']);
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show']);
    Route::post('/payments', [AdminPaymentController::class, 'store']);
    Route::post('/payments/{payment}/validate', [AdminPaymentController::class, 'validate']);

    // Agents & Commissions
    Route::get('/agents', [AdminAgentController::class, 'index']);
    Route::post('/agents', [AdminAgentController::class, 'store']);
    Route::put('/agents/{agent}', [AdminAgentController::class, 'update']);
    Route::get('/commissions', [AdminAgentController::class, 'commissions']);
    Route::post('/commissions/{commission}/pay', [AdminAgentController::class, 'payCommission']);

    // Representatives
    Route::get('/representatives', [AdminRepresentativeController::class, 'index']);
    Route::post('/representatives', [AdminRepresentativeController::class, 'store']);
    Route::put('/representatives/{representative}', [AdminRepresentativeController::class, 'update']);
    Route::delete('/representatives/{representative}', [AdminRepresentativeController::class, 'destroy']);

    // Departures
    Route::apiResource('departures', AdminDepartureController::class);

    // Reports
    Route::get('/reports/sales', [AdminReportController::class, 'sales']);
    Route::get('/reports/commissions', [AdminReportController::class, 'commissions']);
    Route::post('/reports/custom', [AdminReportController::class, 'custom']);

    // CMS
    Route::get('/cms/articles', [AdminCmsController::class, 'articles']);
    Route::post('/cms/articles', [AdminCmsController::class, 'storeArticle']);
    Route::put('/cms/articles/{article}', [AdminCmsController::class, 'updateArticle']);
    Route::delete('/cms/articles/{article}', [AdminCmsController::class, 'destroyArticle']);
    Route::post('/cms/articles/upload-image', [AdminCmsController::class, 'uploadArticleImage']);
    Route::post('/cms/hero-image', [AdminCmsController::class, 'uploadHeroImage']);

    Route::get('/cms/galleries', [AdminCmsController::class, 'galleries']);
    Route::post('/cms/galleries', [AdminCmsController::class, 'storeGallery']);
    Route::put('/cms/galleries/update-album', [AdminCmsController::class, 'updateAlbum']);
    Route::post('/cms/galleries/add-photos', [AdminCmsController::class, 'addPhotosToAlbum']);
    Route::post('/cms/galleries/reorder', [AdminCmsController::class, 'reorderGalleries']);
    Route::post('/cms/galleries/destroy-album', [AdminCmsController::class, 'destroyAlbum']);
    Route::delete('/cms/galleries/{gallery}', [AdminCmsController::class, 'destroyGallery']);

    Route::get('/cms/faqs', [AdminCmsController::class, 'faqs']);
    Route::post('/cms/faqs', [AdminCmsController::class, 'storeFaq']);
    Route::put('/cms/faqs/{faq}', [AdminCmsController::class, 'updateFaq']);
    Route::delete('/cms/faqs/{faq}', [AdminCmsController::class, 'destroyFaq']);

    Route::get('/cms/pages', [AdminCmsController::class, 'pages']);
    Route::put('/cms/pages/{type}', [AdminCmsController::class, 'updatePage']);

    Route::get('/cms/testimonials', [AdminCmsController::class, 'testimonials']);
    Route::post('/cms/testimonials', [AdminCmsController::class, 'storeTestimonial']);
    Route::post('/cms/testimonials/{testimonial}', [AdminCmsController::class, 'updateTestimonial']);
    Route::delete('/cms/testimonials/{testimonial}', [AdminCmsController::class, 'destroyTestimonial']);

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index']);
    Route::post('/settings', [AdminSettingController::class, 'update']);

    // Audit Logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index']);
});
