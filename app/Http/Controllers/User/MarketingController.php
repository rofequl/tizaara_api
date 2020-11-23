<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
    }

    public function quotation()
    {
        $collection = new Collection();
        $quotation = DB::table('quotations')->where('user_id', 'like', '%' . Auth::user()->id . '%')->get();
        foreach ($quotation as $quotations) {
            $user = json_decode($quotations->user_id);
            if (in_array(Auth::user()->id, $user, true)) {
                $collection->push([
                    'email' => $quotations->email,
                    'product' => $quotations->product,
                    'quantity' => $quotations->quantity . ' ' . DB::table('units')->where('id', $quotations->unit_id)->value('name'),
                ]);
            }
        }
        return $collection;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
        //
    }

    public function destroy($id)
    {
        //
    }
}
