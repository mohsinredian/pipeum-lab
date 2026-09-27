<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Silber\Bouncer\Database\Ability;
use Validator;
use Session;
class AbilityController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'abilities');

            return $next($request);
        });
    }
    // Abilities listing
    public function abilities(Request $request){
    	// $abilities = Ability::paginate(10);

        if($request->ajax()){

            $abilities = datatables()
                ->of(
                    Ability::select('id', 'title', 'name')
                                ->get()
                )
                ->addColumn('action', function($data){

                    $button = '<a href="/admin/edit_ability/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';

                    return $button;           
                })
                // ->rawColumns(['action'])
                ->make(true);

            return $abilities;
        }

    	return view('admin.abilities');
    }

    // Display create ability form
    public function create_ability(Request $request){
    	return view('admin.create_ability');
    }

    // Save new ability
    public function store_ability(Request $request){
    	try{

            $request_input = $request->except('_token');

            $rules = [
                'name' => 'required|string|max:250',
                'title' => 'required|string|max:255'
            ];

            $messages = [
                'name.required' => 'Please enter ability name',
                'name.max' => 'Ability name should not be more than 250 characters',
                'title.required' => 'Please enter title',
                'title.max' => 'Title should not be more than 250 characters'
            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{

                Ability::create($request_input);

                $response['result'] = 'success';
                $response['msg'] = 'Ability created';
            }
        }
        catch(Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return json_encode($response);
    }

    // Display edit ability form
    public function edit_ability(Request $request){
        $ability = Ability::find($request->id);

        if(is_null($ability)){
            abort(404);
        }
        return view('admin.edit_ability', compact('ability'));
    }

    // Update ability
    public function update_ability(Request $request){
    	try{

            $request_input = $request->except('_token');

            $rules = [
                'name' => 'required|string|max:250',
                'title' => 'required|string|max:255'
            ];

            $messages = [
                'name.required' => 'Please enter ability name',
                'name.max' => 'Ability name should not be more than 250 characters',
                'title.required' => 'Please enter title',
                'title.max' => 'Title should not be more than 250 characters'
            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                Ability::find($request_input['ability_id'])
                        ->update(['title' => $request_input['title'], 'name' => $request_input['name']]);

                $response['result'] = 'success';
                $response['msg'] = 'Ability updated';
            }
        }
        catch(Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return json_encode($response);
    }
}