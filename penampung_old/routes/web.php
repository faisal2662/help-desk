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
use App\Http\Controllers\LaporanPengaduan;
use App\Http\Controllers\Chat;
use App\Http\Controllers\RoleAccountController;
use App\Http\Controllers\RoleMenu;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CPU;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\SMTP;
use App\Http\Controllers\Profil;
 
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
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

    // kantor pusat
    Route::get('kantor_pusat', [KantorPusatController::class, 'index'])->name('kantor_pusat');
    Route::post('kantor_pusat/datatables', [KantorPusatController::class, 'datatables'])->name('kantor_pusat.datatables');
    Route::post('kantor_pusat/save', [KantorPusatController::class, 'save'])->name('kantor_pusat.save');
    Route::post('kantor_pusat/update', [KantorPusatController::class, 'update'])->name('kantor_pusat.update');
    Route::post('kantor_pusat/delete', [KantorPusatController::class, 'delete'])->name('kantor_pusat.delete');
    // end kantor pusat

    // kantor cabang
    Route::get('kantor_cabang', [KantorCabangController::class, 'index'])->name('kantor_cabang');
    Route::post('kantor_cabang/datatables', [KantorCabangController::class, 'datatables'])->name('kantor_cabang.datatables');
    Route::post('kantor_cabang/save', [KantorCabangController::class, 'save'])->name('kantor_cabang.save');
    Route::post('kantor_cabang/update', [KantorCabangController::class, 'update'])->name('kantor_cabang.update');
    Route::post('kantor_cabang/delete', [KantorCabangController::class, 'delete'])->name('kantor_cabang.delete');
    // end kantor cabang

    // kantor wilayah
    Route::get('kantor_wilayah', [KantorWilayahController::class, 'index'])->name('kantor_wilayah');
    Route::post('kantor_wilayah/datatables', [KantorWilayahController::class, 'datatables'])->name('kantor_wilayah.datatables');
    Route::post('kantor_wilayah/save', [KantorWilayahController::class, 'save'])->name('kantor_wilayah.save');
    Route::post('kantor_wilayah/update', [KantorWilayahController::class, 'update'])->name('kantor_wilayah.update');
    Route::post('kantor_wilayah/delete', [KantorWilayahController::class, 'delete'])->name('kantor_wilayah.delete');
    // end kantor wilayah

    // bagian kantor pusat
    Route::get('bagian_kantor_pusat', [BagianKantorPusatController::class, 'index'])->name('bagian_kantor_pusat');
    Route::post('bagian_kantor_pusat/datatables', [BagianKantorPusatController::class, 'datatables'])->name('bagian_kantor_pusat.datatables');
    Route::post('bagian_kantor_pusat/save', [BagianKantorPusatController::class, 'save'])->name('bagian_kantor_pusat.save');
    Route::post('bagian_kantor_pusat/update', [BagianKantorPusatController::class, 'update'])->name('bagian_kantor_pusat.update');
    Route::post('bagian_kantor_pusat/delete', [BagianKantorPusatController::class, 'delete'])->name('bagian_kantor_pusat.delete');
    // end bagian kantor pusat

    // bagian kantor cabang
    Route::get('bagian_kantor_cabang', [BagianKantorCabangController::class, 'index'])->name('bagian_kantor_cabang');
    Route::post('bagian_kantor_cabang/datatables', [BagianKantorCabangController::class, 'datatables'])->name('bagian_kantor_cabang.datatables');
    Route::post('bagian_kantor_cabang/save', [BagianKantorCabangController::class, 'save'])->name('bagian_kantor_cabang.save');
    Route::post('bagian_kantor_cabang/update', [BagianKantorCabangController::class, 'update'])->name('bagian_kantor_cabang.update');
    Route::post('bagian_kantor_cabang/delete', [BagianKantorCabangController::class, 'delete'])->name('bagian_kantor_cabang.delete');
    // end bagian kantor cabang

    // bagian kantor wilayah
    Route::get('bagian_kantor_wilayah', [BagianKantorWilayahController::class, 'index'])->name('bagian_kantor_wilayah');
    Route::post('bagian_kantor_wilayah/datatables', [BagianKantorWilayahController::class, 'datatables'])->name('bagian_kantor_wilayah.datatables');
    Route::post('bagian_kantor_wilayah/save', [BagianKantorWilayahController::class, 'save'])->name('bagian_kantor_wilayah.save');
    Route::post('bagian_kantor_wilayah/update', [BagianKantorWilayahController::class, 'update'])->name('bagian_kantor_wilayah.update');
    Route::post('bagian_kantor_wilayah/delete', [BagianKantorWilayahController::class, 'delete'])->name('bagian_kantor_wilayah.delete');
    // end bagian kantor wilayah

    
    // mitra/pelanggan
    Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan');
    Route::post('pelanggan/log', [PelangganController::class, 'log'])->name('pelanggan.log');
    Route::post('pelanggan/datatables', [PelangganController::class, 'datatables'])->name('pelanggan.datatables');
    Route::get('pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create');
    Route::post('pelanggan/save', [PelangganController::class, 'save'])->name('pelanggan.save');
    Route::get('pelanggan/show/{id}', [PelangganController::class, 'show'])->name('pelanggan.show');
    Route::get('pelanggan/edit/{id}', [PelangganController::class, 'edit'])->name('pelanggan.edit');
    Route::post('pelanggan/update', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::post('pelanggan/delete', [PelangganController::class, 'delete'])->name('pelanggan.delete');
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
    // end chat
    
    
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
    Route::post('pengaduan/checked', [PengaduanController::class, 'checked'])->name('pengaduan.checked');
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
    Route::post('/menu/save', [RoleMenu::class, 'save'])->name('role.menu.save');
    Route::get('/menu/{id}/delete', [RoleMenu::class, 'delete'])->name('role.menu.delete');
    Route::get('/menu/{id}/detail', [RoleMenu::class,'detail'])->name('role.menu.detail');
    Route::post('/menu/{id}/update', [RoleMenu::class, 'update'])->name('role.menu.update');
    // end menu
    
    
    // lupa password
    Route::get('lupa_password', 'LupaPassword@index', [LupaPassword::class,'index'])->name('lupa_password');
    Route::post('lupa_password/update', 'LupaPassword@update', [LupaPassword::class,'update'])->name('lupa_password.update');
    // end lupa password
    
      // laporan pengaduan
    Route::get('laporan_pengaduan',  [LaporanPengaduan::class, 'index'])->name('laporan_pengaduan');
    Route::post('laporan_pengaduan/cetak',[LaporanPengaduan::class, 'cetak'])->name('laporan_pengaduan.cetak');
    // end laporan pengaduan
    
    
    // notifikasi
    Route::get('notifikasi', [Notifikasi::class,'index'])->name('notifikasi');
    Route::post('notifikasi/datatables',  [Notifikasi::class, 'datatables'] )->name('notifikasi.datatables');
    Route::post('notifikasi/read_notifikasi',  [Notifikasi::class,'read_notifikas'])->name('notifikasi.read_notifikasi');
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
    
    // route smtp
    Route::get('smtp',  [SMTP::class ,'index'])->name('smtp');
    Route::post('smtp/save',  [SMTP::class, 'save'])->name('smtp.save');
    Route::post('smtp/update', [SMTP::class, 'updata'])->name('smtp.update');
    // end route smtp
    

    
});

Route::group(['middleware' => 'auth'], function () {});
