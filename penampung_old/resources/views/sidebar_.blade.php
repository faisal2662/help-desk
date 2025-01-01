<?php 
    // $idAccount = Auth::user()->id_pegawai;

    $role_menu = DB::table('tb_role_menu')
    ->leftjoin('tb_role_user', 'tb_role_user.id_role_menu', 'tb_role_menu.id_role_menu')
    ->where('tb_role_user.id_account', Auth::user()->id_pegawai)
    ->where('tb_role_user.can_access', 'Y')
    ->select('tb_role_menu.id_role_menu', 'tb_role_menu.menu', 'tb_role_menu.type', 'tb_role_menu.route_name', 'tb_role_menu.icon', 'tb_role_user.can_access', 'tb_role_user.can_create', 'tb_role_user.can_update', 'tb_role_user.can_delete')
    ->where('tb_role_menu.is_deleted', 'N')
    ->orderBy('tb_role_menu.position', 'asc')
    ->get();
	
    $page = Request::segment(1);
    // if($page == ''){   
    //     header('Location: '.route('dashboard'));
    //     exit();
    // }

  $menu_notifikasi = array();

    $status_pengaduan = array(
    'Semua',
    'Pending',
    'Checked',
    'Approve',
    'Read',
    'Holding',
    'Moving',
    'On Progress',
    'Late',
    'Finish',
    );

    foreach($status_pengaduan as $data_status_pengaduan){
    
    $session_pegawai = DB::table('tb_pegawai')
    ->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();
    if($session_pegawai->count() > 0){
        
        foreach($session_pegawai as $data_session_pegawai);
        
        if($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai == 'Administrator'){

        if($data_status_pengaduan == 'Semua'){

            $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.delete_pengaduan','=','N')
            ->orderBy('tb_pengaduan.id_pengaduan','DESC')
            ->paginate(12);

            $menu_notifikasi['Semua'] = $pengaduan->count();

        }else{

            $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.delete_pengaduan','=','N')
            ->where('tb_pengaduan.status_pengaduan','=', $data_status_pengaduan)
            ->orderBy('tb_pengaduan.id_pengaduan','DESC')
            ->paginate(12);

            $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();

        }
        
        }else if($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai != 'Administrator'){

        if($data_status_pengaduan == 'Semua'){

            $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
            ->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
            ->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
            ->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
            ->where('tb_pengaduan.delete_pengaduan','=','N')
            ->orderBy('tb_pengaduan.id_pengaduan','DESC')
            ->paginate(12);

            $menu_notifikasi['Semua'] = $pengaduan->count();

        }else{

            $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
            ->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
            ->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
            ->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
            ->where('tb_pengaduan.delete_pengaduan','=','N')
            ->where('tb_pengaduan.status_pengaduan','=', $data_status_pengaduan)
            ->orderBy('tb_pengaduan.id_pengaduan','DESC')
            ->paginate(12);

            $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();

        }
        
        }else if($data_session_pegawai->sebagai_pegawai == 'Agent'){

        if($data_status_pengaduan == 'Semua'){

            $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
            ->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
            ->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
            ->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
            ->where('tb_pengaduan.status_pengaduan','!=','Pending')
            ->where('tb_pengaduan.status_pengaduan','!=','Checked')
            ->where('tb_pengaduan.delete_pengaduan','=','N')
            ->orderBy('tb_pengaduan.id_pengaduan','DESC')
            ->paginate(12);

            $menu_notifikasi['Semua'] = $pengaduan->count();

        }else{

            $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
            ->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
            ->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
            ->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
            ->where('tb_pengaduan.status_pengaduan','!=','Pending')
            ->where('tb_pengaduan.status_pengaduan','!=','Checked')
            ->where('tb_pengaduan.delete_pengaduan','=','N')
            ->where('tb_pengaduan.status_pengaduan','=', $data_status_pengaduan)
            ->orderBy('tb_pengaduan.id_pengaduan','DESC')
            ->paginate(12);

            $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();

        }
        
        }else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai != 'Staff'){
        
        if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Kepala Unit Kerja'){

            $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
            ->limit(1)
            ->get();

            if($unit_kerja->count() < 1){

            if($data_status_pengaduan == 'Semua'){

                $pengaduan = DB::table('tb_pengaduan')
                ->whereRaw('
                tb_pengaduan.id_pegawai IN (
                    Select
                    tb_pegawai.id_pegawai
                    From
                    tb_pegawai
                    Where
                    tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
                    tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
                    tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
                    tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
                )
                ')
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->where('tb_pengaduan.status_pengaduan','!=','Pending')
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi['Semua'] = $pengaduan->count();

            }else{

                $pengaduan = DB::table('tb_pengaduan')
                ->whereRaw('
                tb_pengaduan.id_pegawai IN (
                    Select
                    tb_pegawai.id_pegawai
                    From
                    tb_pegawai
                    Where
                    tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
                    tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
                    tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
                    tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
                )
                ')
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->where('tb_pengaduan.status_pengaduan','!=','Pending')
                ->where('tb_pengaduan.status_pengaduan','=', $data_status_pengaduan)
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();

            }

            }else{

            if($data_status_pengaduan == 'Semua'){

                $pengaduan = DB::table('tb_pengaduan')
                ->whereRaw('
                tb_pengaduan.id_pegawai IN (
                    Select
                    tb_pegawai.id_pegawai
                    From
                    tb_pegawai
                    Where
                    tb_pegawai.kantor_pegawai IN (
                        SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    ) And
                    tb_pegawai.id_bagian_kantor_pusat IN (
                        SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    ) And
                    tb_pegawai.id_bagian_kantor_cabang IN (
                        SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    ) And
                    tb_pegawai.id_bagian_kantor_wilayah IN (
                        SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    )
                )
                ')
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->where('tb_pengaduan.status_pengaduan','!=','Pending')
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi['Semua'] = $pengaduan->count();

            }else{

                $pengaduan = DB::table('tb_pengaduan')
                ->whereRaw('
                tb_pengaduan.id_pegawai IN (
                    Select
                    tb_pegawai.id_pegawai
                    From
                    tb_pegawai
                    Where
                    tb_pegawai.kantor_pegawai IN (
                        SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    ) And
                    tb_pegawai.id_bagian_kantor_pusat IN (
                        SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    ) And
                    tb_pegawai.id_bagian_kantor_cabang IN (
                        SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    ) And
                    tb_pegawai.id_bagian_kantor_wilayah IN (
                        SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
                        delete_kepala_unit_kerja = "N" And 
                        id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
                    )
                )
                ')
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->where('tb_pengaduan.status_pengaduan','!=','Pending')
                ->where('tb_pengaduan.status_pengaduan','=', $data_status_pengaduan)
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();

            }

            }
            
            }else{

                if($data_status_pengaduan == 'Semua'){

                $pengaduan = DB::table('tb_pengaduan')
                ->whereRaw('
                    tb_pengaduan.id_pegawai IN (
                    Select
                        tb_pegawai.id_pegawai
                    From
                        tb_pegawai
                    Where
                        tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
                        tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
                        tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
                        tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
                    )
                ')
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi['Semua'] = $pengaduan->count();

                }else{

                $pengaduan = DB::table('tb_pengaduan')
                ->whereRaw('
                    tb_pengaduan.id_pegawai IN (
                    Select
                        tb_pegawai.id_pegawai
                    From
                        tb_pegawai
                    Where
                        tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
                        tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
                        tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
                        tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
                    )
                ')
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->where('tb_pengaduan.status_pengaduan','=', $data_status_pengaduan)
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();

                }
                
            }

        }else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Staff'){

            if($data_status_pengaduan == 'Semua'){

                $pengaduan = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai','=', $data_session_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi['Semua'] = $pengaduan->count();

            }else{

                $pengaduan = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai','=', $data_session_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan','=','N')
                ->where('tb_pengaduan.status_pengaduan','=', $data_status_pengaduan)
                ->orderBy('tb_pengaduan.id_pengaduan','DESC')
                ->paginate(12);

                $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();

            }

        }
        
    }

    }
 ?>
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
            @foreach ($role_menu as $rm)
                @if ($rm->type == 'TITLE')
                    <li class="nav-item">
                        <p>&nbsp;</p>
                        <span class="menu-title">{{ $rm->menu }}</span>
                    </li>
                @else
                    @php

                        $arr = ["Pengaduan", "Pengaduan Pending", "Pengaduan Checked", "Pengaduan Approve", "Pengaduan Read", "Pengaduan On Progress", "Pengaduan SLA", "Pengaduan Finish", "Pengaduan Late"];
                    @endphp
                    @if (in_array($rm->menu, $arr))
                        @php
                            $menu = str_replace("SLA", "Holding", str_replace("Pengaduan ", "", $rm->menu));
                        @endphp
                        <!--@if ($rm->menu == 'Create Pengaduan')-->
                        <!--    <li class="nav-item {{ Request()->filter == 'Semua' ? 'active' : '' }}">-->
                        <!--        <a class="nav-link" href="{{ Route::has($rm->route_name) ? Route("$rm->route_name")."?filter=Semua" : '' }}">-->
                        <!--            <i class='{{ $rm->icon }} menu-icon'></i>-->
                        <!--            <span class="menu-title">{{ $rm->menu }}</span>-->
                        <!--        </a>-->
                        <!--    </li>-->
                        <!--@else-->
                        <!--    <li class="nav-item">-->
                        <!--        <a class="nav-link" href="{{ Route::has($rm->route_name) ? Route('$rm->route_name') . '?filter=' . $menu : '#' }}">-->
                        <!--            <i class='menu-icon'></i>-->
                        <!--            <span class="menu-title">{{ $rm->menu }}{{ $menu }}</span>-->
                        <!--            @if ($menu != 'Finish')-->
                        <!--                {{-- @if ($menu_notifikasi[$menu] > 0)-->
                        <!--                    &nbsp;&nbsp;&nbsp;-->
                        <!--                    <span class="badge badge-danger" style="border: 2px solid #ffffff;">-->
                        <!--                        <?= number_format($menu_notifikasi[$menu]) ?>-->
                        <!--                    </span>-->
                        <!--                @endif                                            --}}-->
                        <!--            @endif-->
                        <!--        </a>-->
                        <!--    </li>-->
                        <!--@endif-->
                         @if ($rm->menu == 'Pengaduan')
                   
                        <li class="nav-item {{ Request()->filter == 'Semua' ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('pengaduan') . '?filter=Semua'}}">
                                <i class='{{ $rm->icon }} menu-icon'></i>
                                <span class="menu-title">{{ $rm->menu }}</span>
                            </a>
                        </li>
                      
                    @endif
                    @else
                        <li class="nav-item" {{ $page == $rm->route_name ? 'active' : '' }}>
                            <a class="nav-link" href="{{ Route::has($rm->route_name) ? Route("$rm->route_name") : '#' }}">
                                <i class='{{ $rm->icon }} menu-icon'></i>
                                <span class="menu-title">{{ $rm->menu }}</span>
                            </a>
                        </li>
                    @endif
                @endif
                
            @endforeach
        </ul>

      </nav>
      <!-- partial -->