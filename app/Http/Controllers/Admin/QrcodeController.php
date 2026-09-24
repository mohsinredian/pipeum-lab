<?php

namespace App\Http\Controllers\Admin;

use App\Models\ItemIssue;
use App\Models\InventorySerialNumberMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\HtmlString;
use PDF;
use App\Http\Controllers\Controller;
use App\Models\ItemReturn;

class QrcodeController extends Controller
{
    public function qrCode($id)
    {
        $id=decrypt($id);
        $issueAsset = ItemIssue::find($id);
        $quantity = $issueAsset->item_qty;
        $serialNumbers = explode(',',$issueAsset->serial_number);
        $issueDetails = InventorySerialNumberMapping::whereIn('serial_number',$serialNumbers)->where('sold_out',1)->get();
        
        $pdf = PDF::loadView('admin.qrcode.index', compact('issueDetails'));
        return $pdf->stream();
    }

    public function qrCodeReturn($id){
        $id=decrypt($id);
        $issueAsset = ItemReturn::find($id);
        $quantity = $issueAsset->item_qty;
        $serialNumbers = explode(',',$issueAsset->serial_number);
        $issueDetails = InventorySerialNumberMapping::whereIn('serial_number',$serialNumbers)->where('sold_out',1)->get();
        $pdf = PDF::loadView('admin.qrcode.index', compact('issueDetails'));
        return $pdf->stream();
    }
}
