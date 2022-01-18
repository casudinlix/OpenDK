<?php

/*
 * File ini bagian dari:
 *
 * OpenDK
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2017 - 2022 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package	    OpenDK
 * @author	    Tim Pengembang OpenDesa
 * @copyright	Hak Cipta 2017 - 2022 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license    	http://www.gnu.org/licenses/gpl.html    GPL V3
 * @link	    https://github.com/OpenSID/opendk
 */

namespace App\Http\Controllers\Informasi;

use App\Http\Controllers\Controller;
use App\Models\FormDokumen;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FormDokumenController extends Controller
{
    public function index()
    {
        $page_title       = 'Dokumen';
        $page_description = 'Daftar Dokumen';

        return view('informasi.form_dokumen.index', compact('page_title', 'page_description'));
    }

    public function getDataDokumen()
    {
        return DataTables::of(FormDokumen::all())
            ->addColumn('aksi', function ($row) {
                if (! Sentinel::guest()) {
                    $data['edit_url']   = route('informasi.form-dokumen.edit', $row->id);
                    $data['delete_url'] = route('informasi.form-dokumen.destroy', $row->id);
                }

                $data['download_url'] = asset($row->file_dokumen);

                return view('forms.aksi', $data);
            })->make();
    }

    public function create()
    {
        $page_title       = 'Dokumen';
        $page_description = 'Tambah Dokumen';

        return view('informasi.form_dokumen.create', compact('page_title', 'page_description'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'nama_dokumen' => 'required',
            'file_dokumen' => 'required|mimes:jpeg,png,jpg,gif,svg,xlsx,xls,doc,docx,pdf,ppt,pptx|max:2048',
        ]);

        try {
            $dokumen = new FormDokumen($request->input());

            if ($request->hasFile('file_dokumen')) {
                $file     = $request->file('file_dokumen');
                $fileName = $file->getClientOriginalName();
                $path     = "storage/form_dokumen/";
                $request->file('file_dokumen')->move($path, $fileName);
                $dokumen->file_dokumen = $path . $fileName;
            }

            $dokumen->save();
        } catch (Exception $e) {
            return back()->with('error', 'Dokumen gagal disimpan!' . $e->getMessage());
        }

        return redirect()->route('informasi.form-dokumen.index')->with('success', 'Dokumen berhasil ditambah!');
    }

    public function edit($id)
    {
        $dokumen          = FormDokumen::findOrFail($id);
        $page_title       = 'Dokumen';
        $page_description = 'Ubah Dokumen ' . $dokumen->nama_dokumen;

        return view('informasi.form_dokumen.edit', compact('page_title', 'page_description', 'dokumen'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'nama_dokumen' => 'required',
            'file_dokumen' => 'mimes:jpeg,png,jpg,gif,svg,xlsx,xls,doc,docx,pdf,ppt,pptx|max:2048',
        ]);

        try {
            $dokumen = FormDokumen::findOrFail($id);
            $dokumen->fill($request->all());

            if ($request->hasFile('file_dokumen')) {
                $file     = $request->file('file_dokumen');
                $fileName = $file->getClientOriginalName();
                $path     = "storage/form_dokumen/";
                $request->file('file_dokumen')->move($path, $fileName);
                $dokumen->file_dokumen = $path . $fileName;
            }

            $dokumen->save();
        } catch (Exception $e) {
            return back()->with('error', 'Dokumen gagal diubah!' . $e->getMessage());
        }

        return redirect()->route('informasi.form-dokumen.index')->with('success', 'Dokumen berhasil diubah!');
    }

    public function destroy($id)
    {
        try {
            $dokumen = FormDokumen::findOrFail($id);

            unlink(base_path('public/' . $dokumen->file_dokumen));

            $dokumen->delete();
        } catch (Exception $e) {
            return redirect()->route('informasi.form-dokumen.index')->with('error', 'Dokumen gagal dihapus!');
        }

        return redirect()->route('informasi.form-dokumen.index')->with('success', 'Dokumen berhasil dihapus!');
    }
}
