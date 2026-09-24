<?php
use App\Models\Employee;
use App\Models\Location;
use App\Models\ItemIssue;
use App\Models\NeedValidation;
use App\Models\Service;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\Nvsericestatus;
use App\Models\SupDept;

if (!function_exists("dump_array")) {
    /**
     * description.
     *
     * @param array $value
     *
     * @return string
     */
    function dump_array($arr)
    {
        echo "<pre/>";
        print_r($arr);
        echo "</pre>";
    }
}

if (!function_exists("generateUniqueReferenceId")) {
    /**
     * description.
     *
     * @param array $value
     *
     * @return string
     */
    function generateUniqueReferenceId(string $preFix, int $id)
    {
        $milliseconds = milliseconds();

        return $preFix . $milliseconds . $id;
    }
}

if (!function_exists("milliseconds")) {
    /**
     * [milliseconds description].
     *
     * @return [type] [description]
     */
    function milliseconds()
    {
        $mt = explode(" ", microtime());
        $random = rand(1000, 9999);

        return $random . ((int) $mt[1]) * 100 + ((int) round($mt[0] * 100));
    }
}
if (!function_exists("getVendorDetailsByIssueAsset")) {
    function getVendorDetailsByIssueAsset($id)
    {
        $IssueAsset = ItemIssue::find($id);
        $asset_id = $IssueAsset->item_type;
        $inventory = Inventory::where("item_type", $asset_id)->first();
        $vendor = Vendor::find($inventory->vendor_id);

        return $vendor;
    }
}
if (!function_exists("getLocationName")) {
    function getLocationName($id)
    {
        $asset = Location::select("id", "name")->find($id);

        return $asset->name;
    }
}
if (!function_exists("getRoleName")) {
    function getRoleName($id)
    {
        $role = Role::select("id", "name")->find($id);
            if($role){
                return $role->name;
            }
        
    }
}
if (!function_exists("getUserName")) {
    function getUserName($id)
    {
        $user = User::select("id", "name")->find($id);
        if ($user) {
        return $user->name;
        }
    }
}

