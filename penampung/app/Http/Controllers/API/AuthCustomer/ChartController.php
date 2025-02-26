<?php

namespace App\Http\Controllers\API\AuthCustomer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Utils\ComplaintUtilController;
use Illuminate\Http\Request;
use Auth;
use App\Models\API\Pengaduan;

class ChartController extends Controller
{
    public function getStatusComplaints(Request $request)
    {
        $customer = Auth::user();

        $complaints = Pengaduan::whereHas('employee', function ($query) {
                $query->where('status_pegawai', 'Aktif');
            })
            ->where([
                'delete_pengaduan' => 'N'
            ]);

        if ($customer->level_pegawai == 'Kepala Unit Kerja') {
            $complaints
                ->whereHas('employee', function ($query) use ($customer) {
                    if ($customer->kantor_pegawai == 'Kantor Pusat') {
                        $query->whereHas('headOfficeSection', function ($subQuery) use ($customer) {
                            $subQuery->where('id_kantor_pusat', $customer->multi_pegawai);
                        });
                    } else if ($customer->kantor_pegawai == 'Kantor Wilayah') {
                        $query->whereHas('regionalOfficeSection', function ($subQuery) use ($customer) {
                            $subQuery->where('id_kantor_wilayah', $customer->multi_pegawai);
                        });
                    } else if ($customer->kantor_pegawai == 'Kantor Cabang') {
                        $query->whereHas('branchOfficeSection', function ($subQuery) use ($customer) {
                            $subQuery->where('id_kantor_cabang', $customer->multi_pegawai);
                        });
                    }
                    // $query->where([
                    //     'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
                    //     'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
                    //     'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
                    // ]);
                })
                ->where('status_pengaduan', '!=', 'Checked');
        } else if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja') {
            $complaints
                ->whereHas('employee', function ($query) use ($customer) {
                    $query->where([
                        'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
                    ]);
                });
        } else {
            $complaints->where('id_pegawai', $customer->id_pegawai);
        }

        $complaints = $complaints
            ->whereYear('tgl_pengaduan', $request->year ?? date('Y'))
            ->get();

        $complaintUtilController = new ComplaintUtilController();
        return $complaintUtilController->getStatusComplaints($complaints);
    }

    public function getMonthlyComplaints(Request $request)
    {
        $customer = Auth::user();

        $complaints = Pengaduan::where([
                'delete_pengaduan' => 'N'
            ])
            ->whereYear('tgl_pengaduan', date('Y'));

        if ($customer->level_pegawai == 'Kepala Unit Kerja') {
            $complaints
                ->whereHas('employee', function ($query) use ($customer) {
                    if ($customer->kantor_pegawai == 'Kantor Pusat') {
                        $query->whereHas('headOfficeSection', function ($subQuery) use ($customer) {
                            $subQuery->where('id_kantor_pusat', $customer->multi_pegawai);
                        });
                    } else if ($customer->kantor_pegawai == 'Kantor Wilayah') {
                        $query->whereHas('regionalOfficeSection', function ($subQuery) use ($customer) {
                            $subQuery->where('id_kantor_wilayah', $customer->multi_pegawai);
                        });
                    } else if ($customer->kantor_pegawai == 'Kantor Cabang') {
                        $query->whereHas('branchOfficeSection', function ($subQuery) use ($customer) {
                            $subQuery->where('id_kantor_cabang', $customer->multi_pegawai);
                        });
                    }
                    // $query->where([
                    //     'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
                    //     'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
                    //     'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
                    // ]);
                })
                ->where('status_pengaduan', '!=', 'Checked');
        } else if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja') {
            $complaints
                ->whereHas('employee', function ($query) use ($customer) {
                    $query->where([
                        'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
                    ]);
                });
        } else {
            $complaints->where('id_pegawai', $customer->id_pegawai);
        }

        $complaints = $complaints
            ->whereYear('tgl_pengaduan', $request->year ?? date('Y'))
            ->get();

        $complaintUtilController = new ComplaintUtilController();
        return $complaintUtilController->getMonthlyComplaints($complaints);
    }
}
