<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MetaData;

class MetaDataController extends Controller
{
    public function index()
    {
        try{

        $data = MetaData::paginate();
        return view('metaData.index', compact('data'));
        } catch (\Exception $e) {
           report($e);
         return false;
        }
    }

    public function edit($id)
    {
        try{
        
        $data = MetaData::where('id',$id)->first();
        return view('metaData.edit', compact('data'));
        } catch (\Exception $e) {
           report($e);
         return false;
        }
    }

    public function update(Request $request, $id)
    {
        try{
        $updateData = array(
            'pagename' => $request->pagename,
            'metaTitle' => $request->metaTitle,
            'metaKeyword' => $request->metaKeyword,
            'metaDescription' => $request->metaDescription,
            'head' => $request->head,
            'body' => $request->body,
            // 'h1tag' => $request->h1tag,
            // 'h1taggrey' => $request->h1taggrey,
        );
        MetaData::where('id',$id)->update($updateData);
       // $data = MetaData::whereId($id)->get();

        return redirect()->route('metaData.index')->with('success', 'Data Updated Successfully');
        } catch (\Exception $e) {
           report($e);
         return false;
        }
    }
     public function view(Request $request, $id)
    {
        try{
        $datas = MetaData::where(['id' => $id])->first();

        return view('metaData.view',compact('datas'));
        } catch (\Exception $e) {
        report($e);
        return false;
        }
    }


}
