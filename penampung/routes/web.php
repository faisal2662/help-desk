<?php

use App\Http\Controllers\BagianKantorCabangController;
use App\Http\Controllers\BagianKantorPusatController;
use App\Http\Controllers\BagianKantorWilayahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KantorCabangController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\KantorPusatController;
use App\Http\Controllers\KantorWilayahController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LaporanPengaduan;
use App\Http\Controllers\Chat;
use App\Http\Controllers\RoleAccountController;
use App\Http\Controllers\RoleMenu;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NamaPosisiController;

use App\Http\Controllers\CPU;
use App\Http\Controllers\Notifikasi;
use App\Http\Controllers\SetupWhatsappController;
use App\Http\Controllers\PertanyaanFAQController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\PegawaiSunfishController;
use App\Http\Controllers\SMTP;
use App\Http\Controllers\Profil;
use App\Models\API\BagianKantorCabang;
use App\Models\API\BagianKantorPusat;
use App\Models\PegawaiSunfish;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::post('get_data', [LoginController::class, 'get_data'])->name('login.get_data');
Route::get('verify/{id}', [LoginController::class,'verify'])->name('verify_otp');
Route::post('verify_otp', [LoginController::class, 'verifyOtp'])->name('authenticate_otp');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('privacy_policy', function(){
    return view('privacy_policy');
});
Route::get('get-datatable', [PelangganController::class, 'datatables']);
Route::get('cek', function(){
    return view('generate');
});
// Route::get('get-api', [PelangganController::class,'getApi']);
Route::get('/import_posisi', [NamaPosisiController::class, 'importJsonFromFolder']);
Route::get('/import', [PelangganController::class, 'posisi']);
Route::get('/get-api', [PelangganController::class, 'getApi']);
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

    // kantor pusat
    Route::get('kantor_pusat', [KantorPusatController::class, 'index'])->name('kantor_pusat');
    Route::post('kantor_pusat/datatables', [KantorPusatController::class, 'datatables'])->name('kantor_pusat.datatables');
    Route::post('kantor_pusat/save', [KantorPusatController::class, 'save'])->name('kantor_pusat.save');
    Route::get('kantor_pusat/show/{id}', [KantorPusatController::class,'show' ])->name('kantor_pusat.show');
    Route::post('kantor_pusat/update', [KantorPusatController::class, 'update'])->name('kantor_pusat.update');
    Route::post('kantor_pusat/delete', [KantorPusatController::class, 'delete'])->name('kantor_pusat.delete');
    // end kantor pusat

    // kantor cabang
    Route::get('kantor_cabang', [KantorCabangController::class, 'index'])->name('kantor_cabang');
    Route::post('kantor_cabang/datatables', [KantorCabangController::class, 'datatables'])->name('kantor_cabang.datatables');
    Route::post('kantor_cabang/save', [KantorCabangController::class, 'save'])->name('kantor_cabang.save');
    Route::get('kantor_cabang/show/{id}', [KantorCabangController::class,'show'])->name('kantor_cabang.show');
    Route::post('kantor_cabang/update', [KantorCabangController::class, 'update'])->name('kantor_cabang.update');
    Route::post('kantor_cabang/delete', [KantorCabangController::class, 'delete'])->name('kantor_cabang.delete');
    // end kantor cabang

    // kantor wilayah
    Route::get('kantor_wilayah', [KantorWilayahController::class, 'index'])->name('kantor_wilayah');
    Route::post('kantor_wilayah/datatables', [KantorWilayahController::class, 'datatables'])->name('kantor_wilayah.datatables');
    Route::get('kantor_wilayah/show/{id}', [KantorWilayahController::class, 'show'])->name('kantor_wilayah.show');
    Route::post('kantor_wilayah/save', [KantorWilayahController::class, 'save'])->name('kantor_wilayah.save');
    Route::post('kantor_wilayah/update', [KantorWilayahController::class, 'update'])->name('kantor_wilayah.update');
    Route::post('kantor_wilayah/delete', [KantorWilayahController::class, 'delete'])->name('kantor_wilayah.delete');
    // end kantor wilayah

    // bagian kantor pusat
    Route::get('bagian_kantor_pusat', [BagianKantorPusatController::class, 'index'])->name('bagian_kantor_pusat');
    Route::post('bagian_kantor_pusat/datatables', [BagianKantorPusatController::class, 'datatables'])->name('bagian_kantor_pusat.datatables');
    Route::get('bagian_kantor_pusat/show/{id}', [BagianKantorPusatController::class, 'show'])->name('bagian_kantor_pusat.show');
    Route::post('bagian_kantor_pusat/save', [BagianKantorPusatController::class, 'save'])->name('bagian_kantor_pusat.save');
    Route::post('bagian_kantor_pusat/update', [BagianKantorPusatController::class, 'update'])->name('bagian_kantor_pusat.update');
    Route::post('bagian_kantor_pusat/delete', [BagianKantorPusatController::class, 'delete'])->name('bagian_kantor_pusat.delete');
    // end bagian kantor pusat

    // bagian kantor cabang
    Route::get('bagian_kantor_cabang', [BagianKantorCabangController::class, 'index'])->name('bagian_kantor_cabang');
    Route::post('bagian_kantor_cabang/datatables', [BagianKantorCabangController::class, 'datatables'])->name('bagian_kantor_cabang.datatables');
    Route::get('bagian_kantor_cabang/show/{id}', [BagianKantorCabangController::class,'show'])->name('bagian_kantor_cabang.show');
    Route::post('bagian_kantor_cabang/save', [BagianKantorCabangController::class, 'save'])->name('bagian_kantor_cabang.save');
    Route::post('bagian_kantor_cabang/update', [BagianKantorCabangController::class, 'update'])->name('bagian_kantor_cabang.update');
    Route::post('bagian_kantor_cabang/delete', [BagianKantorCabangController::class, 'delete'])->name('bagian_kantor_cabang.delete');
    // end bagian kantor cabang

    // bagian kantor wilayah
    Route::get('bagian_kantor_wilayah', [BagianKantorWilayahController::class, 'index'])->name('bagian_kantor_wilayah');
    Route::post('bagian_kantor_wilayah/datatables', [BagianKantorWilayahController::class, 'datatables'])->name('bagian_kantor_wilayah.datatables');
    Route::get('bagian_kantor_wilayah/show/{id}', [BagianKantorWilayahController::class,'show'])->name('bagian_kantor_wilayah.show');
    Route::post('bagian_kantor_wilayah/save', [BagianKantorWilayahController::class, 'save'])->name('bagian_kantor_wilayah.save');
    Route::post('bagian_kantor_wilayah/update', [BagianKantorWilayahController::class, 'update'])->name('bagian_kantor_wilayah.update');
    Route::post('bagian_kantor_wilayah/delete', [BagianKantorWilayahController::class, 'delete'])->name('bagian_kantor_wilayah.delete');
    // end bagian kantor wilayah

  // hari libur
    Route::get('hari_libur', [HolidayController::class, 'index'])->name('hari_libur');
    Route::get('hari_libur/create', [HolidayController::class, 'create'])->name('hari_libur.create');
    Route::get('hari_libur/datatables', [HolidayController::class ,'datatables'])->name('hari_libur.datatables');
    Route::post('hari_libur/save', [HolidayController::class,'save'])->name('hari_libur.save');
    Route::get('hari_libur/show/{id}', [HolidayController::class, 'show'])->name('hari_libur.show');
    Route::get('hari_libur/update/{id}',[HolidayController::class, 'update'])->name('hari_libur.update');
    Route::post('hari_libur/edit{id}',[HolidayController::class, 'edit'])->name('hari_libur.edit');
    Route::post('hari_libur/delete', [HolidayController::class, 'delete'])->name('hari_libur.delete');
    // end hari libur

    // mitra/pelanggan
    Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan');
    Route::post('pelanggan/log', [PelangganController::class, 'log'])->name('pelanggan.log');
    Route::get('pelanggan/datatables', [PelangganController::class, 'datatables'])->name('pelanggan.datatables');
    Route::post('pelanggan/get_posisi', [PelangganController::class, 'get_posisi'])->name('pelanggan.get_posisi');
    Route::get('pelanggan/create', [PelangganController::class , 'create'])->name('pelanggan.create');
    Route::post('pelanggan/save', [PelangganController::class, 'save'])->name('pelanggan.save');
    Route::get('pelanggan/show/{id}',[PelangganController::class, 'show'])->name('pelanggan.show');
    Route::get('pelanggan/edit/{id}', [PelangganController::class, 'edit'])->name('pelanggan.edit');
    Route::post('pelanggan/update', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::post('pelanggan/delete', [PelangganController::class, 'delete'])->name('pelanggan.delete');
    Route::get('pelanggan/sync', [PelangganController::class,'getApi'])->name('pelanggan.sync');
    Route::get('pelanggan/jabatan',[PelangganController::class,'jabatanData'])->name('pelanggan.jabatan');
    Route::get('pelanggan/last-sync', [PelangganController::class,'lastSync'])->name('pelanggan.last_sync');
    Route::get('pelanggan/back', [PelangganController::class, 'back']);
    // end mitra/pelanggan


    // chat
    Route::get('chat', [Chat::class, 'index'])->name('chat');
    Route::post('chat/notifikasi', [Chat::class, 'notifikasi'])->name('chat.notifikasi');
    Route::post('chat/riwayat_chat',  [Chat::class, 'riwayat_chat'])->name('chat.riwayat_chat');
    Route::post('chat/riwayat_chat_friend',  [Chat::class, 'riwayat_chat_friend'])->name('chat.riwayat_chat_friend');
    Route::post('chat/mulai_chat',  [Chat::class, 'mulai_chat'])->name('chat.mulai_chat');
    Route::post('chat/mulai_chat_friend',  [Chat::class, 'mulai_chat_friend'])->name('chat.mulai_chat_friend');
    Route::post('chat/kirim_chat',  [Chat::class, 'kirim_chat'])->name('chat.kirim_chat');
    Route::post('chat/cek_riwayat_chat', [Chat::class, 'cek_riwayat_chat'])->name('chat.cek_riwayat_chat');
    Route::post('chat/get_pengaduan', [Chat::class,'getPengaduan'])->name('chat_pengaduan');
    Route::post('chat/suara_chat', [Chat::class, 'suara_chat'])->name('chat.suara_chat');
    Route::get('chat/cek_pesan',[Chat::class,'cekPesan'])->name('chat.cek_pesan');

    // end chat

     // nama jabatan
    Route::get('nama_jabatan', [NamaPosisiController::class, 'index'])->name('nama_jabatan');
    Route::get('nama_jabatan/create', [NamaPosisiController::class, 'create'])->name('nama_jabatan.create');
    Route::get('nama_jabatan/show/{id}', [NamaPosisiController::class,'show'])->name('nama_jabatan.show');
    Route::post('nama_jabatan/store', [NamaPosisiController::class, 'store'])->name('nama_jabatan.save');
    Route::get('nama_jabatan/datatable', [NamaPosisiController::class, 'datatables'])->name('nama_jabatan.datatables');
    Route::get('nama_jabatan/edit/{id}', [NamaPosisiController::class, 'edit'])->name('nama_jabatan.edit');
    Route::post('nama_jabatan/update/{id}', [NamaPosisiController::class,'update'])->name('nama_jabatan.update');
    Route::post('nama_jabatan/get', [NamaPosisiController::class ,'getData'])->name('getNamaPosisi');
    Route::post('nama_jabatan/delete', [NamaPosisiController::class, 'destroy'])->name('nama_jabatan.delete');
    // end jabatan

    Route::get('/get-password', function (){
        return view('generate');
    });

    // cek cpu usage
    Route::get('cpu', [CPU::class, 'index'])->name('cpu');
    // end cek cpu usage

    // pengaduan
     Route::get('pengaduan', [PengaduanController::class, 'index'])->name('pengaduan');
    Route::get('pengaduan/buat', [PengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('pengaduan/simpan', [PengaduanController::class, 'save'])->name('pengaduan.save');
    Route::post('pengaduan/data_grid', [PengaduanController::class, 'data_grid'])->name('pengaduan.data_grid');
    Route::post('pengaduan/datatables', [PengaduanController::class, 'datatables'])->name('pengaduan.datatables');
    Route::post('pengaduan/pagination', [PengaduanController::class, 'pagination'])->name('pengaduan.pagination');
    Route::post('pengaduan/lampiran', [PengaduanController::class, 'lampiran'])->name('pengaduan.lampiran');
    Route::post('pengaduan/hapus_lampiran', [PengaduanController::class,'hapus_lampiran'])->name('pengaduan.hapus_lampiran');
    Route::post('pengaduan/approve', [PengaduanController::class, 'approve'])->name('pengaduan.approve');
    Route::post('pengaduan/finish', [PengaduanController::class, 'finish'])->name('pengaduan.finish');
    Route::post('pengaduan/form_tanggapan', [PengaduanController::class, 'form_tanggapan'])->name('pengaduan.form_tanggapan');
    Route::post('pengaduan/tanggapan', [PengaduanController::class, 'tanggapan'])->name('pengaduan.tanggapan');
    Route::post('pengaduan/alihkan', [PengaduanController::class, 'alihkan'])->name('pengaduan.alihkan');
    Route::get('pengaduan/show/{id}/{status}', [PengaduanController::class, 'show'])->name('pengaduan.show');
    Route::get('pengaduan/show-friend/{id}', [PengaduanController::class, 'showFriend'])->name('pengaduan.show_friend');
    Route::post('pengaduan/checked', [PengaduanController::class, 'checked'])->name('pengaduan.checked');
    Route::post('pengaduan/solved', [PengaduanController::class, 'solved'])->name('pengaduan.solved');
    Route::post('cek/resolve', [PengaduanController::class, 'cek_resolve'])->name('cek_resolve');
    Route::get('cek/sla', [PengaduanController::class, 'cek_sla'])->name('cek_sla');
    Route::get('cek/habis_sla', [PengaduanController::class, 'cek_habis_sla'])->name('cek_habis_sla');
    Route::post('cek/tanggapan', [PengaduanController::class, 'cek_tanggapan'])->name('cek_tanggapan');
    Route::post('pengaduan/jawaban', [PengaduanController::class,'jawaban'])->name('pengaduan.jawaban');
    Route::post('pengaduan/get-bagian-unit', [PengaduanController::class, 'getBagian'])->name('pengaduan.get-bagian-unit');

    Route::post('pengaduan/update', [PengaduanController::class,'update'])->name('pengaduan.update');
    Route::post('pengaduan/delete', [PengaduanController::class, 'delete'])->name('pengaduan.delete');
      Route::get('pengaduan/friend', [PengaduanController::class, 'friend'])->name('pengaduan.friend');
    Route::post('pengaduan/friend/data_grid', [PengaduanController::class, 'data_grid_friend'])->name('pangaduan.friend.data_grid');
    Route::post('pengaduan/klasifikasi', [PengaduanController::class, 'klasifikasi'])->name('pengaduan.klasifikasi');
    // end pengaduan

      // role
    Route::get('/role', [RoleAccountController::class, 'index'])->name('role.account');
    Route::get('/role/datatables',  [RoleAccountController::class, 'datatables'])->name('role.account.datatables');
    Route::get('/role/{id}/setting',  [RoleAccountController::class, 'setting'])->name('role.account.setting');
    Route::post('/role/update/setting',  [RoleAccountController::class, 'updateSetting'])->name('role.account.update.setting');
    // end role

    // menu
    Route::get('/menu',[RoleMenu::class, 'index'])->name('role.menu');
    Route::post('/menu/update', [RoleMenu::class, 'updateMenu'])->name('role.menu.update_order');
    Route::post('/menu/save', [RoleMenu::class, 'save'])->name('role.menu.save');
    Route::get('/menu/delete/{id}', [RoleMenu::class, 'delete'])->name('role.menu.delete');
    Route::get('/menu/{id}/detail', [RoleMenu::class,'detail'])->name('role.menu.detail');
    Route::post('/menu/{id}/update', [RoleMenu::class, 'update'])->name('role.menu.update');
    // end menu

     // setup whatsapp
    Route::get('setup_whatsapp', [SetupWhatsappController::class, 'index'])->name('setup_whatsapp');
    Route::post('setup_whatsapp/update/{id}', [SetupWhatsappController::class, 'update'])->name('setup_whatsapp.update');
    // end whatsApp

    // lupa password
    // Route::get('lupa_password', 'LupaPassword@index', [LupaPassword::class,'index'])->name('lupa_password');
    // Route::post('lupa_password/update', 'LupaPassword@update', [LupaPassword::class,'update'])->name('lupa_password.update');
    // end lupa password

      // laporan pengaduan
    Route::get('laporan_pengaduan',  [LaporanPengaduan::class, 'index'])->name('laporan_pengaduan');
    Route::post('laporan_pengaduan/cetak',[LaporanPengaduan::class, 'cetak'])->name('laporan_pengaduan.cetak');
    // end laporan pengaduan


    // notifikasi
    Route::get('notifikasi', [Notifikasi::class,'index'])->name('notifikasi');
    Route::post('notifikasi/datatables',  [Notifikasi::class, 'datatables'] )->name('notifikasi.datatables');
    Route::post('notifikasi/read_notifikasi',  [Notifikasi::class,'read_notifikasi'])->name('notifikasi.read_notifikasi');
    // end notifikasi

    // profil saya
    Route::get('profil',  [Profil::class, 'index'] )->name('profil');
    Route::post('profil/log',  [Profil::class, 'log'])->name('profil.log');
    Route::post('profil/upload',  [Profil::class,'upload'] )->name('profil.upload');
    Route::post('profil/update',  [Profil::class,'update'])->name('profil.update');
    Route::post('profil/ganti_password', [Profil::class, 'ganti_password'])->name('profil.ganti_password');
    // end profil saya


       // FAQ
    Route::get('faq', [FAQController::class, 'index'])->name('faq');
    Route::post('faq/pagination',  [FAQController::class, 'pagination'])->name('faq.pagination');
    Route::get('question/list/{id}', [FAQController::class, 'question'])->name('faq.Quest');
    Route::get('question/create/{id}', [FAQController::class, 'createQuestion'])->name('faq.createQuest');
    Route::post('faq/kategori/save', [FAQController::class, 'saveCategory'])->name('faq.save.kategori');
    Route::get('faq/question/edit/{id}', [FAQController::class, 'editQuest'])->name('faq.editQuest');
    Route::post('question/pagination/{id}',[FAQController::class, 'paginationQuest'])->name('faq.pagination.question');
    Route::post('faq/save',[FAQController::class, 'save'])->name('faq.save');
    Route::post('question/save', [FAQController::class, 'saveQuest'])->name('faq.saveQuest');
    Route::post('question/update/{id}', [FAQController::class, 'updateQuest'])->name('faq.updateQuest');
    Route::post('faq/update/{id}',[FAQController::class, 'update'])->name('faq.update.kategori');
    Route::post('faq/delete', [FAQController::class, 'delete'])->name('faq.delete');
    Route::post('question/delete', [FAQController::class, 'deleteQuest'])->name('faq.deleteQuest');
    // end FAQ

      // pertanyaan user
    Route::get('question/user', [PertanyaanFAQController::class, 'index'])->name('pertanyaan_faq');
    Route::get('question/user/delete/{id}', [PertanyaanFAQController::class, 'delete'])->name('pertanyaan.delete');
    // end pertanyaan user


    // route smtp
    Route::get('smtp',  [SMTP::class ,'index'])->name('smtp');
    Route::post('smtp/save',  [SMTP::class, 'save'])->name('smtp.save');
    Route::post('smtp/update', [SMTP::class, 'update'])->name('smtp.update');
    // end route smtp


    // pegawai sunfish
    Route::get('pegawai-sunfish', [PegawaiSunfishController::class, 'index'])->name('pegawai-sunfish');
    Route::get('pegawai-sunfish/datatables', [PegawaiSunfishController::class, 'datatables'])->name('pegawai-sunfish.datatables');
    Route::get('pegawai-sunfish/show/{id}', [PegawaiSunfishController::class,'show'])->name('pegawai-sunish.show');
    Route::post('pegawai-sunfish/sync-data',[PegawaiSunfishController::class,'syncData'])->name('pegawai-sunfish.sync');
    Route::get('pegawai-sunfish/last-sync-data',[PegawaiSunfishController::class,'lastSync'])->name('pegawai-sunfish.last_sync');

});

// Route::group(['middleware' => 'auth'], function () {});
