<?php

namespace App\Http\Controllers;

use App\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index()
    {
        $collection = new Collection();
        $quotation = DB::table('quotations')->get();
        foreach ($quotation as $quotations) {
            //$user = DB::table('users')->find($quotations->unit_id);
            $collection->push([
                'id' => $quotations->id,
                'email' => $quotations->email,
                'product' => $quotations->product,
                'status' => $quotations->status,
                'quantity' => $quotations->quantity . ' ' . DB::table('units')->where('id', $quotations->unit_id)->value('name'),
            ]);
        }
        return $collection;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $insert = Quotation::create([
            'product' => $request->product,
            'email' => $request->email,
            'quantity' => $request->quantity,
            'unit_id' => $request->unit,
        ]);
        return 'done';
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'email' => 'required',
            'user_id' => 'required',
            'user_id.*' => 'required',
        ],
            [
                'user_id.required' => 'Please select the supplier first',
            ]);
        $insert = Quotation::findOrFail($id);
        $insert->user_id = json_encode($request->user_id);
        $insert->status = 1;
        $insert->save();
        return 'done';
    }

    public function destroy($id)
    {
        return Quotation::findOrFail($id)->delete();
    }
}
