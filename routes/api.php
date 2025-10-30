<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\TechnicialApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\FrontCustomerApiController;
use App\Http\Controllers\Api\OpenAIImageController;
use App\Http\Controllers\Api\FrontApiController;
use App\Http\Controllers\Api\CmsApiController;
use Illuminate\Support\Facades\Artisan;
use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Mail;


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    return 'Cache is cleared';
});

Route::get('/test-whatsapp', function () {
    $controller = new \App\Models\Customer;
    return $controller->WhatsappMessage('7486984607', 'Hello from Laravel!');
});


//vendor api
Route::post('/new-registration', [TechnicialApiController::class, 'vendor_new_registration'])->name('vendor_new_registration');
Route::post('/login', [TechnicialApiController::class, 'login']);
Route::post('/Technicial/change/password', [TechnicialApiController::class, 'change_password'])->name('change_password');
Route::post('/generate-image', [OpenAIImageController::class, 'generateImage'])->name('generateImage');
Route::post('/Technicial/forgot/password', [TechnicialApiController::class, 'forgot_password'])->name('forgot_password');
Route::post('/Technicial/forgot/password/verifyOTP', [TechnicialApiController::class, 'forgot_password_verifyOTP'])->name('forgot_password_verifyOTP');
Route::post('/logout', [TechnicialApiController::class, 'logout']);
Route::post('/Technicial/profile', [TechnicialApiController::class, 'profiledetails'])->name('profiledetails');
Route::post('/Technicial/profile/update', [TechnicialApiController::class, 'profileUpdate'])->name('profileUpdate');
Route::post('/Technicial/Dashboard', [TechnicialApiController::class, 'technicialdashboard'])->name('technicialdashboard');

Route::post('/Technicial/Technicial_startwork_photoUpload', [TechnicialApiController::class, 'Technicial_startwork_photoUpload'])->name('Technicial_startwork_photoUpload');
Route::post('/Technicial/RefreshfirebaseDeviceToken', [TechnicialApiController::class, 'RefreshfirebaseDeviceToken'])->name('RefreshfirebaseDeviceToken');

Route::post('/Technicial/Technicial_endwork_photoUpload', [TechnicialApiController::class, 'Technicial_endwork_photoUpload'])->name('Technicial_endwork_photoUpload');

Route::post('/Technicial/Technicial_endverifyOTP', [TechnicialApiController::class, 'Technicial_endverifyOTP'])->name('Technicial_endverifyOTP');
Route::post('/Technicial/available_order_list', [TechnicialApiController::class, 'available_or_complete_order_list'])->name('available_or_complete_order_list');
Route::post('/Technicial/wallet_load', [TechnicialApiController::class, 'wallet_load'])->name('wallet_load');
Route::post('/Technicial/paymentstatus', [TechnicialApiController::class, 'paymentstatus'])->name('paymentstatus');
Route::post('/Technicial/claimOrder', [TechnicialApiController::class, 'claimOrder'])->name('claimOrder');
Route::get('/states', [TechnicialApiController::class, 'statelist'])->name('statelist');
//12-06-2025
Route::post('/Technicial/ledger/list', [TechnicialApiController::class, 'ledger_list']);
Route::post('/Technicial/video/list', [TechnicialApiController::class, 'video_list']);
Route::post('/Add/By/Technicial/service', [TechnicialApiController::class, 'add_by_tech_service'])->name('add_by_tech_service');


//Customer api
Route::post('/customer-registration', [CustomerApiController::class, 'customer_new_registration'])->name('customer_new_registration');

Route::post('/customerlogin', [CustomerApiController::class, 'login']);
Route::post('/customer/RefreshfirebaseDeviceToken', [CustomerApiController::class, 'RefreshfirebaseDeviceToken'])->name('RefreshfirebaseDeviceToken');

Route::post('/customerverifyOTP', [CustomerApiController::class, 'verifyOTP'])->name('verifyOTP');
Route::post('/customer/category', [CustomerApiController::class, 'categories'])->name('categories');
Route::post('/customer/HomeSliderImage', [CustomerApiController::class, 'HomeSliderImage'])->name('HomeSliderImage');
Route::post('/customer/TechnicialSliderImage', [CustomerApiController::class, 'TechnicialSliderImage'])->name('TechnicialSliderImage');

Route::post('/customer/subcategory/rate', [CustomerApiController::class, 'subcat_or_rate'])->name('subcat_or_rate');
Route::post('/customer/AddCart', [CustomerApiController::class, 'AddCart'])->name('AddCart');
Route::post('/customer/ViewCart', [CustomerApiController::class, 'ViewCart'])->name('ViewCart');
Route::post('/customer/qtyupdate', [CustomerApiController::class, 'qtyupdate'])->name('qtyupdate');
Route::post('/customer/Removeqty_fromcart', [CustomerApiController::class, 'Removeqty_fromcart'])->name('Removeqty_fromcart');

Route::post('/customer/Remove_cart', [CustomerApiController::class, 'Remove_cart']);

Route::post('/customer/offerlist', [CustomerApiController::class, 'offerlist'])->name('offerlist');
Route::post('/customer/ratecardlist', [CustomerApiController::class, 'ratecardlist'])->name('ratecardlist');

Route::post('/customer/cartdetail', [CustomerApiController::class, 'cartdetail'])->name('cartdetail');
Route::post('/customer/order', [CustomerApiController::class, 'order'])->name('order');
Route::post('/customer/remove_category_form_cart', [CustomerApiController::class, 'remove_category_form_cart'])->name('remove_category_form_cart');
Route::post('/offer/apply', [CustomerApiController::class, 'offer_apply'])->name('offer_apply');
Route::post('/paymentstatus', [CustomerApiController::class, 'paymentstatus'])->name('paymentstatus');
Route::post('/Timeslot', [CustomerApiController::class, 'Timeslot'])->name('Timeslot');
Route::post('/faqlist', [CustomerApiController::class, 'faqlist'])->name('faqlist');

