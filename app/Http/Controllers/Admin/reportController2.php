<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use DB;
use App\Models\Inventory;
use App\Models\InventorySerialNumberMapping;
use Session;

class reportController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
           Session::put('active', 'reports');

            return $next($request);
        });
    }
    
    public function reports_list(){
         $inventories2= DB::table('inventory_serial_number_mapping')
       ->join('asset', 'asset.id', '=', 'inventory_serial_number_mapping.asset_id')
       ->join('brands', 'brands.id', '=', 'asset.brand_id')
        ->select('brands.name as brandname','asset.name as assetsname',DB::raw('ifnull(count(inventory_serial_number_mapping.asset_id),0) as issuecount'))
        ->where('inventory_serial_number_mapping.sold_out',1)
       // ->where('inventory_serial_number_mapping.asset_id',3)
        ->groupBy('inventory_serial_number_mapping.asset_id')
        ->get();
       //return $inventories2;

        // SELECT brands.name, asset.name,  count(m.asset_id) AS issuecount FROM inventory_serial_number_mapping m,asset,brands  WHERE asset.id=m.asset_id and m.sold_out = 1 and brands.id=asset.brand_id GROUP BY m.asset_id;
        
        $inventories= DB::table('inventory')
        ->join('asset', 'asset.id', '=', 'inventory.item_type')
        ->join('brands', 'brands.id', '=', 'asset.brand_id')
        ->join('inventory_serial_number_mapping', 'inventory_serial_number_mapping.inventory_id', '=', 'inventory.id')
         ->select('brands.name as brandname','asset.name as assetsname',DB::raw('ifnull(count(inventory.item_qty),0) as item_qty'))
         //->where('inventory_serial_number_mapping.sold_out',1)
         ->groupBy('brandname')
         ->orderBy('asset.name', 'asc')
         ->get();
        //return $inventories;
        return view('admin.reports.list',compact('inventories','inventories2'));
    }
}
