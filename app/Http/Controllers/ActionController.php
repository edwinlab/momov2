<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Momo;


class ActionController extends Controller
{
    public function smsAction(Request $request)
    {
        $message =  $request->input('msg');
        $momo = new Momo('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkZXZpY2VfaWQiOiI0MzI0MzI0MzI0MzI0MjQiLCJleHBpcmVkX3RpbWUiOiIyMDE2LTAzLTI4IDE5OjUzOjAwIiwicmVmcmVzaF90b2tlbiI6IjFjNDdhNmFkMWEwMjRjZmY1ODk5YmFlMzUwNTU1OTRhIiwiY2xpZW50X2lkIjoiYW5kcm9pZF9waG9uZSIsImNsaWVudF9zZWNyZXQiOiJmZDFjZTUzOTJkMmM2MzFiNzg5MWRlMzdjM2QyYzUyZCJ9._hp9yo_w3HRbpklwl6V_iAiQp_hq3PcLwVV-JkY8_v8', $message);
        $result = $momo->result();
        return view('action.sms', compact('result'));
    }
}
