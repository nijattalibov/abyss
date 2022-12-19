<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use Validator;
use File;
use \Storage;
use Response;

class PersonController extends Controller
{
    public function index()
    {
        $persons = Person::select('id','name','description','type')->orderBy('created_at', 'DESC')->simplePaginate(10);
        return response()->json(['success'=>TRUE,'message'=>$persons],200);
    }

    public function detail($id)
    {
        $person = Person::select('name','description','type')->where('id', $id)->first();
        if($person==NULL){
            return response()->json(['success'=>FALSE,'message'=>'Does not exist'],200);
        }
       
        $url = Storage::temporaryUrl(
            $person->name, now()->addMinutes(1),[$id]
        );
        return response()->json(['success'=>TRUE,'message'=>$person,'file'=>$url],200);
    }

    public function get_image($id)
    {
        if(!request()->hasValidSignature()){
            return response()->json(['success'=>FALSE,'message'=>'Signature expired'],200);
        }
        
        $person = Person::select('name','description','type','file')->where('id', $id)->first();
        if($person==NULL){
            return response()->json(['success'=>FALSE,'message'=>'Does not exist'],200);
        }
        
        $headers = array(
            'Content-Type:image/jpeg',
        );
        $name=$person->file;
        $file = public_path() . "\uploads\\" . "person\\".$person->file;

        return Response::download($file, $name, $headers);
    }

    public function create()
    {
        $data = request()->only('name','description','file','type');
        $validator = Validator::make($data, [
            'name'=>'unique:persons,name|required|max:50',
            'description'=>'nullable|required|max:250',
            'type' => 'required|integer|between:1,3',
            'file'=>'nullable|image|mimes:jpg,png,jpeg,gif|max:5120'
        ]);
      
        if ($validator->fails()) {
            return response()->json(['success'=>FALSE,'message'=>$validator->errors()], 200);
        }
        
        //Download file
        if(request()->hasFile('file')){
            $file=request()->file('file');
            $file_name=request('name')."-".time().".".$file->extension();
            $data['file']=$file_name;
            if($file->isValid()){
                $file->move('uploads/person', $file_name);
            }
        }

        $created=Person::create($data);
        $person = Person::select('name','description','type')->where('id', $created["id"])->first();
        return response()->json(['success'=>TRUE,'message'=>$person],200);
    }

    public function update($id)
    {
        $find = Person::where('id', $id)->first();
        if($find==NULL){
            return response()->json(['success'=>FALSE,'message'=>'Does not exist'],200);
        }

        $data = request()->only('name','description','file','type');
        $validator = Validator::make($data, [
            'name'=>(request('name') != $find->name ? 'unique:persons,name|max:50' : 'max:50'),
            'description'=>'nullable|required|max:250',
            'type' => 'required|integer|between:1,3',
            'file'=>'nullable|image|mimes:jpg,png,jpeg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>FALSE,'message'=>$validator->errors()], 200);
        }

        //Download file
        if(request()->hasFile('file')){
            $file=request()->file('file');
            $file_name=request('name')."-".time().".".$file->extension();
            $data['file']=$file_name;
            if($file->isValid()){
                $file->move('uploads/person', $file_name);
            }
        }
        else{
            $data['file']=$find->file;
        }

        $find->update($data);
        $person = Person::select('name','description','type')->where('id', $id)->first();
        return response()->json(['success'=>TRUE,'message'=>$person],200);
    }

    public function delete($id)
    {
        $find = Person::where('id', $id)->first();
        if ($find == null) {
            return response()->json(['success'=>FALSE,'message'=>'Does not exist'],200);
        }
        
        $find->delete();
        return response()->json(['success'=>True,'message'=>'Deleted'],200);
    }
    
}
