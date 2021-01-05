<?php

namespace App\Http\Controllers;

use App\PageManage;
use Illuminate\Http\Request;

class PageManageController extends Controller
{
    public function termCondition()
    {
        $data = PageManage::select('terms_condition')->get()->first();
        if ($data) return $data;
        else {
            $insert = new PageManage();
            $insert->terms_condition = '';
            return $insert->save();
        }
    }

    public function termConditionUpdate(Request $request)
    {
        $insert = PageManage::all()->first();
        $insert->terms_condition = $request->terms_condition;
        $insert->save();
    }

    public function privacyPolicy()
    {
        $data = PageManage::select('privacy')->get()->first();
        if ($data) return $data;
        else {
            $insert = new PageManage();
            $insert->terms_condition = '';
            return $insert->save();
        }
    }

    public function privacyPolicyUpdate(Request $request)
    {
        $insert = PageManage::all()->first();
        $insert->privacy = $request->privacy;
        $insert->save();
    }

    public function aboutUs()
    {
        $data = PageManage::select('about_us')->get()->first();
        if ($data) return $data;
        else {
            $insert = new PageManage();
            $insert->terms_condition = '';
            return $insert->save();
        }
    }

    public function aboutUsUpdate(Request $request)
    {
        $insert = PageManage::all()->first();
        $insert->about_us = $request->about_us;
        $insert->save();
    }

    public function joinSales()
    {
        $data = PageManage::select('join_sales')->get()->first();
        if ($data) return $data;
        else {
            $insert = new PageManage();
            $insert->terms_condition = '';
            return $insert->save();
        }
    }

    public function joinSalesUpdate(Request $request)
    {
        $insert = PageManage::all()->first();
        $insert->join_sales = $request->join_sales;
        $insert->save();
    }
}
