<?php

namespace App\Http\Controllers;

use App\Models\Kencleng;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DownloadQRCodePdf extends Controller
{
    
    public function download()
    {
        $data = Kencleng::all()->toArray();

        // dd($data);

        $pdf = Pdf::loadView('pdf', ['data' => $data])->setPaper('a4');
        return $pdf->download('qrcode.pdf');
    }
}