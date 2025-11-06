<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\TechnicialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SubCategoriesController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\CMSController;
use App\Http\Controllers\HowItWorkController;
use App\Http\Controllers\ManageHotelHostelsController;
use App\Http\Controllers\ManageRateController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\ExamUserController;
use App\Http\Controllers\ManageExamUserController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TechnicialSliderController;
use App\Http\Controllers\OrderlistController;
use App\Http\Controllers\BookingCancelController;
use App\Http\Controllers\VideoListController;
use App\Http\Controllers\MetaDataController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactInquiryController;

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('admin.php', function () {
        return view('dashboard.home');
    });
    Route::get('admin.php', function () {
        return view('dashboard.home');
    });
});

Auth::routes();
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    return 'Cache is cleared';
});

Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/pendingorderlist', [OrderlistController::class, 'pendingorderlist'])->name('pendingorderlist');
Route::get('/ongoingorderlist', [OrderlistController::class, 'ongoingorderlist'])->name('ongoingorderlist');
Route::get('/completeorderlist', [OrderlistController::class, 'completeorderlist'])->name('completeorderlist');
Route::get('/cancelorder/list', [OrderlistController::class, 'cancelorderlist'])->name('cancelorderlist');
Route::get('/RefundPaymentflag/{orderid?}', [OrderlistController::class, 'RefundPaymentflag'])->name('RefundPaymentflag');


