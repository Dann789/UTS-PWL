<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => '',
            'list' => ['Home', 'Profile']
        ];
        $activeMenu = 'profile';
        return view('profile', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
    public function upload_foto(Request $request){
        // buat validasi ektensi dari filenya
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // ini buat nentuin mau ditaruh mana file yang diupload
        $folderPath = 'uploads/profile_pictures/'.auth()->user()->username.'/';
        // ini buat hapus foto profil lama yang nantinya diganti sama yang baru
        $extensions = ['jpg', 'jpeg', 'png'];
        foreach ($extensions as $ext) {
            $namaFileLama = $folderPath . auth()->user()->username . '_profile.' .$ext;
            if(Storage::disk('public')->exists($namaFileLama)){
                Storage::disk('public')->delete($namaFileLama);
                break;
            }
        }
        // Ambil file dari request
        $file = $request->file('foto');
        // Buat nama file unik
        $filename = auth()->user()->username . '_profile.' . $file->getClientOriginalExtension();
        // Simpan file ke storage/app/public/uploads/profile_pictures/(username)
        $file->storeAs($folderPath, $filename, 'public');
        // Lakukan sesuatu dengan file, misalnya simpan ke database
        // auth()->user()->update(['profile_picture' => $filename]);
        return back()->with('success', 'Foto berhasil diupload.');
    }

    public function edit_ajax(string $id)
    {
        $user = UserModel::findOrFail($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('landing_page', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|max:20|unique:m_user,username,'.$id.',user_id',
                'nama' => 'required|max:100',
                'password' => 'nullable|min:6|max:20'
            ];

            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }

            $check = UserModel::find($id);
            if ($check) {
                if(!$request->filled('password') ){ // jika password tidak diisi, maka hapus dari request
                    $request->request->remove('password');
                }
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/profile');
    }
}