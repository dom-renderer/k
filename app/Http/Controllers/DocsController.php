<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocsController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->can('doc-list'), 403, 'Unauthorized action.');

        return view('docs');
    }
}