Route::get('/admin/login', [LoginController::class, 'login'])->name('admin.login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/logout', [LoginController::class, 'logoutPage'])->name('logout_page');
Route::get('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::get('/user/login', [UserController::class, 'userlogin'])->name('userlogin');
Route::post('/loginstore', [UserController::class, 'loginstore'])->name('loginstore');
Route::get('/manage_exams_user', [ExamController::class, 'index'])->name('manage_exams_user.index');
Route::get('/exam/results/{exam_id}/{Exam_user_id}', [ExamController::class, 'showResults'])->name('exam.results');
Route::post('/logout', [ExamController::class, 'logout'])->name('logout');
//Route::post('/user/logout', [ExamController::class, 'logout'])->name('user.logout');

Route::get('/exam/{examid?}/{examdetailid?}', [ExamController::class, 'showQuestion'])->name('exam.question');
Route::post('/exam/next', [ExamController::class, 'nextQuestion'])->name('exam.next');
Route::post('/exam/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
Route::get('/exam/result', [ExamController::class, 'showResult'])->name('exam.result');
Route::post('/exam/final-submit', [ExamController::class, 'finalSubmit'])->name('exam.final.submit');




Route::get('/logout', function () {
    session()->forget('exam_user');
    return redirect()->route('userlogin')->with('success', 'Logged out successfully');
})->name('logout');


Route::get('/technicialregister', [TechnicialController::class, 'register'])->name('register');
Route::post('/technicialregisterstore', [TechnicialController::class, 'registerstore'])->name('registerstore');
Route::get('/JoinAsTechniciallist', [TechnicialController::class, 'JoinAsTechniciallist'])->name('JoinAsTechniciallist');
Route::get('/OnboardTechniciallist', [TechnicialController::class, 'OnboardTechniciallist'])->name('OnboardTechniciallist');
Route::get('/technicialonboard/{id?}', [TechnicialController::class, 'technicialonboard'])->name('technicialonboard');
Route::get('/technicialpincode/{id?}', [TechnicialController::class, 'technicialpincode'])->name('technicialpincode');
Route::post('/techaddpincodeadd/{id?}', [TechnicialController::class, 'techadd_pincodeadd'])->name('techadd_pincodeadd');
Route::post('/technicial/tecadd_serviceadd/{id?}', [TechnicialController::class, 'techadd_serviceadd'])->name('techadd_serviceadd');
Route::get('/get-pincodes', [TechnicialController::class, 'getPincodesByStateCity'])->name('get.pincodes');
Route::delete('/technicialdelete', [TechnicialController::class, 'delete'])->name('technicialdelete');
Route::post('/technicialpayment/store', [TechnicialController::class, 'store'])->name('technicialpayment.store');
Route::get('/technicialledger/{id?}', [TechnicialController::class, 'technicialledger'])->name('technicialledger');
Route::get('/OnboardTechnicial/add', [TechnicialController::class, 'onboardtechnicialadd'])->name('onboardtechnicialadd');
Route::get('/OnboardTechnicial/edit/{id?}', [TechnicialController::class, 'edit'])->name('onboardedit');
Route::post('/OnboardTechnicial/update', [TechnicialController::class, 'update'])->name('onboardupdate');
Route::delete('/OnboardTechnicial/delete', [TechnicialController::class, 'OnboardTechdelete'])->name('techonboard.delete');


// Dashboard routes
Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/profile', [HomeController::class, 'getProfile'])->middleware('auth')->name('profile');
Route::post('/updateprofile', [HomeController::class, 'updateProfile'])->middleware('auth')->name('updateprofile');
Route::get('/Changepassword', [HomeController::class, 'changePassword'])->middleware('auth')->name('Changepassword');
Route::post('/Change_password', [HomeController::class, 'changePassword_update'])->middleware('auth')->name('Change_password');

//State Master
Route::prefix('admin')->name('state.')->middleware('auth')->group(function () {
    Route::get('/state/index', [StateController::class, 'index'])->name('index');
    Route::post('/state/store', [StateController::class, 'store'])->name('store');
    Route::get('/state/edit/{id?}', [StateController::class, 'edit'])->name('edit');
    Route::post('/state/update', [StateController::class, 'update'])->name('update');
    Route::delete('/state/delete', [StateController::class, 'delete'])->name('delete');
    Route::delete('/state/deleteselected', [StateController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/state/updateStatus', [StateController::class, 'updateStatus'])->name('updateStatus');
});

//City Master
Route::prefix('admin')->name('city.')->middleware('auth')->group(function () {
    Route::get('/city/index', [CityController::class, 'index'])->name('index');
    Route::post('/city/store', [CityController::class, 'store'])->name('store');
    Route::get('/city/edit/{id?}', [CityController::class, 'edit'])->name('edit');
    Route::post('/city/update', [CityController::class, 'update'])->name('update');
    Route::delete('/city/delete', [CityController::class, 'delete'])->name('delete');
    Route::delete('/city/deleteselected', [CityController::class, 'deleteselected'])->name('deleteselected');
});

//Area Master
Route::prefix('admin')->name('area.')->middleware('auth')->group(function () {
    Route::any('/area/index', [AreaController::class, 'index'])->name('index');
    Route::any('/state/city/mapping', [AreaController::class, 'state_city_mapping'])->name('state_city_mapping');
    Route::get('/area/add', [AreaController::class, 'add'])->name('add');
    Route::post('/area/store', [AreaController::class, 'store'])->name('store');
    Route::get('/area/edit/{id?}', [AreaController::class, 'edit'])->name('edit');
    Route::post('/area/update/{id?}', [AreaController::class, 'update'])->name('update');
    Route::delete('/area/delete', [AreaController::class, 'delete'])->name('delete');
    Route::delete('/area/deleteselected', [AreaController::class, 'deleteselected'])->name('deleteselected');
});

//categories Master
Route::prefix('admin')->name('categories.')->middleware('auth')->group(function () {
    Route::any('/categories/index', [CategoriesController::class, 'index'])->name('index');
    Route::get('/categories/add', [CategoriesController::class, 'add'])->name('add');
    Route::post('/categories/store', [CategoriesController::class, 'store'])->name('store');
    Route::get('/categories/edit/{id?}', [CategoriesController::class, 'edit'])->name('edit');
    Route::post('/categories/update/{id?}', [CategoriesController::class, 'update'])->name('update');
    Route::delete('/categories/delete', [CategoriesController::class, 'delete'])->name('delete');
    Route::delete('/categories/deleteselected', [CategoriesController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/categories/updateStatus', [CategoriesController::class, 'updateStatus'])->name('updateStatus');
});


Route::prefix('admin')->name('blog.')->middleware(['auth'])->group(function () {
    Route::get('/blog/index', [BlogController::class, 'index'])->name('index');
    Route::get('/blog/add', [BlogController::class, 'createview'])->name('add');
    Route::post('/blog/store', [BlogController::class, 'store'])->name('store');
    Route::get('/blog/edit/{id?}', [BlogController::class, 'editview'])->name('edit');
    Route::post('/blog/update/{id?}', [BlogController::class, 'update'])->name('update');
    Route::delete('/blog/delete', [BlogController::class, 'delete'])->name('delete');
    Route::delete('/blog/deleteselected', [BlogController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/blog/updateStatus', [BlogController::class, 'updateStatus'])->name('updateStatus');
});

Route::prefix('admin')->name('sub_categories.')->middleware('auth')->group(function () {
    Route::any('/sub_categories/index', [SubCategoriesController::class, 'index'])->name('index');
    Route::get('/sub_categories/add', [SubCategoriesController::class, 'add'])->name('add');
    Route::post('/sub_categories/store', [SubCategoriesController::class, 'store'])->name('store');
    Route::get('/sub_categories/edit/{id?}', [SubCategoriesController::class, 'edit'])->name('edit');
    Route::post('/sub_categories/update/{id?}', [SubCategoriesController::class, 'update'])->name('update');
    Route::delete('/sub_categories/delete', [SubCategoriesController::class, 'delete'])->name('delete');
    Route::delete('/sub_categories/deleteselected', [SubCategoriesController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/sub_categories/updateStatus', [SubCategoriesController::class, 'updateStatus'])->name('updateStatus');
    Route::any('/sub_categories/service_subservice_mapping', [SubCategoriesController::class, 'service_subservice_mapping'])->name('service_subservice_mapping');
});

Route::prefix('admin')->name('howitworks.')->middleware('auth')->group(function () {
    Route::any('/howitworks/index', [HowItWorkController::class, 'index'])->name('index');
    Route::get('/howitworks/add', [HowItWorkController::class, 'add'])->name('add');
    Route::post('/howitworks/store', [HowItWorkController::class, 'store'])->name('store');
    Route::get('/howitworks/edit/{id?}', [HowItWorkController::class, 'edit'])->name('edit');
    Route::post('/howitworks/update/{id?}', [HowItWorkController::class, 'update'])->name('update');
    Route::delete('/howitworks/delete', [HowItWorkController::class, 'delete'])->name('delete');
    Route::delete('/howitworks/deleteselected', [HowItWorkController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/howitworks/updateStatus', [HowItWorkController::class, 'updateStatus'])->name('updateStatus');
    Route::any('/howitworks/category_subcategory_mapping', [HowItWorkController::class, 'category_subcategory_mapping'])->name('category_subcategory_mapping');
});

Route::prefix('admin')->name('manage_hotel_hostels.')->middleware('auth')->group(function () {
    Route::any('/manage_hotel_hostels/index', [ManageHotelHostelsController::class, 'index'])->name('index');
    Route::get('/manage_hotel_hostels/add', [ManageHotelHostelsController::class, 'add'])->name('add');
    Route::post('/manage_hotel_hostels/store', [ManageHotelHostelsController::class, 'store'])->name('store');
    Route::get('/manage_hotel_hostels/edit/{id?}', [ManageHotelHostelsController::class, 'edit'])->name('edit');
    Route::post('/manage_hotel_hostels/update/{id?}', [ManageHotelHostelsController::class, 'update'])->name('update');
    Route::delete('/manage_hotel_hostels/delete', [ManageHotelHostelsController::class, 'delete'])->name('delete');
    Route::delete('/manage_hotel_hostels/deleteselected', [ManageHotelHostelsController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/manage_hotel_hostels/updateStatus', [ManageHotelHostelsController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/manage_hotel_hostels/Technicialmailsend/{id?}', [ManageHotelHostelsController::class, 'Technicialmailsend'])->name('Technicialmailsend');
});

Route::prefix('admin')->name('manage_rate.')->middleware('auth')->group(function () {
    Route::any('/manage_rate/index', [ManageRateController::class, 'index'])->name('index');
    Route::get('/manage_rate/add', [ManageRateController::class, 'add'])->name('add');
    Route::post('/manage_rate/store', [ManageRateController::class, 'store'])->name('store');
    Route::get('/manage_rate/edit/{id?}', [ManageRateController::class, 'edit'])->name('edit');
    Route::post('/manage_rate/update/{id?}', [ManageRateController::class, 'update'])->name('update');
    Route::delete('/manage_rate/delete', [ManageRateController::class, 'delete'])->name('delete');
    Route::delete('/manage_rate/deleteselected', [ManageRateController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/manage_rate/updateStatus', [ManageRateController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/manage_rate/category_subcategory_mapping', [ManageRateController::class, 'category_subcategory_mapping'])->name('category_subcategory_mapping');
});

Route::prefix('admin')->name('exam_master.')->middleware('auth')->group(function () {
    Route::any('/exam_master/index', [ExamUserController::class, 'index'])->name('index');
    Route::get('/exam_master/add', [ExamUserController::class, 'add'])->name('add');
    Route::post('/exam_master/store', [ExamUserController::class, 'store'])->name('store');
    Route::get('/exam_master/edit/{id?}', [ExamUserController::class, 'edit'])->name('edit');
    Route::post('/exam_master/update/{id?}', [ExamUserController::class, 'update'])->name('update');
    Route::delete('/exam_master/delete', [ExamUserController::class, 'delete'])->name('delete');
    Route::delete('/exam_master/deleteselected', [ExamUserController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/exam_master/category_subcategory_mapping', [ExamUserController::class, 'category_subcategory_mapping'])->name('category_subcategory_mapping');

    Route::get('/exam_detail/add/{id?}', [ExamUserController::class, 'questionadd'])->name('questionadd');
    Route::post('/exam_master/questionstore/{id?}', [ExamUserController::class, 'questionstore'])->name('questionstore');
    Route::get('/exam_detail/list/{id?}', [ExamUserController::class, 'questionpaperlist'])->name('questionpaperlist');
    Route::get('/exam_detail/edit/{id?}/{examid?}', [ExamUserController::class, 'questionedit'])->name('questionedit');
    Route::post('/exam_detail/update/{id?}', [ExamUserController::class, 'questionupdate'])->name('questionupdate');
    Route::delete('/exam_detail/delete', [ExamUserController::class, 'questiondelete'])->name('questiondelete');
    Route::delete('/exam_detail/deleteselected', [ExamUserController::class, 'questiondeleteselected'])->name('questiondeleteselected');
});

Route::prefix('admin')->name('pincode.')->middleware('auth')->group(function () {
    Route::any('/pincode/index', [PincodeController::class, 'index'])->name('index');
    Route::get('/pincode/add', [PincodeController::class, 'add'])->name('add');
    Route::post('/pincode/store', [PincodeController::class, 'store'])->name('store');
    Route::get('/pincode/edit/{id?}', [PincodeController::class, 'edit'])->name('edit');
    Route::post('/pincode/update/{id?}', [PincodeController::class, 'update'])->name('update');
    Route::delete('/pincode/delete', [PincodeController::class, 'delete'])->name('delete');
    Route::delete('/pincode/deleteselected', [PincodeController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/pincode/updateStatus', [PincodeController::class, 'updateStatus'])->name('updateStatus');
    Route::any('/pincode/category_subcategory_mapping', [PincodeController::class, 'category_subcategory_mapping'])->name('category_subcategory_mapping');
});

//Manage Exam User
Route::prefix('admin')->name('manage_exam_user.')->middleware('auth')->group(function () {
    Route::any('/manage_exam_user/index', [ManageExamUserController::class, 'index'])->name('index');
    Route::get('/manage_exam_user/add', [ManageExamUserController::class, 'add'])->name('add');
    Route::post('/manage_exam_user/store', [ManageExamUserController::class, 'store'])->name('store');
    Route::get('/manage_exam_user/edit/{id?}', [ManageExamUserController::class, 'edit'])->name('edit');
    Route::post('/manage_exam_user/update/{id?}', [ManageExamUserController::class, 'update'])->name('update');
    Route::delete('/manage_exam_user/delete', [ManageExamUserController::class, 'delete'])->name('delete');
    Route::delete('/manage_exam_user/deleteselected', [ManageExamUserController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/manage_exam_user/updateStatus', [ManageExamUserController::class, 'updateStatus'])->name('updateStatus');
});

//Faq Master
Route::prefix('admin')->name('faq.')->middleware('auth')->group(function () {
    Route::get('/faq/index', [FaqController::class, 'index'])->name('index');
    Route::get('/faq/add', [FaqController::class, 'add'])->name('add');
    Route::post('/faq/store', [FaqController::class, 'store'])->name('store');
    Route::get('/faq/edit/{id?}', [FaqController::class, 'edit'])->name('edit');
    Route::get('/faq/view/{id?}', [FaqController::class, 'view'])->name('view');
    Route::post('/faq/update/{id?}', [FaqController::class, 'update'])->name('update');
    Route::delete('/faq/delete', [FaqController::class, 'delete'])->name('delete');
    Route::delete('/faq/deleteselected', [FaqController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/faq/updateStatus', [FaqController::class, 'updateStatus'])->name('updateStatus');
});

Route::prefix('admin')->name('BookingCancelReason.')->middleware('auth')->group(function () {
    Route::get('/BookingCancelReason/index', [BookingCancelController::class, 'index'])->name('index');
    Route::get('/BookingCancelReason/add', [BookingCancelController::class, 'add'])->name('add');
    Route::post('/BookingCancelReason/store', [BookingCancelController::class, 'store'])->name('store');
    Route::get('/BookingCancelReason/edit/{id?}', [BookingCancelController::class, 'edit'])->name('edit');
    Route::post('/BookingCancelReason/update/{id?}', [BookingCancelController::class, 'update'])->name('update');
    Route::delete('/BookingCancelReason/delete', [BookingCancelController::class, 'delete'])->name('delete');
    Route::delete('/BookingCancelReason/deleteselected', [BookingCancelController::class, 'deleteselected'])->name('deleteselected');
});

Route::prefix('admin')->name('VideoList.')->middleware('auth')->group(function () {
    Route::get('/VideoList/index', [VideoListController::class, 'index'])->name('index');
    Route::get('/VideoList/add', [VideoListController::class, 'add'])->name('add');
    Route::post('/VideoList/store', [VideoListController::class, 'store'])->name('store');
    Route::get('/VideoList/edit/{id?}', [VideoListController::class, 'edit'])->name('edit');
    Route::post('/VideoList/update/{id?}', [VideoListController::class, 'update'])->name('update');
    Route::delete('/VideoList/delete', [VideoListController::class, 'delete'])->name('delete');
    Route::delete('/VideoList/deleteselected', [VideoListController::class, 'deleteselected'])->name('deleteselected');
});

Route::prefix('admin')->name('slider.')->middleware('auth')->group(function () {
    Route::any('/slider/index', [SliderController::class, 'index'])->name('index');
    Route::get('/slider/add', [SliderController::class, 'add'])->name('add');
    Route::post('/slider/store', [SliderController::class, 'store'])->name('store');
    Route::get('/slider/edit/{id?}', [SliderController::class, 'edit'])->name('edit');
    Route::post('/slider/update', [SliderController::class, 'update'])->name('update');
    Route::delete('/slider/delete', [SliderController::class, 'delete'])->name('delete');
    Route::delete('/slider/deleteselected', [SliderController::class, 'deleteselected'])->name('deleteselected');
});

Route::prefix('admin')->name('Technicialslider.')->middleware('auth')->group(function () {
    Route::any('/Technicial/index', [TechnicialSliderController::class, 'index'])->name('index');
    Route::get('/Technicial/add', [TechnicialSliderController::class, 'add'])->name('add');
    Route::post('/Technicial/store', [TechnicialSliderController::class, 'store'])->name('store');
    Route::get('/Technicial/edit/{id?}', [TechnicialSliderController::class, 'edit'])->name('edit');
    Route::post('/Technicial/update', [TechnicialSliderController::class, 'update'])->name('update');
    Route::delete('/Technicial/delete', [TechnicialSliderController::class, 'delete'])->name('delete');
    Route::delete('/Technicial/deleteselected', [TechnicialSliderController::class, 'deleteselected'])->name('deleteselected');
});

//Career Master

Route::prefix('admin')->name('metaData.')->middleware('auth')->group(function () {
    Route::get('/seo/index', [MetaDataController::class, 'index'])->name('index');
    Route::get('seo/{id}/edit', [MetaDataController::class, 'edit'])->name('edit');
    Route::post('seo/{id}', [MetaDataController::class, 'update'])->name('update');
    Route::get('/seo/view/{id?}', [MetaDataController::class, 'view'])->name('view');
});

//CMS
Route::prefix('admin')->name('cms.')->middleware('auth')->group(function () {
    Route::get('/cms/index', [CMSController::class, 'index'])->name('index');
    Route::get('/cms/edit/{id?}', [CMSController::class, 'edit'])->name('edit');
    Route::post('/cms/update/{id?}', [CMSController::class, 'update'])->name('update');
});

Route::prefix('/admin')->name('offer.')->middleware('auth')->group(function () {
    Route::get('/offer/index', [OfferController::class, 'index'])->name('index');
    Route::get('/offer/create', [OfferController::class, 'create'])->name('create');
    Route::post('/offer/store', [OfferController::class, 'store'])->name('store');
    Route::get('/offer/edit/{id?}', [OfferController::class, 'edit'])->name('edit');
    Route::post('/offer/update/{id?}', [OfferController::class, 'update'])->name('update');
    Route::delete('/offer/delete', [OfferController::class, 'delete'])->name('delete');
    Route::delete('/offer/deleteselected', [OfferController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/offer/updateStatus', [OfferController::class, 'updateStatus'])->name('updateStatus');

    Route::get('/offer/view/{id?}', [OfferController::class, 'view'])->name('view');
});


Route::prefix('/admin')->name('Recruitment.')->middleware('auth')->group(function () {
    Route::get('/Recruitment/index', [RecruitmentController::class, 'index'])->name('index');
    Route::get('/Recruitment/create', [RecruitmentController::class, 'create'])->name('create');
    Route::post('/Recruitment/store', [RecruitmentController::class, 'store'])->name('store');
    Route::get('/Recruitment/edit/{id?}', [RecruitmentController::class, 'edit'])->name('edit');
    Route::post('/Recruitment/update/{id?}', [RecruitmentController::class, 'update'])->name('update');
    Route::delete('/Recruitment/delete', [RecruitmentController::class, 'delete'])->name('delete');
    Route::delete('/Recruitment/deleteselected', [RecruitmentController::class, 'deleteselected'])->name('deleteselected');
    Route::any('/Recruitment/updateStatus', [RecruitmentController::class, 'updateStatus'])->name('updateStatus');
});

Route::prefix('/admin')->name('ContactInquiry.')->middleware('auth')->group(function () {
    Route::get('/ContactInquiry/index', [ContactInquiryController::class, 'index'])->name('index');
    Route::delete('/ContactInquiry/delete', [ContactInquiryController::class, 'delete'])->name('delete');
    Route::delete('/ContactInquiry/deleteselected', [ContactInquiryController::class, 'deleteselected'])->name('deleteselected');
});
