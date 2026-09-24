<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
class MasterController extends Controller
{
	public function __construct()
    {
    }
    /**
     * [get_states description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_states(Request $request)
    {
        try {
            $country_id = $request->country_id ?? null;
                
            $country = Country::find($country_id);
            $states = [];
            if (!empty($country)) {
                $country_id = $country->id;
                $states = State::select('id', 'name', 'country_id')
                    ->where('country_id', $country_id)
                    ->get();
            }
            return response()->json(['result' => 'success', 'data' => $states]);
         } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json(['result' => 'failure', 'msg' => $e->getMessage()]);
        }
    }
    /**
     * [get_cities description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_cities(Request $request)
    {
        try {
            $state_id = $request->state_id ?? null;
                
            $state = State::find($state_id);
            $cities = [];

            if (!empty($state)) {
                $state_id = $state->id;
                $cities = City::select('id', 'name', 'state_id')
                    ->where('state_id', $state_id)
                    ->get();
            }

            return response()->json(['result' => 'success', 'data' => $cities]);
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json(['result' => 'failure', 'msg' => $e->getMessage()]);
        }
    }
}