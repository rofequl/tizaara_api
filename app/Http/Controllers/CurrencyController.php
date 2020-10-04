<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'code', 'symbol', 'exchange_rate', 'status'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        if ($length == null && $column == null && $dir == null && $searchValue == null) {
            return Currency::select('id', 'name', 'symbol')->where('status', 1)->orderBy('id', 'DESc')->get();
        }
        $query = Currency::orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('code', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->latest()->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:currencies',
            'symbol' => 'required|max:255',
        ]);

        return Currency::create($request->all());
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
            'name' => 'required|max:255|unique:currencies,name,' . $id,
            'symbol' => 'required|max:255',
        ]);

        return Currency::findOrFail($id)->update($request->all());
    }

    public function updateStatus($id, $id2)
    {
        $currency = Currency::findOrFail($id);
        $currency->status = $id2;
        return $currency->save();
    }

    public function destroy($id)
    {
        //
    }
}
