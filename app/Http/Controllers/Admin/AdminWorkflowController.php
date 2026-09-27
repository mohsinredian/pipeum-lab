<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MultipleWorkFlow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Session;

class AdminWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'notesworkflows_list');

            return $next($request);
        });
    }
    public function notesworkflows_list(Request $request)
    {
        $user = \Auth()->user();
        $workflows = MultipleWorkFlow::orderBy('id', 'desc')->get();
        return view('admin.adminworkflow.list', ['workflows' => $workflows]);
    }

    public function create_notesworkflows()
    {
        return view('admin.adminworkflow.create');
    }

    public function store_notesworkflows(Request $request)
    {
        $user_id = \Auth::user()->id;
        $request_input = $request->except('_token');

        $validatedData = $request->validate([
            'name' => ['required', 'max:255', Rule::unique(MultipleWorkFlow::class)],
            'status' => 'required',
        ], [
            'name.unique' => 'The name has already been taken.',
        ]);
    

        // Validate and store the new workflow
       $notes_workflow= MultipleWorkFlow::create([
            'name' => $validatedData['name'],
            'status' => $validatedData['status'],
        ]);

    

        return response()->json(['result' => 'success']);
    }

    
    public function edit_notesworkflows(MultipleWorkFlow $workflow)
    {
        return view('admin.adminworkflow.edit', ['workflow' => $workflow]);
    }

    public function update_notesworkflows(Request $request, MultipleWorkFlow $workflow)
    {
        $user_id = \Auth::user()->id;
        $request_input = $request->except('_token');

        $validatedData = $request->validate([
            'name' => 'required|max:255', // Adjust the validation rules as needed
            'status' => 'required',
        ]);

        // Update the workflow with the validated data
        $workflow->update([
            'name' => $validatedData['name'],
            'status' => $validatedData['status'],
        ]);

        return response()->json(['result' => 'success']);
    }

    public function export_excel(Request $request)
    {
        $notes_workflows =  MultipleWorkFlow::orderBy('id', 'desc')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Workflow Name</th><th>Status</th></tr>';
        
        foreach ($notes_workflows as $key => $notes_workflows) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' .$notes_workflows->name . '</td>';
            $output .= '<td>' . ($notes_workflows->status == 1 ? 'Active' : 'Inactive') . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=NotesWokflow_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
 
}