if (!function_exists("getEmpName")) {
    function getEmpName($id)
    {
        $emp = Employee::select("id", "name")->find($id);

        return $emp->name;
    }
}
if (!function_exists("getHodName")) {
    function getHodName($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("hod_id", $id)
            ->first();
       // dd($user->hod_id);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
            $employee = Employee::where("user_id", $nv_status->hod_id)->first();

            // $report_to = Employee::where(
            //     "user_id",
            //     $employee->report_to
            // )->first();
            return $employee->name ?? null;
        // }
    }
}
if (!function_exists("getUserEmail")) {
    function getUserEmail($id)
    {
        $user = User::select("id", "name", "role_id")
            ->where("id", $id)
            ->first();
        // dd(   $user->role);
        if ($user->role_id == 1) {
            return "Admin";
        } elseif($user->role_id == 2) {
            $employee = Employee::where("user_id", $user->id)->first();

            // return $employee ;
            $report_to = Employee::where(
                "user_id",
                $employee->report_to
            )->first();

            return $report_to->email;
        }
        elseif($user->role_id == 5) {
            $employee = Employee::where("user_id", $user->id)->first();

            // return $employee ;
            $report_to = Employee::where(
                "user_id",
                $employee->report_to
            )->first();

            return $report_to->email;
        }  elseif($user->role_id == 10) {
            $employee = Employee::where("user_id", $user->id)->first();

            // return $employee ;
            $report_to = Employee::where(
                "user_id",
                $employee->report_to
            )->first();

            return $report_to->email;
        }
        elseif($user->role_id == 6) {
            $employee = Employee::where("user_id", $user->id)->first();

            // return $employee ;
            $report_to = Employee::where(
                "user_id",
                $employee->report_to
            )->first();

            return $report_to->email;
        }
        elseif($user->role_id == 7) {
            $employee = Employee::where("user_id", $user->id)->first();

            // return $employee ;
            $report_to = Employee::where(
                "user_id",
                $employee->report_to
            )->first();

            return $report_to->email;
        } elseif($user->role_id == 8) {
            $employee = Employee::where("user_id", $user->id)->first();

            // return $employee ;
            $report_to = Employee::where(
                "user_id",
                $employee->report_to
            )->first();

            return $report_to->email;
        }
    }
}
if (!function_exists("getCpmgName")) {
    function getCpmgName($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("cpmg_id", $id)
            ->first();
       // dd($user->hod_id);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
            $employee = Employee::where("user_id", $nv_status->cpmg_id)->first();
        // $user = User::select("id", "name", "role_id")
        //     ->where("id", $id)
        //     ->first();
        // // dd(   $user->role);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
        //     $employee = Employee::where("user_id", $user->id)->first();
        //     if (!empty($employee)) {
        //         $report_to = Employee::where(
        //             "user_id",
        //             $employee->report_to
        //         )->first();
        //         if (!empty($report_to)) {
        //             $report_cpmg = Employee::where(
        //                 "user_id",
        //                 $report_to->report_to
        //             )->first();
        //             if (!empty($report_cpmg)) {
        //                 return $report_cpmg->name;
        //             } else {
        //                 return "";
        //             }
        //         }
        //     }
        // }
        return $employee->name ?? null ;
    }
}
if (!function_exists("getCesName")) {
    function getCesName($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("ces_id", $id)
            ->first();
       // dd($user->hod_id);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
            $employee = Employee::where("user_id", $nv_status->ces_id)->first();
        // dd(   $user->role);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
        //     $employee = Employee::where("user_id", $user->id)->first();
        //     $report_to = Employee::where(
        //         "user_id",
        //         $employee->report_to
        //     )->first();
        //     if (!empty($report_to)) {
        //         $report_cpmg = Employee::where(
        //             "user_id",
        //             $report_to->report_to
        //         )->first();
        //         if (!empty($report_cpmg)) {
        //             $report_ces = Employee::where(
        //                 "user_id",
        //                 $report_cpmg->report_to
        //             )->first();
        //             if (!empty($report_ces)) {
        //                 $report_budgetTeam = Employee::where(
        //                     "user_id",
        //                     $report_ces->report_to
        //                 )->first();
        //                 if (!empty($report_budgetTeam)) {
        //                     return $report_budgetTeam->name;
        //                 } else {
        //                     return "";
        //                 }
        //             }
        //         }
        //     }
        // }
        return  $employee->name ?? null;
    }
}
if (!function_exists("getCtoName")) {
    function getCtoName($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("cto_id", $id)
            ->first();
       // dd($user->hod_id);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
            $employee = Employee::where("user_id", $nv_status->cto_id)->first();
        // dd(   $user->role);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
        //     $employee = Employee::where("user_id", $user->id)->first();
        //     $report_to = Employee::where(
        //         "user_id",
        //         $employee->report_to
        //     )->first();
        //     if (!empty($report_to)) {
        //         $report_cpmg = Employee::where(
        //             "user_id",
        //             $report_to->report_to
        //         )->first();
        //         if (!empty($report_cpmg)) {
        //             $report_ces = Employee::where(
        //                 "user_id",
        //                 $report_cpmg->report_to
        //             )->first();
        //             if (!empty($report_ces)) {
        //                 $report_budgetTeam = Employee::where(
        //                     "user_id",
        //                     $report_ces->report_to
        //                 )->first();
        //                 if (!empty($report_budgetTeam)) {
        //                     return $report_budgetTeam->name;
        //                 } else {
        //                     return "";
        //                 }
        //             }
        //         }
        //     }
        // }
        return  $employee->name ?? null;
    }
}

