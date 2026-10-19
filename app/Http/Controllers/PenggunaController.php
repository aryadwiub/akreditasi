<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function user(Request $request)
    {
        $edit = null;
        $nipCari = null;
        $errorCari = null;

        // pencarian berdasarkan NIP (nanti diganti/ditambah pemanggilan API Satudata)
        if ($request->filled('nip')) {
            $nip = trim((string) $request->query('nip'));

            if (!preg_match('/^[0-9]{1,30}$/', $nip)) {
                $errorCari = 'NIP hanya boleh berisi angka (maksimal 30 digit).';
            } else {
                $nipCari = $nip;
                $edit = User::where('nip', $nipCari)->first();

                if (!$edit) {
                    session()->now('info', 'NIP ' . $nipCari . ' belum terdaftar. Silakan lengkapi form untuk menambah pengguna baru.');
                }
            }
        } elseif ($request->filled('edit')) {
            $edit = User::findOrFail((int) $request->query('edit'));
        }

        $users = User::orderBy('nama')->orderBy('username')->get();

        return view(
            'backend/referensiuser',
            ['judul' => 'Manajemen Pengguna'],
            compact('users', 'edit', 'nipCari', 'errorCari')
        );
    }

    public function simpanuser(Request $request)
    {
        $data = $this->validasi($request);

        User::create($data);

        return redirect('/referensiuser')->with('sukses', 'Pengguna berhasil ditambahkan.');
    }

    public function ubahsimpanuser(Request $request, $id)
    {
        $user = User::findOrFail((int) $id);
        $data = $this->validasi($request, $user);

        // password kosong saat ubah = password lama tidak diganti
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // cegah admin menonaktifkan akunnya sendiri
        if ($user->id === Auth::id() && !$data['aktif']) {
            return back()->withInput()->withErrors(['aktif' => 'Anda tidak dapat menonaktifkan akun yang sedang dipakai.']);
        }

        $user->update($data);

        return redirect('/referensiuser')->with('sukses', 'Data pengguna berhasil diubah.');
    }

    private function validasi(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user?->id)],
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'regex:/^[0-9]{1,30}$/', Rule::unique('users', 'nip')->ignore($user?->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
            'username.unique' => 'Username sudah dipakai pengguna lain.',
            'nip.regex' => 'NIP hanya boleh berisi angka (maksimal 30 digit).',
            'nip.unique' => 'NIP sudah terdaftar.',
            'email.unique' => 'Email sudah dipakai pengguna lain.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }
}
