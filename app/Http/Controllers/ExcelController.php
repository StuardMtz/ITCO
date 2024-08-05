<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AldeasImport;
use App\Imports\MunicipiosImport;
use App\Imports\SatImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    //
    public function ImportarExcel()
    {
      return view('excel.importar');
    }

    public function ImportarAldeas()
    {
      Excel::import(new AldeasImport, request()->file('excel'));
      return back()->with('success','Datos Importados Correctamente');
    }

    public function ImportarMunicipios()
    {
      Excel::import(new MunicipiosImport, request()->file('excel'));
      return back()->with('success','Datos Importados Correctamente');
    }

    public function ImportarReporteSat()
    {
      Excel::import(new SatImport, request()->file('excel'));
      return back()->with('success','Datos Importados Correctamente');
    }
}
