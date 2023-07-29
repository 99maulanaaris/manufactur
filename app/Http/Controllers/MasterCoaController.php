<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class MasterCoaController extends Controller
{
    public function index (Request $request)
    {
        $data = Account::orderBy('no_account','desc');
        if($request->ajax()){
            $data = $data->get();
            return DataTables::of($data)->addIndexColumn()->toJson();
        }
        return view('MasterCoa.index',compact('data'));
    }

    public function getAccount()
    {
        $data = Account::all();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(),[
            'account_name' => 'required',
            'account_type' => 'required',
            'type_coa' => 'required',
            'no_account' => 'required|unique:accounts,no_account'
        ],[
            'account_name.required' => 'Nama Akun Tidak Boleh Kosong !',
            'account_type.required' => 'Tipe Akun Tidak Boleh Kosong !',
            'type_coa.required' => 'Tipe Coa Tidak Boleh Kosong !',
            'no_account.unique' => 'No Coa Sudah Terdaftar !'
        ]);

        if($validasi->fails()){
            return response()->json(['msg' => $validasi->errors()->first(),'status' => 400]);
        }
        
        $account = Account::where('no_account',$request->no_account)->first();
        if(!$account){
            if($request->parent_id){
                $parent = Account::where('id',$request->parent_id)->first();

                Account::create([
                    'no_account' => $request->no_account,
                    'account_type' => $request->account_type,
                    'account_name' => $request->account_name,
                    'type_coa' => $request->type_coa,
                    'parent_id' => $request->parent_id,
                ]);

                Account::where('id',$request->parent_id)->update([
                    'child' => $parent->child + 1
                ]);

                return response()->json(['msg' => 'Berhasil Membuat Akun','status' => 200]);
                
            }

            Account::create([
                'no_account' => $request->no_account,
                'account_type' => $request->account_type,
                'account_name' => $request->account_name,
                'type_coa' => $request->type_coa,
                'parent_id' => $request->parent_id,
            ]);

            return response()->json(['msg' => 'Berhasil Membuat Akun','status' => 200]);
        }

        Account::where('id',$request->id)->update([
            'no_account' => $request->no_account,
            'account_type' => $request->account_type,
            'account_name' => $request->account_name,
            'type_coa' => $request->type_coa,
            'parent_id' => $request->parent_id,
        ]);
        
        return response()->json(['msg' => 'Berhasil Edit Akun','status' => 200]);

    }
}
