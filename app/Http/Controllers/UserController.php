<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\ManageExamUser;

use App\Models\Role;

use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\ValidationException;

use App\Models\User;

use Carbon\Carbon;

use Illuminate\Support\Facades\Hash;



class UserController extends Controller

{

    /**

     * Display a listing of the resource.

     */
    public function userlogin()
    {

        try {
            return view('user.userlogin');
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function loginstore(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required'
        ]);

        // Find user by login_id
        $user = ManageExamUser::with('category')->where('login_id', $request->login_id)->first();
        //dd($user);
        if (!$user) {
            return back()->withErrors(['login_id' => 'Invalid login ID']);
        }

        // Check if password matches
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password']);
        }
        session(['exam_user' => [
            'Exam_User_id' => $user->Exam_User_id,
            'login_id' => $user->login_id, // Assuming login_id is the username
            'name' => $user->name,
            'cate_id' => $user->category->Category_name,
            'exam_id' => $user->exam_id

        ]]);


        // Store session and redirect to dashboard
        session()->flash('success', 'Login successful!');
        return redirect()->route('manage_exams_user.index');
    }
}