if (!function_exists("getCeoNomineeName")) {
    function getCeoNomineeName($id)
    {

        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("ceo_nominee_id", $id)
            ->first();
       // dd($user->hod_id);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
            $employee = Employee::where("user_id", $nv_status->ceo_nominee_id)->first();
        // $user = User::select("id", "name", "role_id")
        //     ->where("id", $id)
        //     ->first();
        // // dd(   $user->role);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
        //     $employee = Employee::where("user_id", $user->id)->first();
        //     $report_to = Employee::where(
        //         "user_id",
        //         $employee->report_to
        //     )->first();
        //     if (!empty($report_to)) {
        //         $report_cpmg = Employee::where(
        //             "user_id",
        //             $report_to->report_to
        //         )->first();

        //         if (!empty($report_cpmg)) {
        //             $report_ces = Employee::where(
        //                 "user_id",
        //                 $report_cpmg->report_to
        //             )->first();
        //             if (!empty($report_ces)) {
        //                 $report_budgetTeam = Employee::where(
        //                     "user_id",
        //                     $report_ces->report_to
        //                 )->first();
        //                 if (!empty($report_budgetTeam)) {
        //                     $report_ceoNominee = Employee::where(
        //                         "user_id",
        //                         $report_budgetTeam->report_to
        //                     )->first();
        //                     if (!empty($report_ceoNominee)) {
        //                         return $report_ceoNominee->name;
        //                     } else {
        //                         return "";
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
        return $employee->name ?? null ;
    }
}
if (!function_exists("getCeoNominee2Name")) {
    function getCeoNominee2Name($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("ceo_nominee2_id", $id)
            ->first();
      
            $employee = Employee::where("user_id", $nv_status->ceo_nominee2_id)->first();
            return $employee->name ?? null ;
    }
}
if (!function_exists("getGroupHeadName")) {
    function getGroupHeadName($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("groupcio_id", $id)
            ->first();
      
            $employee = Employee::where("user_id", $nv_status->groupcio_id)->first();
            return $employee->name ?? null ;
    }
}
if (!function_exists("getCeoNominee2a2Name")) {
    function getCeoNominee2a2Name($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("ceo_nominee2_id", $id)
            ->first();
      
            $employee = Employee::where("user_id", $nv_status->ceo_nominee2a2_id)->first();
            return $employee->name ?? null ;
    }
}
if (!function_exists("getCeoName")) {
    function getCeoName($id)
    {
        $user = \Auth()->user();
        $nv_status = Nvsericestatus::where("ceo_id", $id)
            ->first();
      
            $employee = Employee::where("user_id", $nv_status->ceo_id)->first();
            return $employee->name ?? null ;
        // $user = User::select("id", "name", "role_id")
        //     ->where("id", $id)
        //     ->first();
        // // dd(   $user->role);
        // if ($user->role_id == 1) {
        //     return "Admin";
        // } else {
        //     $employee = Employee::where("user_id", $user->id)->first();
        //     $report_to = Employee::where(
        //         "user_id",
        //         $employee->report_to
        //     )->first();
        //     if (!empty($report_to)) {
        //         $report_cpmg = Employee::where(
        //             "user_id",
        //             $report_to->report_to
        //         )->first();

        //         if (!empty($report_cpmg)) {
        //             $report_ces = Employee::where(
        //                 "user_id",
        //                 $report_cpmg->report_to
        //             )->first();
        //             if (!empty($report_ces)) {
        //                 $report_budgetTeam = Employee::where(
        //                     "user_id",
        //                     $report_ces->report_to
        //                 )->first();
        //                 if (!empty($report_budgetTeam)) {
        //                     $report_ceoNominee = Employee::where(
        //                         "user_id",
        //                         $report_budgetTeam->report_to
        //                     )->first();
        //                     if (!empty($report_ceoNominee)) {
        //                         $report_ceo = Employee::where(
        //                             "user_id",
        //                             $report_ceoNominee->report_to
        //                         )->first();
        //                         if (!empty($report_ceo)) {
        //                             return $report_ceo->name;
        //                         } else {
        //                             return "";
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
        return $employee->name ?? null;
    }
}
if (!function_exists("getServiceName")) {
    function getServiceName($id)
    {
        $NeedValidation = NeedValidation::select("id", "service_id")->find($id);
        $service = Service::where("id", $NeedValidation->service_id)->first();
        return $service->name;
    }
}
if (!function_exists("getAllStatus")) {
    function getAllStatus($id)
    {
// dd($id);
        $all_status = Nvsericestatus::where("nv_id", $id)->get();
        // dd($all_status);
        // if($all_status->service_id!=null){
        //     $all_status = Nvsericestatus::where("service_id", $id)->orderBy('id','desc')->get();

        // }else{
        //     $all_status = Nvsericestatus::where("material_id", $id)->orderBy('id','desc')->get();
        // }
        // foreach ($all_status as $status) {
        //     yield $status;
        // }
      
        // if($all_status > 1){
        //    $offset = 0 ;
        //    while(true){
        //     $all_status = Nvsericestatus::where("nv_id", $id)->skip($offset)->first();
        //     if(!$all_status){
        //         break;
        //     } $offset++;
        //    }
          
        // }else{
        //     $all_status =Nvsericestatus::where("nv_id", $id)->orderBy('id','desc')->first();
        // }
        // $asset_id = $IssueAsset->item_type;
        // $inventory = Inventory::where("item_type", $asset_id)->first();
        // $vendor = Vendor::find($inventory->vendor_id);
        // dd($all_status);
        return $all_status;
    }
}
if (!function_exists("getProposalNumber")) {
    function getProposalNumber($id)
    {
        $NeedValidation = NeedValidation::select("*")->find($id);
        // $service=Service::where('id',$NeedValidation->service_id)->first();
        return $NeedValidation;
    }
}
if (!function_exists("getDepartmentName")) {
    function getDepartmentName($id)
    {
       
        $Department = Department::select("id", "name")->find($id);
         if($Department){
            return $Department->name;
         }
       
    }
}
if (!function_exists("getDepartmentNameByPro")) {
    function getDepartmentNameByPro($userId)
    {
        $emp = Employee::select('department_id')
            ->where('user_id', $userId)
            ->first();

        if (!$emp || empty($emp->department_id)) {
            return '';
        }

        // Convert comma separated IDs to array
        $deptIds = array_map('trim', explode(',', $emp->department_id));

        // Get department names
        $departments = Department::whereIn('id', $deptIds)
            ->pluck('name')
            ->toArray();
        // Return as comma separated string
        return implode(', ', $departments);
    }
}

