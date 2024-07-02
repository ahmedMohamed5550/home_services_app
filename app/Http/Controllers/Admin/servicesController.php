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
        $data = $request->validate([
            "name" => "required|string",
            "desc" => "required|string",
            "image" => "required|image|mimes:jpg,png,jpeg,gif",
        ]);
        // Store the image and get the path
        $newImage = $request->file('image')->store('services_folder', 'public');
        // Prefix the stored path with 'storage'
        $data['image'] = 'storage/' . $newImage;
        Services::create($data);
        return redirect(url("admin/addServices"))->with('succsess', "inserted is succsessfuly");
    }

    public function getAllServices()
    {
        $services = Services::all();
        return view("Services.all", compact("services"));
    }
    public function showServices($id)
    {
        $service = Services::findOrFail($id);
        return view("Services.showOne", compact("service"));
    }
    public function showEditForm($id)
    {
        $service = Services::findOrFail($id);
        return view("Services.edit", compact("service"));
    }
    public function editServices(Request $request, $id)
    {
        $data = $request->validate([
            "name" => "required|string",
            "desc" => "required|string",
            "image" => "image|mimes:jpg,png",
        ]);
        $service = Services::findOrFail($id);
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($service->image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $service->image));
            }

            // Store the new image and get the path
            $newImage = $request->file('image')->store('services_folder', 'public');

            // Prefix the stored path with 'storage'
            $data['image'] = 'storage/' . $newImage;
        }
        $service->update($data);
        return redirect(url("admin/services/show/$service->id"))->with('succsess', "Service updating successfully");
    }
    public function deleteServices($id)
    {
        $service = Services::findOrFail($id);

        // Delete the associated image if it exists
        if ($service->image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $service->image));
        }

        $service->delete();

        return redirect(url("admin/services"))->with('succsess', "Service deleted successfully");
    }
}
