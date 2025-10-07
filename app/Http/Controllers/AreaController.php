<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\CityMaster;

use App\Models\StateMaster;

use App\Models\AreaMaster;

// use App\Models\PriceMaster;

use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\ValidationException;



class AreaController extends Controller

{

    public function index(Request $request)

    {

        try {

            $StateId = $request->stateId;
            $CityId = $request->cityId;
            $AreaName = $request->area_name;


            $states = StateMaster::orderBy('stateName', 'asc')->get();

            $cities = CityMaster::orderBy('cityName', 'asc')->get();

            // $prices = PriceMaster::orderBy('priceId', 'asc')->get();



            $arealist = AreaMaster::select(

                'area-masters.*',

                'state-masters.stateId',

                'state-masters.stateName',

                'city-masters.cityId',

                'city-masters.cityName',



            )

                ->orderBy('areaName', 'asc')

                ->where(['area-masters.iStatus' => 1, 'area-masters.isDelete' => 0])

                ->when(
                    $request->area_name,
                    fn($query, $AreaName) =>
                    $query->where(function ($q) use ($AreaName) {
                        $q->where('area-masters.areaName', 'like', '%' . $AreaName . '%')
                            ->orWhere('area-masters.areaPincode', 'like', '%' . $AreaName . '%');
                    })
                )
                ->when($request->stateId, fn($query, $StateId) => $query->where('area-masters.areastateId', $StateId))
                ->when($request->cityId, fn($query, $CityId) => $query->where('area-masters.areacityId', $CityId))

                ->leftjoin('state-masters', 'area-masters.areastateId', '=', 'state-masters.stateId')

                ->leftjoin('city-masters', 'area-masters.areacityId', '=', 'city-masters.cityId')



                ->paginate(config('app.per_page'));



            return view('area_master.index', compact('states', 'cities', 'arealist', 'StateId', 'CityId', 'AreaName'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }



    public function state_city_mapping(Request $request)

    {

        $stateId = $request->state;



        if ($stateId) {

            $cities =  CityMaster::orderBy('cityName', 'asc')->where(['stateMasterStateId' => $stateId])->get();

            // dd($cities);

            if ($cities) {

                //     $html = "";

                //     $html .= "<option value='0'>No data found !</option>";

                //     return $html;

                // } else {

                $html = "";

                $html .= "<option value=''>Select City</option>";

                foreach ($cities as $city) {

                    $html .= "<option value='" . $city->cityId . "'>" . $city->cityName . "</option>";
                }



                return $html;
            }
        }
    }

    public function add()
    {
        try {
            $states = StateMaster::orderBy('stateName', 'asc')->get();
            $cities = CityMaster::orderBy('cityName', 'asc')->get();
            // $prices = PriceMaster::orderBy('priceId', 'asc')->get();

            return view('area_master.add', compact('states', 'cities'));
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

                'areaName' => 'required',



            ]);

            $area = AreaMaster::create([

                'areaName' => $request->areaName,

                'areaPincode' => $request->areaPincode,

                //'priceId' => $request->priceId,

                'areastateId' => $request->areastateId ?? 0,

                'areacityId' => $request->areacityId ?? 0,

                'pickupstarttime' => $request->pickupstarttime,

                'pickupendtime' => $request->pickupendtime,

                'created_at' => date('Y-m-d H:i:s'),

                'strIP' => $request->ip(),

            ]);
            DB::commit();
            Toastr::success('Area created successfully :)', 'Success');

            return redirect()->route('area.index');
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

            Toastr::error('Failed to create area: ' . $th->getMessage(), 'Error');

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }



    public function edit(Request $request, $id)

    {

        try {
            $states = StateMaster::orderBy('stateName', 'asc')->get();
            $cities = CityMaster::orderBy('cityName', 'asc')->get();
            // $prices = PriceMaster::orderBy('priceId', 'asc')->get();

            $data = AreaMaster::where(['iStatus' => 1, 'isDelete' => 0, 'areaId' => $id])->first();

            // echo json_encode($data);
            return view('area_master.edit', compact('states', 'cities', 'data'));
        } catch (\Throwable $th) {

            // Rollback and return with Error

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }



    public function update(Request $request, $id)

    {

        DB::beginTransaction();

        try {



            $request->validate([

                'areaName' => 'required',



            ]);



            AreaMaster::where(['areaId' => $id])->update([

                'areaName' => $request->areaName,



                'areastateId' => $request->areastateId ?? 0,

                'areacityId' => $request->areacityId ?? 0,


                'updated_at' => date('Y-m-d H:i:s'),

                'strIP' => $request->ip(),

            ]);



            DB::commit();



            Toastr::success('Area updated successfully :)', 'Success');

            // return back();
            return redirect()->route('area.index');
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

            AreaMaster::where(['iStatus' => 1, 'isDelete' => 0, 'areaId' => $request->id])->delete();



            DB::commit();



            Toastr::success('Area deleted successfully :)', 'Success');

            //return back();

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



        try {

            $ids = $request->input('area_ids', []);

            AreaMaster::whereIn('areaId', $ids)->delete();



            Toastr::success('Area deleted successfully :)', 'Success');

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
