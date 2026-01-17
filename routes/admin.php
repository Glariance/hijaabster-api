<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CMSController;
use App\Http\Controllers\Admin\ContactInquiryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\BundleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'adminLogin'])->name('adminLogin');

    Route::get('/forgot-password', [LoginController::class, 'forgot'])->name('forgot');
    Route::post('/forgot-password', [LoginController::class, 'forgotpost'])->name('forgotpost');

    Route::get('/reset-password', [LoginController::class, 'resetPassword'])->name('reset.password');
    Route::post('/reset-password', [LoginController::class, 'resetPasswordPost'])->name('reset.password.post');
});

// Route::middleware(['auth', 'role:' . config('constants.ADMIN')])->group(function () {
Route::middleware(['auth', 'role:'.config('constants.ADMIN')])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('settings');

        Route::get('/profile/page', [SettingController::class, 'profilePage'])->name('profile.page');
        Route::post('/profile/page', [SettingController::class, 'profilePost'])->name('profile.post');

        Route::get('/change-password/page', [SettingController::class, 'changePasswordPage'])->name('changepassword.page');
        Route::post('/change-password/page', [SettingController::class, 'changePasswordPost'])->name('changepassword.post');

        Route::get('/smtp/page', [SettingController::class, 'smtpPage'])->name('smtp.page');
        Route::post('/smtp/page', [SettingController::class, 'smtpPost'])->name('smtp.post');


        Route::get('/general/page', [SettingController::class, 'generalPage'])->name('general.page');
        Route::put('/general/page', [SettingController::class, 'generalPost'])->name('general.post');
        Route::delete('/general/page/{id}', [SettingController::class, 'generaldelete'])->name('general.delete');
    });
    Route::prefix('cms')->name('cms.')->group(function () {

        // CMS Pages Routes
        Route::prefix('page')->group(function () {
            Route::get('/create', [CMSController::class, 'pageCreate'])->name('page.create');
            Route::post('/create', [CMSController::class, 'pagePost'])->name('page.post');
            Route::delete('/delete/{id}', [CMSController::class, 'pageDelete'])->name('page.delete');
        });

        // CMS Sections Routes
        Route::prefix('section')->group(function () {
            Route::get('/create', [CMSController::class, 'sectionCreate'])->name('section.create');
            Route::post('/create', [CMSController::class, 'sectionPost'])->name('section.post');
            Route::delete('/delete/{id}', [CMSController::class, 'sectionDelete'])->name('section.delete');

            // Section Fields Routes
            Route::post('/image/upload', [CMSController::class, 'uploadImage'])->name('image.upload');
            Route::prefix('{sectionId}/fields')->group(function () {
                Route::get('/', [CMSController::class, 'sectionFieldIndex'])->name('section.fields');
                Route::post('/store', [CMSController::class, 'sectionFieldStore'])->name('section.field.store');
                Route::post('/group/store', [CMSController::class, 'sectionGroupFieldStore'])->name('section.group.store');
                Route::get('/group/copy', [CMSController::class, 'sectionGroupFieldCopy'])->name('section.group.copy');
                Route::post('/group/delete', [CMSController::class, 'sectionGroupFieldDelete'])->name('section.group.delete');
                Route::get('/group', [CMSController::class, 'addFieldsInGroup'])->name('section.addFieldsInGroup');
                Route::post('/group/field/store', [CMSController::class, 'addFieldsInGroupPost'])->name('section.group.field.post');
            });
            Route::delete('/field/delete/{id}', [CMSController::class, 'sectionFieldDestroy'])->name('section.field.delete');
        });
        // Bind Slug Route for CMS Pages
        Route::get('/{slug}', [CMSController::class, 'index'])->name('index');
    });

    Route::get('newsletter-management/send-mail', [NewsLetterController::class, 'sendMailView'])->name('newsletter-management.send-mail-view');
    Route::post('newsletter-management/send-mail', [NewsLetterController::class, 'sendMail'])->name('newsletter-management.send-mail');
    Route::resource('newsletter-management', NewsLetterController::class);

    Route::resource('contact-inquiry', ContactInquiryController::class);
    Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);

    Route::resource('tags', TagController::class);
    Route::resource('blogs', BlogController::class);
    Route::delete('blogs/destroyMedia/{media}', [BlogController::class, 'destroyMedia'])->name('blogs.destroyMedia');
    Route::delete('blogs/destroyComment/{comment}', [BlogController::class, 'destroyComment'])->name('blogs.destroyComment');
    Route::get('blogs/updateCommentStatus/{comment}', [BlogController::class, 'updateCommentStatus'])->name('blogs.updateCommentStatus');
    // dd('$roles');


    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::resource('category', CategoryController::class);
        Route::resource('brand', BrandController::class);
        Route::resource('attributes', AttributeController::class);
        Route::resource('product', ProductController::class);
    });

    Route::resource('coupon', CouponController::class);
    Route::resource('bundle', BundleController::class);















    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});



Route::get('/mailable-preview', function () {
    return new \App\Mail\UniversalMailable(
        'Test Subject',
        [
            'message' => "<p>Dear Subscribers,</p>

    <p>We hope this message finds you well.</p>

    <p>Thank you for subscribing to <strong>Erin Website</strong>. We&#39;re excited to have you with us!<br />
    Our team is working hard behind the scenes to bring you exciting updates, helpful resources, and exclusive content.<br />
    Stay tuned for more announcements and new features launching soon.</p>

    <p>If you have any questions or suggestions, feel free to reach out to our support team.</p>

    <p>Best regards,<br />
    <strong>Erin Website Team</strong></p>",
        ],
        'admin.emails.send-newsletter'
    );
});








  // Route::prefix('cms')->name('cms.')->group(function () {

    //     Route::get('/page/create', [CMSController::class, 'pageCreate'])->name('page.create');
    //     Route::post('/page/create', [CMSController::class, 'pagePost'])->name('page.post');
    //     Route::delete('/page/create/{id}', [CMSController::class, 'pageDelete'])->name('page.delete');

    //     Route::get('/section/create', [CMSController::class, 'sectionCreate'])->name('section.create');
    //     Route::post('/section/create', [CMSController::class, 'sectionPost'])->name('section.post');
    //     Route::delete('/section/create/{id}', [CMSController::class, 'sectionDelete'])->name('section.delete');

    //     // bind here

    //     Route::get('/{slug}', [CMSController::class, 'index'])->name('index');
    // });
