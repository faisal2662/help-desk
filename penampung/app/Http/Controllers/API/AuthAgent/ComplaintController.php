<?php

namespace App\Http\Controllers\API\AuthAgent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\SendNotificationController;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use Mail;
use Log;
use Exception;
use App\Models\API\Pengaduan;
use App\Models\API\Jawaban;
use App\Models\API\Mengetahui;
use App\Models\API\Dibaca;
use App\Models\API\Alihkan;
use App\Models\API\Notifikasi;
use App\Models\API\Pegawai;

class ComplaintController extends Controller
{
    private $complaintStatuses = ['Approve', 'Read', 'On Progress', 'Holding', 'Moving', 'Late', 'Finish'];

    public function getComplaints()
    {
        $agent = Auth::user();

        $complaints = Pengaduan::with([
                'employee',
                'headOfficeSection.headOffice',
                'regionalOfficeSection.regionalOffice',
                'branchOfficeSection.branchOffice',
                'knows.employee',
                'reads',
                'done',
                'answers'
            ])
            ->where([
                'id_bagian_kantor_pusat' => $agent->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $agent->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $agent->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereIn('status_pengaduan', $this->complaintStatuses)
            ->orderBy('tgl_pengaduan', 'DESC')
            ->get();

        return $complaints;
    }

    public function getComplaint($id)
    {
        $agent = Auth::user();

        $complaint = Pengaduan::with([
                'employee.headOfficeSection.headOffice',
                'employee.regionalOfficeSection.regionalOffice',
                'employee.branchOfficeSection.branchOffice',
                'employee.headOffice',
                'employee.regionalOffice',
                'employee.branchOffice',
                'headOfficeSection.headOffice',
                'regionalOfficeSection.regionalOffice',
                'branchOfficeSection.branchOffice',
                'knows.employee',
                'reads',
                'done',
                'attachments' => function ($query) {
                    $query->where('delete_lampiran', 'N');
                },
                'answers.employee',
                'answers.responses'
            ])
            ->where([
                'id_pengaduan' => $id,
                'id_bagian_kantor_pusat' => $agent->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $agent->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $agent->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereIn('status_pengaduan', $this->complaintStatuses)
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        $hasReadEmployee = Dibaca::where([
                'id_pengaduan' => $complaint->id_pengaduan,
                'id_pegawai' => $agent->id_pegawai,
                'delete_dibaca' => 'N'
            ])
            ->count() > 0;

        if (!$hasReadEmployee) {
            Dibaca::create([
                'id_pengaduan' => $complaint->id_pengaduan,
                'id_pegawai' => $agent->id_pegawai,
                'tgl_dibaca' => date('Y-m-d H:i:s')
            ]);
            Notifikasi::create([
                'id_pegawai' => $complaint->id_pegawai,
                'nama_notifikasi' => 'Pengaduan Read',
                'keterangan_notifikasi' => 'Pengaduan sudah di read oleh Agent.',
                'warna_notifikasi' => 'info',
                'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                'status_notifikasi' => 'Delivery',
                'tgl_notifikasi' => date('Y-m-d H:i:s')
            ]);
        }

        if ($complaint->status_pengaduan == 'Approve') {
            SendNotificationController::complaintBeReadToCustomer($complaint->id_pengaduan);

            $complaint->update([
                'status_pengaduan' => 'Read'
            ]);

            $complaint = Pengaduan::with([
                    'employee.headOfficeSection.headOffice',
                    'employee.regionalOfficeSection.regionalOffice',
                    'employee.branchOfficeSection.branchOffice',
                    'employee.headOffice',
                    'employee.regionalOffice',
                    'employee.branchOffice',
                    'headOfficeSection.headOffice',
                    'regionalOfficeSection.regionalOffice',
                    'branchOfficeSection.branchOffice',
                    'knows.employee',
                    'reads',
                    'done',
                    'attachments' => function ($query) {
                        $query->where('delete_lampiran', 'N');
                    },
                    'answers.employee',
                    'answers.responses'
                ])
                ->find($id);
        }

        return $complaint;
    }

    public function createAnswer($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keterangan_jawaban' => 'required',
            'lampiran_jawaban' => 'nullable|image',
            'sla' => 'nullable|bool',
            'alasan_sla_jawaban' => 'nullable|string',
            'durasi_sla_jawaban' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $agent = Auth::user();

        $complaint = Pengaduan::where([
                'id_pengaduan' => $id,
                'id_bagian_kantor_pusat' => $agent->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $agent->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $agent->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereIn('status_pengaduan', $this->complaintStatuses)
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        $data = [
            'id_pengaduan' => $complaint->id_pengaduan,
            'id_pegawai' => $agent->id_pegawai,
            'keterangan_jawaban' => $request->keterangan_jawaban,
            'tgl_jawaban' => date('Y-m-d H:i:s'),
            'durasi_sla_jawaban' => '0000-00-00'
        ];

        if ($request->hasFile('foto_jawaban')) {
            $attachment = 'foto_jawaban_'.date('Ymd_His.').$request->file('foto_jawaban')->getClientOriginalExtension();
            $request->file('foto_jawaban')->move('images', $attachment);
            $data['foto_jawaban'] = url('images/'.$attachment);
        } else {
            $data['foto_jawaban'] = 'logos/image.png';
        }

        if ($request->sla) {
            $data['sla_jawaban'] = 'Ya';
            $data['durasi_sla_jawaban'] = date('Y-m-d H:i:s', strtotime('+'.$request->durasi_sla_jawaban.' days'));
            $data['alasan_sla_jawaban'] = $request->alasan_sla_jawaban;
        } else {
            $data['sla_jawaban'] = 'Tidak';
            $data['durasi_sla_jawaban'] = date('Y-m-d H:i:s');
            $data['alasan_sla_jawaban'] = 'Tidak ada ...';
        }

        $notifications = [];
        $employees = Pegawai::where([
                'sebagai_pegawai' => 'Mitra/Pelanggan',
                'kantor_pegawai' => $agent->kantor_pegawai,
                'id_bagian_kantor_pusat' => $agent->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $agent->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $agent->id_bagian_kantor_cabang,
                'status_pegawai' => 'Aktif',
                'delete_pegawai' => 'N'
            ])
            ->get();
        if ($request->sla) {
            foreach ($employees as $employee) {
                $notifications[] = [
                    'id_pegawai' => $employee->id_pegawai,
                    'nama_notifikasi' => 'Pengaduan Holding',
                    'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" tertunda sementara.',
                    'warna_notifikasi' => 'danger',
                    'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                    'status_notifikasi' => 'Delivery',
                    'tgl_notifikasi' => date('Y-m-d H:i:s')
                ];
            }
        } else {
            $emails = [];
            foreach ($employees as $employee) {
                $emails[] = $employee->email_pegawai;
                $notifications[] = [
                    'id_pegawai' => $employee->id_pegawai,
                    'nama_notifikasi' => 'Pengaduan On Progress',
                    'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" sedang dalam proses.',
                    'warna_notifikasi' => 'primary',
                    'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                    'status_notifikasi' => 'Delivery',
                    'tgl_notifikasi' => date('Y-m-d H:i:s')
                ];
            }

            Mail::send('pengaduan.email_on_progress', ['id_pengaduan' => $complaint->id_pengaduan], function ($message) use ($emails) {
                $message->to($emails)
                        ->subject('Pengaduan Baru (On Progress)')
                        ->from('helpdesk@cnplus.id', 'Helpdesk');
            });
        }

        DB::beginTransaction();

        try {
            Jawaban::create($data);
            Notifikasi::insert($notifications);
            $complaint->update([
                'status_pengaduan' => $request->sla ? 'Holding' : 'On Progress'
            ]);
            $complaint = Pengaduan::with([
                    'employee.headOfficeSection.headOffice',
                    'employee.regionalOfficeSection.regionalOffice',
                    'employee.branchOfficeSection.branchOffice',
                    'employee.headOffice',
                    'employee.regionalOffice',
                    'employee.branchOffice',
                    'headOfficeSection.headOffice',
                    'regionalOfficeSection.regionalOffice',
                    'branchOfficeSection.branchOffice',
                    'knows.employee',
                    'reads',
                    'done',
                    'attachments' => function ($query) {
                        $query->where('delete_lampiran', 'N');
                    },
                    'answers.employee',
                    'answers.responses'
                ])
                ->find($id);

            DB::commit();

            if ($request->sla) {
                SendNotificationController::complaintHoldingToCustomer($complaint->id_pengaduan);
            } else {
                SendNotificationController::complaintAnsweredToCustomer($complaint->id_pengaduan);
            }

            return $complaint;
        } catch (Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }

    public function transferComplaint($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kantor_alihkan' => 'required|string|in:Kantor Pusat,Kantor Wilayah,Kantor Cabang',
            'id_bagian_kantor_pusat' => 'nullable|integer',
            'id_bagian_kantor_wilayah' => 'nullable|integer',
            'id_bagian_kantor_cabang' => 'nullable|integer',
            'keterangan_alihkan' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $agent = Auth::user();

        $complaint = Pengaduan::where([
                'id_pengaduan' => $id,
                'id_bagian_kantor_pusat' => $agent->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $agent->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $agent->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereIn('status_pengaduan', $this->complaintStatuses)
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        $dataTransfer = [
            'id_pengaduan' => $complaint->id_pengaduan,
            'id_pegawai' => $agent->id_pegawai,
            'tgl_alihkan' => date('Y-m-d H:i:s')
        ];

        $dataComplaint = [
            'kantor_pengaduan' => $request->kantor_alihkan,
            'id_bagian_kantor_pusat' => $request->id_bagian_kantor_pusat ?? 0,
            'id_bagian_kantor_wilayah' => $request->id_bagian_kantor_wilayah ?? 0,
            'id_bagian_kantor_cabang' => $request->id_bagian_kantor_cabang ?? 0,
            'status_pengaduan' => 'Moving',
            'respon_pengaduan' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ];

        $notifications = [];
        $employees = Pegawai::where(function ($query) use ($complaint, $request) {
                $query
                    ->where([
                        'sebagai_pegawai' => 'Mitra/Pelanggan',
                        'id_bagian_kantor_pusat' => $complaint->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $complaint->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $complaint->id_bagian_kantor_cabang,
                    ])
                    ->orWhere([
                        'sebagai_pegawai' => 'Petugas',
                        'id_bagian_kantor_pusat' => $request->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $request->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $request->id_bagian_kantor_cabang,
                    ])
                    ->orWhere([
                        'sebagai_pegawai' => 'Agent',
                        'id_bagian_kantor_pusat' => $request->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $request->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $request->id_bagian_kantor_cabang,
                    ]);
            })
            ->where([
                'status_pegawai' => 'Aktif',
                'delete_pegawai' => 'N'
            ])
            ->get();
        foreach ($employees as $employee) {
            $notifications[] = [
                'id_pegawai' => $employee->id_pegawai,
                'nama_notifikasi' => 'Pengaduan Moving',
                'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" telah dialihkan.',
                'warna_notifikasi' => 'danger',
                'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                'status_notifikasi' => 'Delivery',
                'tgl_notifikasi' => date('Y-m-d H:i:s')
            ];
        }

        DB::beginTransaction();

        try {
            Alihkan::create($dataTransfer);
            Notifikasi::insert($notifications);
            $complaint->update($dataComplaint);
            $complaint = Pengaduan::with([
                    'employee.headOfficeSection.headOffice',
                    'employee.regionalOfficeSection.regionalOffice',
                    'employee.branchOfficeSection.branchOffice',
                    'employee.headOffice',
                    'employee.regionalOffice',
                    'employee.branchOffice',
                    'headOfficeSection.headOffice',
                    'regionalOfficeSection.regionalOffice',
                    'branchOfficeSection.branchOffice',
                    'knows.employee',
                    'reads',
                    'done',
                    'attachments' => function ($query) {
                        $query->where('delete_lampiran', 'N');
                    },
                    'answers.employee',
                    'answers.responses'
                ])
                ->find($id);

            DB::commit();

            SendNotificationController::complaintTransferToCustomer($complaint->id_pengaduan);

            return $complaint;
        } catch (Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }
}
