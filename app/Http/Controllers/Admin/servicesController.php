<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class servicesController extends Controller
{
    public function showServicesForm()
    {
        return view("Services.ServicesForm");
    }
    public function storeServices(Request $request)
    {


      $data=$request->validate([
        "name"=>"required|string",
        "desc"=>"required|string",
        "image"=>"required|image|mimes:jpg,png,jpeg,gif",
      ]);
      $data['image']=Storage::putFile("services",$data['image']);
       Services::create($data);
      return redirect(url("admin/addServices"))->with('succsess',"inserted is succsessfuly");
    }

    public function getAllServices()
    {
        $services=Services::all();
        return view("Services.all",compact("services"));
    }
    public function showServices($id)
    {
        $service=Services::findOrFail($id);
        return view("Services.showOne",compact("service"));
    }
    public function showEditForm($id)
    {
        $service=Services::findOrFail($id);
        return view("Services.edit",compact("service"));
    }
    public function editServices(Request $request,$id)
    {
        $data=$request->validate([
            "name"=>"required|string",
            "desc"=>"required|string",
            "image"=>"image|mimes:jpg,png",
          ]);
          $service=Services::findOrFail($id);
          if($request->has('image'))
          {
            Storage::delete($service->image);
            $data['image']=Storage::putFile("services",$data['image']);
          }
          $service->update($data);
          return redirect(url("admin/services/show/$service->id"));
    }
    public function deleteServices($id)
    {
        $service=Services::findOrFail($id);
        Storage::delete($service->image);
        $service->delete();
        return redirect(url("admin/services"))->with('succsess',"deleted is succsessfully");
    }
}
