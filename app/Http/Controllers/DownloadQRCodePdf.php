<?php

namespace App\Http\Controllers;

use App\Models\Kencleng;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DownloadQRCodePdf extends Controller
{
    
    public function download(string $id)
    {
        $data = Kencleng::where('batch_kencleng_id', $id)->get()->toArray();


        $pdf = Pdf::loadView('pdf', ['data' => $data])->setPaper('a3');
        return $pdf->download('qrcode.pdf');
    }
}