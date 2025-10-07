<?php

namespace App\Http\Controllers;

use App\Models\CareerMaster;
use App\Models\CityMaster;
use App\Models\FaqMaster;
use App\Models\JobCategoryMaster;
use App\Models\NewsUpdate;
use App\Models\OurTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\StateMaster;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\TagMaster;
use App\Models\Vehicle;
use App\Models\PassMaster;
use App\Models\Driver;
use App\Models\AreaMaster;
use App\Models\PriceMaster;
use App\Models\CMSMaster;
use App\Models\GoodsType;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use App\Models\MetaData;
use App\Models\Customer;
use App\Models\NewsLetters;
use App\Models\Career;
use App\Models\subcategory;
use App\Models\Categories;
use App\Models\ContactUs;
use App\Models\SubCategories;
use App\Models\Vendor;
use App\Models\Offer;
use App\Models\Deals;




class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $states = StateMaster::orderBy('stateId', 'asc')->count();
        $cities = CityMaster::orderBy('cityId', 'asc')->count();


        $AreaMaster = AreaMaster::orderBy('areaId', 'asc')->count();


        $Categories = Categories::orderBy('Categories_id', 'asc')->count();
        $subcategory = SubCategories::orderBy('iSubCategoryId', 'asc')->count();
        $FaqMaster = FaqMaster::orderBy('id', 'asc')->count();
        $CMSMaster = CMSMaster::orderBy('id', 'asc')->count();
        $promocode = Offer::orderBy('id', 'asc')->count();
        // $deal = Deals::orderBy('id', 'asc')->count();

        return view('dashboard.home', compact('Categories', 'FaqMaster', 'CMSMaster', 'subcategory', 'states', 'cities', 'AreaMaster', 'promocode'));
    }

    public function getProfile()
    {
        $sessionrole = Auth::user();
        $session = Auth::user()->id;
        $users = User::where('users.id',  $session)->first();
        // dd($users);

        return view('profile', compact('users'));
    }

    public function updateProfile(Request $request)
    {

        // dd($request); 
        $request->validate([

            'editmobile' => ['required', 'digits:10']


        ]);

        $session = Auth::user()->id;
        $users = User::where('id', '=', $session)
            //  ->where('email', 'admin@admin.com')
            ->first();
        //  dd($users);

        $data = array(
            "first_name" => $request->editfirstname,
            "last_name" => $request->editlastname,
            "email" => $request->editemail,
            "address" => $request->editaddress,
            "mobile_number" => $request->editmobile
        );
        // dd($data);
        //9028187696
        $user = User::where('users.id',  $session)->update($data);
        if ($user) {
            Toastr::success('User proflie updated successfully :)', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Something went wrong :)', 'Error');
            return redirect()->back();
        }
    }

    public function changePassword(Request $request)
    {
        return view('dashboard.Changepassword');
    }

    public function changePassword_update(Request $request)
    {
        DB::beginTransaction();
        try {
            $newPassword = $request->newPassword;
            $confirmPassword = $request->confirmPassword;

            $users = User::where('id',  $request->id)->first();

            if (!Hash::check($request->oldPassword, $users->password)) {
                Toastr::error('Old password does not match');
                return redirect()->back()->withInput();
            }

            if ($newPassword === $confirmPassword) {

                $users->password = Hash::make($request->newPassword);
                $users->save();

                DB::commit();

                Toastr::success('Password changed successfully');
                return redirect()->route('logout');
            } else {
                Toastr::error('new password and confirm password does not match');
                return redirect()->back()->withInput();
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            Toastr::error('Validation Error: ' . implode(', ', $e->errors()));
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
