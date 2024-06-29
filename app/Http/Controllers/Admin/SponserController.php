<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SponserController extends Controller
{
    public function showSponserForm()
    {
        return view("Sponser.sponserForm");
    }

    public function storesponser(Request $request)
    {


        $data = $request->validate([
            'title' => 'required|string',
            'desc' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:available,expired',
            'expired_at' => 'required|date',
        ]);
        $newImage = $request->file('image')->store('sponsers', 'public');
        // Prefix the stored path with 'storage'
        $data['image'] = 'storage/' . $newImage;
        Sponsor::create($data);
        return redirect(url("admin/addSponser"))->with('succsess', "inserted is succsessfuly");
    }

    public function getAllsponser()
    {
        $sponsers = Sponsor::all();
        return view("Sponser.all", compact("sponsers"));
    }

    public function showsponser($id)
    {
        $sponser = Sponsor::findOrFail($id);
        return view("Sponser.showOne", compact("sponser"));
    }
    public function showEditForm($id)
    {
        $sponser = Sponsor::findOrFail($id);
        return view("Sponser.edit", compact("sponser"));
    }
    public function editsponser(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'desc' => 'required|string',
            'image' => 'mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:available,expired',
            'expired_at' => 'required|date',
        ]);
        $sponser = Sponsor::findOrFail($id);
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($sponser->image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $sponser->image));
            }

            // Store the new image and get the path
            $newImage = $request->file('image')->store('sponsers', 'public');

            // Prefix the stored path with 'storage'
            $data['image'] = 'storage/' . $newImage;
        }
        $sponser->update($data);


        return redirect(url("admin/sponser/show/$sponser->id"))->with('succsess', "updated is succsessfuly");;
    }

    public function deletesponser($id)
    {
        $sponser = Sponsor::findOrFail($id);
        if ($sponser->image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $sponser->image));
        }

        $sponser->delete();
        return redirect(url("admin/sponser"))->with('succsess', "deleted is succsessfully");
    }
}
