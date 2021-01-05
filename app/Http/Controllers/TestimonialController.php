<?php

namespace App\Http\Controllers;

use App\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{

    public function index()
    {
        return DB::table('testimonials')->select('id', 'name', 'profession', 'message', 'video', 'status')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'profession' => 'required|max:100',
            'message' => 'required',
        ]);

        return Testimonial::create($request->all());
    }

    public function statusUpdate(Request $request)
    {
        if ($request->status == 1) {
            Testimonial::where('status', 1)->update(['status' => 0]);
        }
        $insert = Testimonial::findOrFail($request->id);
        $insert->status = $request->status;
        $insert->save();
        return $request->id;
    }

    public function destroy($id)
    {
        Testimonial::findOrFail($id)->delete();
        return response()->json(['result' => 'Success', 'message' => 'Testimonial has been deleted'], 200);
    }
}
