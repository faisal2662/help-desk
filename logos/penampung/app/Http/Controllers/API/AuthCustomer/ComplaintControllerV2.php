<?php

namespace App\Http\Controllers\API\AuthCustomer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\SendNotificationController;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use Mail;
use Log;
use App\Models\API\Pengaduan;
use App\Models\API\Selesai;
use App\Models\API\Jawaban;
use App\Models\API\Tanggapan;
use App\Models\API\Mengetahui;
use App\Models\API\Notifikasi;
use App\Models\API\Pegawai;
use App\Models\API\Kontak;
use App\Models\API\LogKontak;

class ComplaintControllerV2 extends Controller
{
    public function getComplaints()
    {
        $customer = Auth::user();

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
                ->where('status_pengaduan', '!=', 'Pending');
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

        $complaints = $complaints->orderBy('tgl_pengaduan', 'DESC')->get();

        return $complaints;
    }

    public function getComplaint($id)
    {
        $customer = Auth::user();

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
                'delete_pengaduan' => 'N'
            ]);

        if ($customer->level_pegawai == 'Kepala Unit Kerja') {
            $complaint
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
                });
        } else if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja') {
            $complaint
                ->whereHas('employee', function ($query) use ($customer) {
                    $query->where([
                        'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
                    ]);
                });
        } else {
            $complaint->where('id_pegawai', $customer->id_pegawai);
        }

        $complaint = $complaint->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        return $complaint;
    }

    public function createComplaint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kantor_pengaduan' => 'required|string|in:Kantor Pusat,Kantor Wilayah,Kantor Cabang',
            'id_bagian_kantor_pusat' => 'nullable|integer',
            'id_bagian_kantor_wilayah' => 'nullable|integer',
            'id_bagian_kantor_cabang' => 'nullable|integer',
            'nama_pengaduan' => 'required|max:255',
            'keterangan_pengaduan' => 'nullable',
            'klasifikasi_pengaduan' => 'nullable|string|in:High,Medium,Low',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $customer = Auth::user();

        $data = [
            'id_pegawai' => $customer->id_pegawai,
            'kantor_pengaduan' => $request->kantor_pengaduan,
            'id_bagian_kantor_pusat' => $request->id_bagian_kantor_pusat ?? 0,
            'id_bagian_kantor_wilayah' => $request->id_bagian_kantor_wilayah ?? 0,
            'id_bagian_kantor_cabang' => $request->id_bagian_kantor_cabang ?? 0,
            'nama_pengaduan' => $request->nama_pengaduan,
            'keterangan_pengaduan' => $request->keterangan_pengaduan,
            'klasifikasi_pengaduan' => $request->klasifikasi_pengaduan,
            'status_pengaduan' => 'Pending',
            'respon_pengaduan' => date('Y-m-d H:i:s'),
            'tgl_pengaduan' => date('Y-m-d H:i:s')
        ];

        if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja') {
            $data['status_pengaduan'] = 'Checked';
        }

        $attachments = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $index => $attachment) {
                $fileName = 'lampiran_'.date('Ymd_His_').$index.'.'.$attachment->getClientOriginalExtension();
                $attachment->move('images', $fileName);
                $attachments[] = [
                    'file_lampiran' => url('images/'.$fileName)
                ];
            }
        }

        DB::beginTransaction();

        try {
            $complaint = Pengaduan::create($data);
            $complaint->attachments()->createMany($attachments);

            $complaint = Pengaduan::with([
                    'employee',
                    'headOfficeSection.headOffice',
                    'regionalOfficeSection.regionalOffice',
                    'branchOfficeSection.branchOffice',
                    'knows',
                    'done',
                    'answers'
                ])
                ->findOrFail($complaint->id_pengaduan);

            $notifications = [];
            $emails = [];
            $employees = Pegawai::where([
                    'level_pegawai' => 'Kepala Bagian Unit Kerja',
                    'kantor_pegawai' => $customer->kantor_pegawai,
                    'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
                    'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
                    'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
                    'status_pegawai' => 'Aktif',
                    'delete_pegawai' => 'N',
                ])
                ->where(function ($query) {
                    $query
                        ->where('level_pegawai', '!=', 'Kepala Unit Kerja')
                        ->orWhere('level_pegawai', 'Kepala Bagian Unit Kerja');
                })
                ->where('id_pegawai', '!=', $customer->id_pegawai)
                ->get();
            foreach ($employees as $employee) {
                $emails[] = $employee->email_pegawai;
                $notifications[] = [
                    'id_pegawai' => $employee->id_pegawai,
                    'nama_notifikasi' => 'Pengaduan Pending',
                    'keterangan_notifikasi' => 'Pengaduan baru "'.$complaint->nama_pengaduan.'" telah diajukan oleh '.$employee->nama_pegawai,
                    'warna_notifikasi' => 'warning',
                    'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                    'status_notifikasi' => 'Delivery',
                    'tgl_notifikasi' => date('Y-m-d H:i:s')
                ];
            }
            Notifikasi::insert($notifications);
            // Mail::send('pengaduan.email_pending', ['id_pengaduan' => $complaint->id_pengaduan], function ($message) use ($emails) {
            //     $message->to($emails)
            //             ->subject('Pengaduan Baru (Pending)')
            //             ->from('helpdesk@cnplus.id', 'Helpdesk');
            // });

            DB::commit();

            if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja') {
                SendNotificationController::complaintCreatedToHead($complaint->id_pengaduan);
            } else if ($customer->level_pegawai == 'Staff') {
                SendNotificationController::complaintCreatedToUnit($complaint->id_pengaduan);
            }

            return response()->json($complaint, 201);
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }

    public function updateComplaint($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kantor_pengaduan' => 'required|string|in:Kantor Pusat,Kantor Wilayah,Kantor Cabang',
            'id_bagian_kantor_pusat' => 'nullable|integer',
            'id_bagian_kantor_wilayah' => 'nullable|integer',
            'id_bagian_kantor_cabang' => 'nullable|integer',
            'nama_pengaduan' => 'required|max:255',
            'keterangan_pengaduan' => 'nullable',
            'klasifikasi_pengaduan' => 'nullable|string|in:High,Medium,Low',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $customer = Auth::user();

        $complaint = Pengaduan::where([
                'id_pengaduan' => $id,
                'delete_pengaduan' => 'N'
            ]);

        if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja') {
            $complaint
                ->whereHas('employee', function ($query) use ($customer) {
                    $query->where([
                        'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
                    ]);
                });
        } else {
            $complaint->where('id_pegawai', $customer->id_pegawai);
        }

        $complaint = $complaint->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.',
            ], 404);
        }

        $data = [
            'kantor_pengaduan' => $request->kantor_pengaduan,
            'id_bagian_kantor_pusat' => $request->id_bagian_kantor_pusat ?? 0,
            'id_bagian_kantor_wilayah' => $request->id_bagian_kantor_wilayah ?? 0,
            'id_bagian_kantor_cabang' => $request->id_bagian_kantor_cabang ?? 0,
            'nama_pengaduan' => $request->nama_pengaduan,
            'keterangan_pengaduan' => $request->keterangan_pengaduan,
            'klasifikasi_pengaduan' => $request->klasifikasi_pengaduan,
        ];

        $attachments = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $index => $attachment) {
                $fileName = 'lampiran_'.date('Ymd_His_').$index.'.'.$attachment->getClientOriginalExtension();
                $attachment->move('images', $fileName);
                $attachments[] = [
                    'file_lampiran' => url('images/'.$fileName)
                ];
            }
        }

        DB::beginTransaction();
        try {
            $complaint->update($data);
            if ($request->lampiran_lama != null) {
                $complaint->attachments()
                    ->whereNotIn('id_lampiran', $request->lampiran_lama)
                    ->update([
                        'delete_lampiran' => 'Y'
                    ]);
            } else {
                $complaint->attachments()
                    ->update([
                        'delete_lampiran' => 'Y'
                    ]);
            }
            if (count($attachments) > 0) {
                $complaint->attachments()->createMany($attachments);
            }
            $complaint = $complaint->with([
                    'employee',
                    'headOfficeSection.headOffice',
                    'regionalOfficeSection.regionalOffice',
                    'branchOfficeSection.branchOffice',
                    'knows.employee',
                    'reads',
                    'done',
                    'answers'
                ])
                ->find($id);
            DB::commit();
            return $complaint;
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }

    public function deleteComplaint($id)
    {
        $customer = Auth::user();

        $complaint = Pengaduan::where([
                'id_pengaduan' => $id,
                'id_pegawai' => $customer->id_pegawai,
                'delete_pengaduan' => 'N'
            ])
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        $complaint->update([
            'delete_pengaduan' => 'Y'
        ]);

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function approveComplaint($id)
    {
        $customer = Auth::user();

        if (!in_array($customer->level_pegawai, ['Kepala Unit Kerja', 'Kepala Bagian Unit Kerja'])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menyetujui pengaduan.'
            ], 400);
        }

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
            // ->whereHas('employee', function ($query) use ($customer) {
            //     $query->where([
            //         'id_bagian_kantor_pusat' => $customer->id_bagian_kantor_pusat,
            //         'id_bagian_kantor_wilayah' => $customer->id_bagian_kantor_wilayah,
            //         'id_bagian_kantor_cabang' => $customer->id_bagian_kantor_cabang,
            //     ]);
            // })
            ->where([
                'id_pengaduan' => $id,
                'delete_pengaduan' => 'N'
            ])
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja' && $complaint->status_pengaduan != 'Pending') {
            return response()->json([
                'message' => 'Tidak dapat melakukan aksi tersebut, karena pengaduan sudah disetujui oleh Kepala Bagian Unit Kerja.'
            ], 404);
        }

        if ($customer->level_pegawai == 'Kepala Unit Kerja' && !in_array($complaint->status_pengaduan, ['Pending', 'Checked'])) {
            return response()->json([
                'message' => 'Tidak dapat melakukan aksi tersebut, karena pengaduan sudah disetujui oleh Kepala Bagian Unit Kerja.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $data = [];
            if ($customer->level_pegawai == 'Kepala Bagian Unit Kerja') {
                $data['status_pengaduan'] = 'Checked';

                $emails = [];
                $notifications = [];

                switch ($complaint->kantor_pengaduan) {
                    case 'Kantor Pusat':
                        $officeId = $complaint->headOfficeSection->id_kantor_pusat;
                        break;
                    case 'Kantor Cabang':
                        $officeId = $complaint->branchOfficeSection->id_kantor_cabang;
                        break;
                    default:
                        $officeId = $complaint->regionalOfficeSection->id_kantor_wilayah;
                }
                $employees = Pegawai::where([
                        'sebagai_pegawai' => 'Mitra/Pelanggan',
                        'level_pegawai' => 'Kepala Unit Kerja',
                        'kantor_pegawai' => $complaint->kantor_pengaduan,
                        // 'id_bagian_kantor_pusat' => $complaint->id_bagian_kantor_pusat,
                        // 'id_bagian_kantor_wilayah' => $complaint->id_bagian_kantor_wilayah,
                        // 'id_bagian_kantor_cabang' => $complaint->id_bagian_kantor_cabang,
                        'multi_pegawai' => $officeId,
                        'status_pegawai' => 'Aktif',
                        'delete_pegawai' => 'N',
                    ])
                    ->get();
                foreach ($employees as $employee) {
                    $emails[] = $employee->email_pegawai;
                    $notifications[] = [
                        'id_pegawai' => $employee->id_pegawai,
                        'nama_notifikasi' => 'Pengaduan Approve',
                        'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" telah di approve oleh Mitra/Pelanggan.',
                        'warna_notifikasi' => 'info',
                        'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                        'status_notifikasi' => 'Delivery',
                        'tgl_notifikasi' => date('Y-m-d H:i:s')
                    ];
                }

                $notifications[] = [
                    'id_pegawai' => $complaint->id_pegawai,
                    'nama_notifikasi' => 'Pengaduan Approve',
                    'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" telah di approve oleh Kepala Bagian Unit Kerja.',
                    'warna_notifikasi' => 'info',
                    'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                    'status_notifikasi' => 'Delivery',
                    'tgl_notifikasi' => date('Y-m-d H:i:s')
                ];

                Notifikasi::insert($notifications);

                // Mail::send('pengaduan.email_checked', ['id_pengaduan' => $complaint->id_pengaduan], function ($message) use ($emails) {
                //     $message->to($emails)
                //             ->subject('Pengaduan Baru (Checked)')
                //             ->from('helpdesk@cnplus.id', 'Helpdesk');
                // });
            } else if ($customer->level_pegawai == 'Kepala Unit Kerja') {
                $data['status_pengaduan'] = 'Approve';
                $data['respon_pengaduan'] = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $fromOffice = '';
                $fromSection = '';
                // if ($customer->kantor_pegawai == 'Kantor Pusat') {
                //     $fromOffice = $customer->headOfficeSection->headOffice->nama_kantor_pusat;
                //     $fromSection = $customer->headOfficeSection->nama_bagian_kantor_pusat;
                // } else if ($customer->kantor_pegawai == 'Kantor Wilayah') {
                //     $fromOffice = $customer->regionalOfficeSection->regionalOffice->nama_kantor_pusat;
                //     $fromSection = $customer->regionalOfficeSection->nama_bagian_kantor_pusat;
                // } else if ($customer->kantor_pegawai == 'Kantor Cabang') {
                //     $fromOffice = $customer->branchOfficeSection->branchOffice->nama_kantor_pusat;
                //     $fromSection = $customer->branchOfficeSection->nama_bagian_kantor_pusat;
                // }
                if ($complaint->employee->kantor_pegawai == 'Kantor Pusat') {
                    $fromOffice = $complaint->employee->headOfficeSection->headOffice->nama_kantor_pusat;
                    $fromSection = $complaint->employee->headOfficeSection->nama_bagian_kantor_pusat;
                } else if ($complaint->employee->kantor_pegawai == 'Kantor Wilayah') {
                    $fromOffice = $complaint->employee->regionalOfficeSection->regionalOffice->nama_kantor_pusat;
                    $fromSection = $complaint->employee->regionalOfficeSection->nama_bagian_kantor_pusat;
                } else if ($complaint->employee->kantor_pegawai == 'Kantor Cabang') {
                    $fromOffice = $complaint->employee->branchOfficeSection->branchOffice->nama_kantor_pusat;
                    $fromSection = $complaint->employee->branchOfficeSection->nama_bagian_kantor_pusat;
                }

                $toOffice = '';
                $toSection = '';
                // if ($complaint->employee->kantor_pegawai == 'Kantor Pusat') {
                //     $toOffice = $complaint->employee->headOfficeSection->headOffice->nama_kantor_pusat;
                //     $toSection = $complaint->employee->headOfficeSection->nama_bagian_kantor_pusat;
                // } else if ($complaint->employee->kantor_pegawai == 'Kantor Wilayah') {
                //     $toOffice = $complaint->employee->regionalOfficeSection->regionalOffice->nama_kantor_pusat;
                //     $toSection = $complaint->employee->regionalOfficeSection->nama_bagian_kantor_pusat;
                // } else if ($complaint->employee->kantor_pegawai == 'Kantor Cabang') {
                //     $toOffice = $complaint->employee->branchOfficeSection->branchOffice->nama_kantor_pusat;
                //     $toSection = $complaint->employee->branchOfficeSection->nama_bagian_kantor_pusat;
                // }
                if ($complaint->kantor_pengaduan == 'Kantor Pusat') {
                    $toOffice = $complaint->headOfficeSection->headOffice->nama_kantor_pusat;
                    $toSection = $complaint->headOfficeSection->nama_bagian_kantor_pusat;
                } else if ($complaint->kantor_pengaduan == 'Kantor Wilayah') {
                    $toOffice = $complaint->regionalOfficeSection->regionalOffice->nama_kantor_pusat;
                    $toSection = $complaint->regionalOfficeSection->nama_bagian_kantor_pusat;
                } else if ($complaint->kantor_pengaduan == 'Kantor Cabang') {
                    $toOffice = $complaint->branchOfficeSection->branchOffice->nama_kantor_pusat;
                    $toSection = $complaint->branchOfficeSection->nama_bagian_kantor_pusat;
                }

                $contact = Kontak::create([
                    'created_pengaduan' => $complaint->id_pegawai,
                    'kode_pengaduan' => 'P'.date('y').'-0000'.$complaint->id_pengaduan,
                    'nama_pengaduan' => $complaint->nama_pengaduan,
                    'dari_kontak' => $fromOffice.' - '.$fromSection,
                    'kepada_kontak' => $toOffice.' - '.$toSection,
                    'role_kontak' => $customer->nama_pegawai.' (Mitra/Pelanggan)',
                    'keterangan_kontak' => 'Telah melakukan pengaduan',
                    'tgl_kontak' => date('Y-m-d H:i:s'),
                ]);

                $emails = [];
                $notifications = [];
                $logContacts = [];

                $logContacts[] = [
                    'id_kontak' => $contact->id_kontak,
                    'id_pegawai' => $customer->id_pegawai,
                    'role_log_kontak' => $customer->nama_pegawai.' (Mitra/Pelanggan)',
                    'keterangan_log_kontak' => 'Telah melakukan pengaduan',
                    'status_log_kontak' => 'Delivery',
                    'tgl_log_kontak' => date('Y-m-d H:i:s')
                ];

                $employees = Pegawai::whereIn('sebagai_pegawai', ['Petugas', 'Agent'])
                    ->where([
                        'id_bagian_kantor_pusat' => $complaint->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $complaint->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $complaint->id_bagian_kantor_cabang,
                        'status_pegawai' => 'Aktif',
                        'delete_pegawai' => 'N',
                    ])
                    ->get();
                foreach ($employees as $employee) {
                    $emails[] = $employee->email_pegawai;
                    $logContacts[] = [
                        'id_kontak' => $contact->id_kontak,
                        'id_pegawai' => $employee->id_pegawai,
                        'role_log_kontak' => $customer->nama_pegawai.' (Mitra/Pelanggan)',
                        'keterangan_log_kontak' => 'Telah melakukan pengaduan',
                        'status_log_kontak' => 'Delivery',
                        'tgl_log_kontak' => date('Y-m-d H:i:s')
                    ];
                    $notifications[] = [
                        'id_pegawai' => $employee->id_pegawai,
                        'nama_notifikasi' => 'Pengaduan Approve',
                        'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" telah di approve oleh Mitra/Pelanggan.',
                        'warna_notifikasi' => 'info',
                        'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                        'status_notifikasi' => 'Delivery',
                        'tgl_notifikasi' => date('Y-m-d H:i:s')
                    ];
                }

                $employees = Pegawai::where([
                        'sebagai_pegawai' => 'Mitra/Pelanggan',
                        'level_pegawai' => 'Kepala Unit Kerja',
                        'id_bagian_kantor_pusat' => $complaint->id_bagian_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $complaint->id_bagian_kantor_wilayah,
                        'id_bagian_kantor_cabang' => $complaint->id_bagian_kantor_cabang,
                        'status_pegawai' => 'Aktif',
                        'delete_pegawai' => 'N',
                    ])
                    ->where('id_pegawai', '!=', $customer->id_pegawai)
                    ->get();
                foreach ($employees as $employee) {
                    $logContacts[] = [
                        'id_kontak' => $contact->id_kontak,
                        'id_pegawai' => $employee->id_pegawai,
                        'role_log_kontak' => $customer->nama_pegawai.' (Mitra/Pelanggan)',
                        'keterangan_log_kontak' => 'Telah melakukan pengaduan',
                        'status_log_kontak' => 'Delivery',
                        'tgl_log_kontak' => date('Y-m-d H:i:s')
                    ];
                }

                $notifications[] = [
                    'id_pegawai' => $complaint->id_pegawai,
                    'nama_notifikasi' => 'Pengaduan Approve',
                    'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" telah di approve oleh Kepala Unit Kerja.',
                    'warna_notifikasi' => 'info',
                    'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                    'status_notifikasi' => 'Delivery',
                    'tgl_notifikasi' => date('Y-m-d H:i:s')
                ];

                LogKontak::insert($logContacts);
                Notifikasi::insert($notifications);

                // Mail::send('pengaduan.email_approve', ['id_pengaduan' => $complaint->id_pengaduan], function ($message) use ($emails) {
                //     $message->to($emails)
                //             ->subject('Pengaduan Baru (Approve)')
                //             ->from('helpdesk@cnplus.id', 'Helpdesk');
                // });
            }

            $complaint->update($data);
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
                ->find($complaint->id_pengaduan);

            if ($data['status_pengaduan'] == 'Checked') {
                SendNotificationController::complaintApprovedUnitToCustomer($complaint->id_pengaduan);
                SendNotificationController::complaintCreatedToHead($complaint->id_pengaduan);
            } else {
                SendNotificationController::complaintApprovedHeadToCustomer($complaint->id_pengaduan);
                SendNotificationController::complaintCreatedToAgent($complaint->id_pengaduan);
            }

            DB::commit();
            return $complaint;
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }

    public function doneComplaint($id)
    {
        $customer = Auth::user();

        $complaint = Pengaduan::with('done')
            ->where([
                'id_pengaduan' => $id,
                'id_pegawai' => $customer->id_pegawai,
                'delete_pengaduan' => 'N'
            ])
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        if ($complaint->status_pengaduan == 'Finish') {
            return response()->json([
                'message' => 'Tidak dapat melakukan aksi tersebut, karena pengaduan sudah selesai.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            Selesai::create([
                'id_pengaduan' => $complaint->id_pengaduan,
                'id_pegawai' => $customer->id_pegawai,
                'tgl_selesai' => date('Y-m-d H:i:s')
            ]);

            $complaint->update([
                'status_pengaduan' => 'Finish'
            ]);

            $emails = [];
            $notifications = [];
            $employees = Pegawai::where([
                    'kantor_pegawai' => $complaint->kantor_pengaduan,
                    'id_bagian_kantor_pusat' => $complaint->id_bagian_kantor_pusat,
                    'id_bagian_kantor_cabang' => $complaint->id_bagian_kantor_cabang,
                    'id_bagian_kantor_wilayah' => $complaint->id_bagian_kantor_wilayah,
                    'status_pegawai' => 'Aktif',
                    'delete_pegawai' => 'N'
                ])
                ->get();
            foreach ($employees as $employee) {
                $emails[] = $employee->email_pegawai;
                $notifications[] = [
                    'id_pegawai' => $employee->id_pegawai,
                    'nama_notifikasi' => 'Pengaduan Finish',
                    'keterangan_notifikasi' => 'Pengaduan "'.$complaint->nama_pengaduan.'" telah diselesaikan.',
                    'warna_notifikasi' => 'success',
                    'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                    'status_notifikasi' => 'Delivery',
                    'tgl_notifikasi' => date('Y-m-d H:i:s')
                ];
            }
            Notifikasi::insert($notifications);

            // Mail::send('pengaduan.email_finish', ['id_pengaduan' => $complaint->id_pengaduan], function ($message) use ($emails) {
            //     $message->to($emails)
            //             ->subject('Pengaduan Baru (Finish)')
            //             ->from('helpdesk@cnplus.id', 'Helpdesk');
            // });

            DB::commit();

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

            SendNotificationController::complaintDoneToAgent($complaint->id_pengaduan);

            return $complaint;
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }

    public function createResponseAnswer($id, $answerId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keterangan_tanggapan' => 'required',
            'foto_tanggapan' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $customer = Auth::user();

        $complaint = Pengaduan::where([
                'id_pengaduan' => $id,
                // 'id_pegawai' => $customer->id_pegawai,
                'delete_pengaduan' => 'N'
            ])
            ->whereIn('status_pengaduan', ['Read', 'Holding', 'Moving', 'On Progress', 'Late', 'Finish'])
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        $answer = Jawaban::where([
                'id_jawaban' => $answerId,
                'id_pengaduan' => $complaint->id_pengaduan,
                'delete_jawaban' => 'N'
            ])
            ->first();

        if ($answer == null) {
            return response()->json([
                'message' => 'Jawaban tidak ditemukan.'
            ], 404);
        }

        $data = [
            'id_jawaban' => $answer->id_jawaban,
            'keterangan_tanggapan' => $request->keterangan_tanggapan,
            'tgl_tanggapan' => date('Y-m-d H:i:s')
        ];

        if ($request->hasFile('foto_tanggapan')) {
            $attachment = 'foto_lampiran_jawaban_'.date('Ymd_His.').$request->file('foto_tanggapan')->getClientOriginalExtension();
            $request->file('foto_tanggapan')->move('images', $attachment);
            $data['foto_tanggapan'] = url('images/'.$attachment);
        } else {
            $data['foto_tanggapan'] = 'logos/image.png';
        }

        $notifications = [];
        $employees = Pegawai::where([
                'kantor_pegawai' => $complaint->kantor_pengaduan,
                'id_bagian_kantor_pusat' => $complaint->id_bagian_kantor_pusat,
                'id_bagian_kantor_cabang' => $complaint->id_bagian_kantor_cabang,
                'id_bagian_kantor_wilayah' => $complaint->id_bagian_kantor_wilayah,
                'status_pegawai' => 'Aktif',
                'delete_pegawai' => 'N'
            ])
            ->whereIn('sebagai_pegawai', ['Petugas', 'Agent'])
            ->get();
        foreach ($employees as $employee) {
            $notifications[] = [
                'id_pegawai' => $employee->id_pegawai,
                'nama_notifikasi' => 'Pengaduan Tanggapan',
                'keterangan_notifikasi' => 'Tanggapan pengaduan "'.$complaint->nama_pengaduan.'" dari Mitra/Pelanggan.',
                'warna_notifikasi' => 'info',
                'url_notifikasi' => route('pengaduan').'?view='.$complaint->id_pengaduan,
                'status_notifikasi' => 'Delivery',
                'tgl_notifikasi' => date('Y-m-d H:i:s')
            ];
        }

        DB::beginTransaction();

        try {
            Tanggapan::create($data);
            Notifikasi::insert($notifications);
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

            SendNotificationController::complaintRepliedToAgent($complaint->id_pengaduan);

            DB::commit();
            return $complaint;
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }

    public function updateClassification($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'klasifikasi_pengaduan' => 'required|string|in:High,Medium,Low',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $customer = Auth::user();

        $complaint = Pengaduan::where([
                'id_pengaduan' => $id,
                'delete_pengaduan' => 'N'
            ])
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.',
            ], 404);
        }

        $data = [
            'klasifikasi_pengaduan' => $request->klasifikasi_pengaduan,
        ];

        DB::beginTransaction();
        try {
            $complaint->update($data);
            $complaint = $complaint->with([
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
            return $complaint;
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error($error);
            return $error;
        }
    }
}
