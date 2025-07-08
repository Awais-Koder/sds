<?php

namespace App\Http\Controllers;

use App\Models\Submittel;
use Illuminate\Http\Request;

class PdfDownloadController extends Controller
{
    public function downloadPdf($id)
    {
        $project = Submittel::with('outgoings')->findOrFail($id);
        // dd($project);
        return view('pdf.pdf' , compact('project'));
    }
}
