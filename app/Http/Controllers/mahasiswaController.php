<?php

namespace App\Http\Controllers;

use App\Models\mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class mahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $katakunci = $request->katakunci;
        $jumlahbaris = 3;
        if(strlen($katakunci)){
            $data = mahasiswa::where('nim','like',"%$katakunci%")
                ->orWhere('nama','like',"%$katakunci%")
                ->orWhere('jurusan','like',"%$katakunci%")
                ->paginate($jumlahbaris);
        } else  {
            $data = mahasiswa::orderBy('nim','desc')->paginate($jumlahbaris);
        }
        return view('mahasiswa.index')->with('data', $data);
    }

    public function create()
    {
        return view('mahasiswa.create');
    }

    public function store(Request $request)
    {
        Session::flash('nim', $request->nim);
        Session::flash('nama', $request->nama);
        Session::flash('jurusan', $request->jurusan);

        $request->validate([
            'nim'=>'required|numeric|unique:mahasiswa,nim',
            'nama'=>'required',
            'jurusan'=>'required'
        ],[
            'nim.required' => 'NIM wajib diisi',
            'nim.numeric' => 'NIM wajib berupa angka',
            'nim.unique' => 'NIM sudah ada dalam database',
            'nama.required' => 'Nama wajib diisi',
            'jurusan.required' => 'Jurusan wajib diisi'
        ]);
        $data = [
            'nim' => $request->nim,
            'nama' => $request->nama,
            'jurusan' => $request->jurusan
        ];
        mahasiswa::create($data);
        return redirect()->to('mahasiswa')->with('success','Berhasil menambahkan data');
    }
   
    public function show(string $id)
    {
        $data = mahasiswa::where('nim', $id)->firstOrFail();
        return view('mahasiswa.show', compact('data'));
    }
    public function edit(string $id)
    {
        $data = mahasiswa::where('nim',$id)->first();
        return view('mahasiswa.edit')->with('data',$data);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama'=>'required',
            'jurusan'=>'required'
        ],[
            'nama.required' => 'Nama wajib diisi',
            'jurusan.required' => 'Jurusan wajib diisi'
        ]);
        $data = [
            'nama' => $request->nama,
            'jurusan' => $request->jurusan
        ];
        mahasiswa::where('nim', $id)->update($data);
        return redirect()->to('mahasiswa')->with('success','Berhasil melakukan update data');
    }

    public function destroy(string $id)
    {
        mahasiswa::where('nim', $id)->delete();
        return redirect()->to('mahasiswa')->with('success','Berhasil melakukan delete data');
    }
}
