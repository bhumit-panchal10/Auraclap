<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\CityMaster;

use App\Models\StateMaster;

use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\ValidationException;



class CityController extends Controller

{

    public function index(Request $request)

    {

        try {

            $SearchState = $request->stateId;

            $states = StateMaster::orderBy('stateName', 'asc')->where(['iStatus' => 1, 'isDelete' => 0])->get();

            $Citylist = CityMaster::select(

                'city-masters.cityId',

                'city-masters.cityName',
                'city-masters.company_percentage',

                'state-masters.stateName'

            )

                ->orderBy('stateName', 'asc')

                ->when($request->stateId, fn($query, $SearchState) => $query

                    ->where('city-masters.stateId', '=', $SearchState))

                ->leftjoin('state-masters', 'city-masters.stateMasterStateId', '=', 'state-masters.stateId')

                ->paginate(config('app.per_page'));



            return view('city_master.index', compact('Citylist', 'states', 'SearchState'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }



    public function store(Request $request)

    {

        DB::beginTransaction();

        try {



            $request->validate([

                'stateMasterStateId' => 'required',

                'company_percentage' => 'required|numeric|between:0,30',

                'cityName' => 'required|unique:city-masters,cityName'


            ]);



            $Data = CityMaster::create([

                'stateMasterStateId' => $request->stateMasterStateId,

                'cityName' => $request->cityName,

                'company_percentage' => $request->company_percentage,

                'created_at' => date('Y-m-d H:i:s'),

                'strIP' => $request->ip(),

            ]);



            DB::commit();



            Toastr::success('City created successfully :)', 'Success');

            return redirect()->route('city.index')->with('success', 'City Created Successfully.');
        } catch (ValidationException $e) {

            DB::rollBack();

            $errors = $e->errors(); // Get the errors array

            $errorMessages = []; // Initialize an array to hold error messages



            // Loop through the errors array and flatten the error messages

            foreach ($errors as $field => $messages) {

                foreach ($messages as $message) {

                    $errorMessages[] = $message;
                }
            }



            // Join all error messages into a single string

            $errorMessageString = implode(', ', $errorMessages);



            Toastr::error($errorMessageString, 'Error');

            return redirect()->back()->withInput();
        } catch (\Throwable $th) {

            DB::rollBack();

            Toastr::error('Failed to create state: ' . $th->getMessage(), 'Error');

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }



    public function edit(Request $request, $id)

    {

        try {

            $data = CityMaster::where(['isDelete' => 0, 'cityId' => $id])->first();


            echo json_encode($data);
        } catch (\Throwable $th) {

            // Rollback and return with Error

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }



    public function update(Request $request)

    {

        DB::beginTransaction();

        try {



            $request->validate([

                'company_percentage' => 'required|numeric|between:0,30',
                'stateMasterStateId' => 'required',
                'cityName' => 'required|unique:city-masters,cityName,' . $request->cityId . ',cityId'


            ]);



            CityMaster::where(['cityId' => $request->cityId])->update([

                'stateMasterStateId' => $request->stateMasterStateId,

                'cityName' => $request->cityName,

                'company_percentage' => $request->company_percentage,

                'updated_at' => date('Y-m-d H:i:s'),

                'strIP' => $request->ip(),

            ]);



            DB::commit();



            Toastr::success('City updated successfully :)', 'Success');

            return back();
        } catch (ValidationException $e) {

            DB::rollBack();

            $errors = $e->errors(); // Get the errors array

            $errorMessages = []; // Initialize an array to hold error messages



            // Loop through the errors array and flatten the error messages

            foreach ($errors as $field => $messages) {

                foreach ($messages as $message) {

                    $errorMessages[] = $message;
                }
            }



            // Join all error messages into a single string

            $errorMessageString = implode(', ', $errorMessages);



            Toastr::error($errorMessageString, 'Error');

            return redirect()->back()->withInput();
        } catch (\Throwable $th) {

            DB::rollBack();

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }



    public function delete(Request $request)

    {

        DB::beginTransaction();



        try {

            CityMaster::where(['iStatus' => 1, 'isDelete' => 0, 'cityId' => $request->id])->delete();



            DB::commit();



            Toastr::success('City deleted successfully :)', 'Success');

            return response()->json(['success' => true]);
        } catch (ValidationException $e) {

            DB::rollBack();

            Toastr::error(implode(', ', $e->errors()));

            return redirect()->back()->withInput();
        } catch (\Throwable $th) {

            DB::rollBack();

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }



    public function deleteselected(Request $request)

    {

        // dd($request->all());

        try {

            $ids = $request->input('city_ids', []);

            CityMaster::whereIn('cityId', $ids)->delete();



            Toastr::success('City deleted successfully :)', 'Success');

            return back();
        } catch (ValidationException $e) {

            DB::rollBack();

            Toastr::error(implode(', ', $e->errors()));

            return redirect()->back()->withInput();
        } catch (\Throwable $th) {

            DB::rollBack();

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }
}