if (!function_exists("getAssetName")) {
    function getAssetName($id)
    {
        $asset = Asset::select("id", "name")->find($id);

        return $asset->name;
    }
}
if (!function_exists("getVendorDetailsByReturnAsset")) {
    function getVendorDetailsByReturnAsset($id)
    {
        $IssueAsset = ItemReturn::find($id);
        $asset_id = $IssueAsset->item_type;
        $inventory = Inventory::where("item_type", $asset_id)->first();
        $vendor = Vendor::find($inventory->vendor_id);

        return $vendor;
    }
}
if (!function_exists("getDevisionName")) {
    function getDevisionName($id)
    {
        $division = Division::select("id", "name")->find($id);

        return $division->name;
    }
}

if (!function_exists("getlocationID")) {
    function getlocationID($id)
    {
        $location = Location::select("id", "name")->find($id);
        return $location->name;
    }
}

if (!function_exists("getHodid")) {

    function hodid($id)

    {

        $user = \Auth()->user();

        if($user->role_id == 11){

            $emp = Employee::select("id", "name", 'department_id', 'division_id')

            ->where("status", 1)->where('user_id', $user->id)

            ->first();

            $departments = Department::select("id", "name")

                ->where("status", 1)->where('id', $emp->department_id)

                ->get();

            $divisions = Division::select("id", "name")

                ->where("status", 1)->where('id', $emp->division_id)

                ->get();

 

            $employees = Employee::where("user_id", $user->id)->first();

            $department = Department::where("id", $employees->department_id)->first();

            //  dd($department->id);

            $hod = $department->dep_hod;

            

                    return $hod?? null;

        }

        

    }

}



