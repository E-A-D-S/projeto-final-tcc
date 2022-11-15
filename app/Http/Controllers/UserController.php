<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\model_has_permission;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
  
  function home () {
    return View('index');
  }
  function homeScreen () {
    return View('homeScreen');
  }
  function permission() {
    $permission = model_has_permission::all();
    $user = User::all();
    return View('permission', compact('permission', 'user'));
  }

  function index()
  {
   $patient = Patient::all();
   $search = request('search');
   if($search ) {
     $patient = Patient::where([
            ['name', 'like', '%' . $search . '%']
      ])->get();
      return view('admin.index', compact('patient'));
    }   
    return view('admin.index', compact('patient'));
  }

  public function store(Request $request)
  {
    $patient  = new Patient();
    
    $patient->name = $request->name;
    $patient->address = $request->address;
    $patient->rg = $request->rg;
    $patient->cpf = $request->cpf;
    $patient->birth_date = $request->birth_date;
    $patient->telephone = $request->telephone;
    $patient->time_service = $request->time_service;
    $patient->consultation = $request->consultation;
    $patient->marital_status = $request->marital_status;
    $patient->house_number = $request->house_number;
    $patient->district = $request->district;
    $patient->Complement = $request->Complement;
    $patient->city = $request->city;
    $patient->name_father = $request->name_father;
    $patient->address_father = $request->address_father;
    $patient->city_father = $request->city_father;
      
    $patient->save();

    $data = array(
      'name'=> $request->name,
      'birth_date' => $request->birth_date,
      'time_service' => $request->time_service,
      'consultation' => $request->consultation,
    );
    Mail::to(config('mail.from.address'))->send(new SendMail($data));
    return redirect()->route('paciente.home')->with('paciente', 'Cadastrado feito com sucesso!');
  }

  public function destroy($id)
  {
    $Patient = Patient::find($id);
    if (!$Patient) {
      return redirect()->route('category.index');
    }
    $Patient->delete();
    return redirect()->route('paciente.index')->with('categoriadel', 'Produto deletado com sucesso!');
  }

  public function edit($id)
  {
    $patient = Patient::find($id);
    if (!$patient) {
      return redirect()->route('paciente.index');
    }

    return view('admin.edit', compact('patient'));
  }

  public function view($id)
  {
    $patient = Patient::find($id);
    if (!$patient) {
      return redirect()->route('paciente.index');
    }

    return view('admin.view', compact('patient'));
  }

  public function update(Request $request)
  {
    Patient::findOrfail($request->id)->update($request->all());
    return redirect()->route('paciente.index')->with('categoriaedit', 'Produto editado com sucesso!');
  }

  public function generatePdf($id)
  {
    $data = Patient::find($id);
    $pdf = Pdf::loadView('pdf.dicePatient', compact('data'));
    return $pdf->stream('dicePatient.pdf');
  }

  public function permissionEdit($id) {
    $data = model_has_permission::where([
      ['model_id', '=', $id ]
    ])->get();
    return view('permissionEdit', compact('data'));
  }

  public function permissionUpdate(Request $request, $id)
  {
    $data = request()->except(['_method', '_token']);
    model_has_permission::where('model_id', $id)->update($data);
    return redirect()->route('paciente.permission');
  }
    
}