Route::post('/notificationlist', [CustomerApiController::class, 'notificationlist'])->name('notificationlist');

Route::post('/customer/state', [CustomerApiController::class, 'state'])->name('state');
Route::post('/customer/cityupdate', [CustomerApiController::class, 'cityupdate'])->name('cityupdate');


Route::post('/customer/category_subcategory', [CustomerApiController::class, 'category_subcategory'])->name('category_subcategory');
Route::post('/customer/cat_sub_detail', [CustomerApiController::class, 'cat_sub_detail'])->name('cat_sub_detail');

Route::post('/customer/city', [CustomerApiController::class, 'city'])->name('city');
Route::post('/customer/Area', [CustomerApiController::class, 'Area'])->name('Area');

Route::post('/customer/ongoingorder', [CustomerApiController::class, 'ongoingorder'])->name('ongoingorder');
Route::post('/customer/orderHistory', [CustomerApiController::class, 'orderHistory'])->name('orderHistory');
Route::post('/customer/completeorder', [CustomerApiController::class, 'completeorder'])->name('completeorder');
Route::post('/customer/cancelorder', [CustomerApiController::class, 'cancelorder'])->name('cancelorder');
Route::post('/customer/cancelorderlist', [CustomerApiController::class, 'cancelorderlist']);


Route::post('/customer/profiledetails', [CustomerApiController::class, 'profiledetails'])->name('profiledetails');
Route::post('/customer/profile/update', [CustomerApiController::class, 'profileUpdate'])->name('profileUpdate');
Route::post('/customer/startotp', [CustomerApiController::class, 'startotp'])->name('startotp');
Route::post('/customer/endotp', [CustomerApiController::class, 'endotp'])->name('endotp');
Route::post('/customer/rating', [CustomerApiController::class, 'rating'])->name('rating');

Route::post('/customer/pdfgenerate', [CustomerApiController::class, 'pdfgenerate'])->name('pdfgenerate');


Route::post('/customer/cms', [CmsApiController::class, 'cms'])->name('cms');

Route::post('/customer/forgot/password', [CustomerApiController::class, 'forgot_password'])->name('forgot_password');
Route::post('/customer/forgot/password/verifyOTP', [CustomerApiController::class, 'forgot_password_verifyOTP'])->name('forgot_password_verifyOTP');
Route::post('/customerlogout', [CustomerApiController::class, 'logout']);
Route::post('/customer/profile', [CustomerApiController::class, 'profiledetails'])->name('profiledetails');
Route::post('/customer/profile/update', [CustomerApiController::class, 'profileUpdate'])->name('profileUpdate');
Route::post('/customer/Recruitment', [CustomerApiController::class, 'Recruitment'])->name('Recruitment');

Route::post('/Add_Address', [CustomerApiController::class, 'Add_Address'])->name('Add_Address');
Route::post('/Addresslist', [CustomerApiController::class, 'Addresslist'])->name('Addresslist');
Route::get('/Booking/Cancel/list', [CustomerApiController::class, 'bookingcancellist'])->name('bookingcancellist');

Route::post('/customer/front_category_subcategory', [FrontCustomerApiController::class, 'front_category_subcategory'])->name('front_category_subcategory');
Route::post('/customer/subcat', [FrontCustomerApiController::class, 'subcat'])->name('subcat');
Route::post('/JoinAsTechnicial', [FrontCustomerApiController::class, 'JoinAsTechnicial'])->name('JoinAsTechnicial');
Route::post('/sendcontact_mail', [FrontCustomerApiController::class, 'sendcontact_mail'])->name('sendcontact_mail');
Route::post('/order_checkout', [FrontCustomerApiController::class, 'order_checkout'])->name('order_checkout');
Route::post('/payment_status', [FrontCustomerApiController::class, 'payment_status'])->name('payment_status');
Route::post('/blogs', [FrontCustomerApiController::class, 'blogs'])->name('blogs');
Route::post('/blog/details', [FrontCustomerApiController::class, 'blog_details'])->name('blog_details');

//for seo
Route::post('/homelist', [FrontCustomerApiController::class, 'homelist'])->name('homelist');
Route::post('/aboutlist', [FrontCustomerApiController::class, 'aboutlist'])->name('aboutlist');
Route::post('/contactlist', [FrontCustomerApiController::class, 'contactlist'])->name('contactlist');
Route::post('/serviceslist', [FrontCustomerApiController::class, 'serviceslist'])->name('serviceslist');
Route::post('/existinguser', [FrontCustomerApiController::class, 'existinguser'])->name('existinguser');
Route::post('/regenerate_cod_otp', [FrontCustomerApiController::class, 'regenerate_cod_otp'])->name('regenerate_cod_otp');
Route::post('/verifyotp', [FrontCustomerApiController::class, 'verifyotp']);
Route::post('/orderdetail', [FrontCustomerApiController::class, 'orderdetail'])->name('orderdetail');


Route::get('/authorize_payment/{orderid?}', [FrontCustomerApiController::class, 'authorize_payment'])->name('authorize.payment');

Route::get('/testroute', function () {
    $name = "Funny Coder";

    // The email sending is done using the to method on the Mail facade
    Mail::to('dev2.apolloinfotech@gmail.com')->send(new MyTestEmail($name));
});


Route::get('/run-scheduled-notifications', function () {
    Artisan::call('send:scheduled-notifications');
    return 'Scheduled ride notifications command has been executed.';
});
