<?php


namespace App\Imports;

// ini_set('max_execution_time', '300'); //300 seconds = 5 minutes

ini_set('max_execution_time', '0'); // for infinite time of execution

// use Validator;

use App\Models\NeedValidation;
use App\Models\Nvsericestatus;
use App\Models\NVMaterial;
// use App\Models\Country;
use App\Models\NVService;
// use App\Models\Provider;
// use App\Models\ProviderVehicleServices;
// use App\Models\State;
use Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;


class ProviderBulkImport implements ToCollection, WithStartRow

{

    protected $returnAbortData = [];
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        dd($rows);
      $count = count($rows->toArray());
        $abortData = [];
        if ($count <= 5000) {
        $chunks = array_chunk($rows->toArray(), 500);
       
        foreach ($chunks as $chunk) {
            foreach ($chunk as $row) {

                if (count($row) >= 9) {
                    if ($row[0] != '' && $row[1] != '' && $row[2] != '' && $row[3] != '' && $row[4] != '' && $row[5] != '' ) {

                        // $country = Country::where('id', '101')->first();
                        // $state = State::where('id', $row[6])->where('country_id', '101')->first();
                        // $city = City::where('id', $row[7])->where('state_id', $row[6])->first();

                        $checkEmail = Provider::where('email', $row[2])->first();

                        if (empty($checkEmail->id)) {

                            // $checkworkshop = Provider::where('workshop_registered_name', $row[5])->first();
                            // if ($checkworkshop) {
                            //     $row['20'] = 'This workshop name already exist.';
                            //     array_push($abortData, $row);
                            // } else {
                            // if ($row[3] == $row[17]) {
                            //     $row['20'] = 'This mobile no. and seconadry mobile no. should not same.';
                            //     array_push($abortData, $row);
                            // } else {
                            #check the requested email already exist or not

                            //$checkmobile = Provider::where('mobile', $row[3])->first();

                        //    if (empty($checkmobile->id)) {
                                 // if ($country != "" && $state != "" && $city != "") {

                                    $arrayData = [

                                        'provider_unique_id' => $this->generateRandomCode(15),
                                        'material_id' => $row[0] ? str_replace(' ', '',$row[0]) : null,
                                        'nv_id' => $row[1] ?str_replace(' ', '',$row[1]) : null,
                                        'material_code' => $row[2] ? str_replace(' ', '',$row[2]) : null,
                                        'rate' => $row[3] ? str_replace(' ', '', $row[3]) : null,
                                        'quantity' => Hash::make($row[4]) ?? null,
                                        'amount' => $row[5] ?? null,
                                        // 'country_id' => 101,
                                        // 'state_id' => $row[6] ?? null,
                                        // 'city_id' => $row[7] ?? null,
                                        // 'address' => $row[8] ?? null,
                                        // 'location' => $row[9] ?? null,
                                        // 'open_time' => date("H:i:s", strtotime($row[10])) ?? null,
                                        // 'close_time' => date("H:i:s", strtotime($row[11])) ?? null,
                                        // 'latitude' => $row[12] ?? null,
                                        // 'longitude' => $row[13] ?? null,
                                        // 'customer_location_visit' => ($row[14] == 1) ? 'Yes' : 'No',
                                        // 'gst_number' =>$row[16] ? str_replace(' ', '',$row[16]) : null,
                                        // 'mobile_secondary' => $row[17] ? str_replace(' ', '',$row[17]) : null,
                                        // 'pan_card' => $row[18] ? str_replace(' ', '',$row[18]) : null,
                                        // 'aadhar_card' => $row[19] ? str_replace(' ', '',$row[19]) : null,
                                        // 'approve_status' => 2, // Approved
                                        // 'plane_password' => $row[4] ?? null,
                                        // 'added_by_bulk' => 1,
                                        // 'mail_send' => 1,
                                    ];

                                   $createData = MaterialBOQBulk::create($arrayData);

                                    if ($createData) {
                                        $vehicle_services = explode(', ', trim($row[15]));
                                        foreach ($vehicle_services as $key => $vehicle_cat) {
                                            # request param
                                            $value = new ProviderVehicleServices;
                                            $value->provider_id = $createData->id ?? null;
                                            $value->vehicle_type_id = $vehicle_cat ?? null;
                                            $value->save();
                                        }

                                    } else {
                                        $row['20'] = 'Data not uploaded';
                                        // $abortData1[][$key] = $row;
                                        // $abortData1[] = $row;
                                        array_push($abortData, $row);
                                    }
                                // } else {
                                //     $row['20'] = 'State/City not matched.';
                                //     array_push($abortData, $row);
                                // }
                            // } else {
                            //     $row['20'] = 'This mobile already exist.';
                            //     array_push($abortData, $row);
                            // }
                            //}
                            //}
                        } else {
                            $row['20'] = 'This email already exist.';
                            array_push($abortData, $row);
                        }
                    } else {
                        $row['20'] = 'Required field is empty.';
                        array_push($abortData, $row);
                    }
                } else {
                    $row['20'] = 'Your Are Uploaded Wrong Sheet.';
                    array_push($abortData, $row);

                }
            }
        }
        } else {
            $row['20'] = 'Your Are Uploaded Large Amount Data Sheet.Please upload only 5000 data at a time.';
            array_push($abortData, $row);
        }
        $this->returnAbortData = $abortData;
        return;

    }
    public function allAbortData()

    {
        return $this->returnAbortData;
    }

/**

* function to generate the random code
*
* @return Token

*/
    public function generateRandomCode($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;

    }
}