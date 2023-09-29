<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function showForm()
{
    return view('form');
}

public function submitForm(Request $request)
{
    $data = $request->all();
    // Handle the form submission data.
    print_r($data);
    die;
    return redirect('/form')->with('success', 'Form submitted successfully!');
}
}