if (!function_exists("getsuperdepname")) {
    function getsuperdepname($id)
    {
        $supdept_id = SupDept::select("id", "name")->find($id);
        // $service=Service::where('id',$NeedValidation->service_id)->first();
        if($supdept_id){
            return $supdept_id->name;
        }
      
    }

    // function convertNumberToWords($number) {
    //     $no = floor($number);
    //     $point = round($number - $no, 2) * 100;
    //     $hundred = null;
    //     $digits_1 = strlen($no);
    //     $i = 0;
    //     $str = array();
    //     $words = array(
    //         0 => '', 1 => 'one', 2 => 'two',
    //         3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
    //         7 => 'seven', 8 => 'eight', 9 => 'nine',
    //         10 => 'ten', 11 => 'eleven', 12 => 'twelve',
    //         13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
    //         16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
    //         20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty',
    //         60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    //     );
    //     $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    //     while ($i < $digits_1) {
    //         $divider = ($i == 2) ? 10 : 100;
    //         $number = floor($no % $divider);
    //         $no = floor($no / $divider);
    //         $i += ($divider == 10) ? 1 : 2;
    //         if ($number) {
    //             $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
    //             $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
    //             $str [] = ($number < 21) ? $words[$number] .
    //                 " " . $digits[$counter] . $plural . " " . $hundred :
    //                 $words[floor($number / 10) * 10]
    //                 . " " . $words[$number % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
    //         } else $str[] = null;
    //     }
    //     $str = array_reverse($str);
    //     $result = implode('', $str);
    //     $points = ($point) ? "." . $words[$point / 10] . " " .
    //         $words[$point = $point % 10] : '';
    //     // return $result . "Rupees " . $points . " Paise";
    //     return $result;
    // }

    function convertNumberToWords($number) {
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
            20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty',
            60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? $words[$number] . " " . $digits[$counter] . $plural . " " . $hundred :
                    $words[floor($number / 10) * 10] . " " . $words[$number % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ? "." . $words[$point / 10] . " " . $words[$point = $point % 10] : '';
        return $result;
    }
}

if (!function_exists("getdepname")) {
    function getdepname($id)
    {
        $dept_id = Department::select("id", "name")->find($id);
        if($dept_id){
            return $dept_id->name;
        }
      
    }
}

if (!function_exists("getPrefixDepartmentName")) {
    function getPrefixDepartmentName($id)
    {
       
        $Department = Department::select("id", "prefix")->find($id);
         if($Department){
            return $Department->prefix;
         }
       
    }
}

 if (!function_exists("getStatusUserName")) {
        function getStatusUserName($id)
        {
            // $user = \Auth()->user();
            // $nv_status = DB::table('capex_workflows_status')->where("workflow_user_id", $id)
            //     ->first();
           
                if($id){
                    $employee = Employee::where("user_id", $id)->first();
                }
                // dd($employee->name);
            return  $employee->name ?? null;
        }

       if (!function_exists('indian_number_format')) {
            function indian_number_format($num, $decimals = 2, $currency = '₹') {

                // Handle null, empty string, non-numeric safely
                if ($num === null || $num === '' || !is_numeric($num)) {
                    $num = 0;
                }

                // Convert to float to avoid number_format error
                $num = (float)$num;

                $exploded = explode('.', number_format($num, $decimals, '.', ''));
                $decimal = isset($exploded[1]) ? '.' . $exploded[1] : '';
                $num = $exploded[0];

                $last3 = substr($num, -3);
                $rest = substr($num, 0, -3);

                if ($rest != '') {
                    $last3 = ',' . $last3;
                }

                $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);

                return $currency . ' ' . $rest . $last3 . $decimal;
            }
        }

       function bses_get_token()
        {
            if (cache()->has('bses_token')) {
                return cache()->get('bses_token');
            }

            return bses_generate_token();
        }

        function bses_generate_token()
        {
            $url = "https://bsesapps.bsesdelhi.com/DelhiV2/ISUService.asmx";

            $soapXml = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <GenerateAuthenticationToken xmlns="http://tempuri.org/">
                        <userName>DelhiV2ServiceUser</userName>
                        <password>X!9q#Ld7@Vw$35Nz</password>
                    </GenerateAuthenticationToken>
                </soap:Body>
            </soap:Envelope>';

            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $soapXml,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: text/xml',
                    'SOAPAction: http://tempuri.org/GenerateAuthenticationToken',
                ],
                CURLOPT_TIMEOUT        => 30,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch));
            }

            curl_close($ch);

            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = simplexml_load_string($response);
            $array = json_decode(json_encode($xml), true);

            $token = $array['soapBody']['GenerateAuthenticationTokenResponse']
                ['GenerateAuthenticationTokenResult'] ?? null;

            if (!$token) {
                throw new Exception('Token generation failed');
            }

            cache()->put('bses_token', $token, now()->addMinutes(55));

            return $token;
        }


    }

