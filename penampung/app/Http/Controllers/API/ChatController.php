<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Log;
use Auth;
use Session;
use Validator;
use Exception;
use App\Models\API\Kontak;
use App\Models\API\LogKontak;
use App\Models\API\Pengaduan;
use App\Models\API\Pegawai;
use App\Models\API\Chat;

class ChatController extends Controller
{
    public function getChats()
    {
        $employee = Auth::user();

        $contacts = Kontak::with([
                'chats' => function ($query) {
                    $query->with('employee')->orderBy('id_chat', 'DESC');
                }
            ]);

        if ($employee->sebagai_pegawai == 'Mitra/Pelanggan') {
            if ($employee->level_pegawai == 'Kepala Unit Kerja') {
                $contacts
                    ->whereHas('employee', function ($query) use ($employee) {
                        if ($employee->kantor_pegawai == 'Kantor Pusat') {
                            $query->whereHas('headOfficeSection', function ($subQuery) use ($employee) {
                                $subQuery->where('id_kantor_pusat', $employee->multi_pegawai);
                            });
                        } else if ($employee->kantor_pegawai == 'Kantor Wilayah') {
                            $query->whereHas('regionalOfficeSection', function ($subQuery) use ($employee) {
                                $subQuery->where('id_kantor_wilayah', $employee->multi_pegawai);
                            });
                        } else if ($employee->kantor_pegawai == 'Kantor Cabang') {
                            $query->whereHas('branchOfficeSection', function ($subQuery) use ($employee) {
                                $subQuery->where('id_kantor_cabang', $employee->multi_pegawai);
                            });
                        }
                    });
            } else if ($employee->level_pegawai == 'Kepala Bagian Unit Kerja') {
                $contacts
                    ->whereHas('employee', function ($query) use ($employee) {
                        $query->where([
                            'id_bagian_kantor_pusat' => $employee->id_bagian_kantor_pusat,
                            'id_bagian_kantor_wilayah' => $employee->id_bagian_kantor_wilayah,
                            'id_bagian_kantor_cabang' => $employee->id_bagian_kantor_cabang,
                        ]);
                    });
            } else {
                $contacts->whereHas('employee', function ($query) use ($employee) {
                    $query->where('id_pegawai', $employee->id_pegawai);
                });
            }
        } else if ($employee->sebagai_pegawai == 'Agent') {
            $contacts
                ->join('tb_pengaduan', DB::raw("CAST(SUBSTR(tb_kontak.kode_pengaduan, 5) AS UNSIGNED)"), 'tb_pengaduan.id_pengaduan')
                ->where([
                    'tb_pengaduan.id_bagian_kantor_pusat' => $employee->id_bagian_kantor_pusat,
                    'tb_pengaduan.id_bagian_kantor_cabang' => $employee->id_bagian_kantor_cabang,
                    'tb_pengaduan.id_bagian_kantor_wilayah' => $employee->id_bagian_kantor_wilayah,
                ]);
        } else if ($employee->sebagai_pegawai == 'Petugas') {
            $contacts->whereHas('employee', function ($query) use ($employee) {
                $query->where([
                    'id_bagian_kantor_pusat' => $employee->id_bagian_kantor_pusat,
                    'id_bagian_kantor_cabang' => $employee->id_bagian_kantor_cabang,
                    'id_bagian_kantor_wilayah' => $employee->id_bagian_kantor_wilayah,
                ]);
            });
        }

        $contacts = $contacts
            ->get()
            ->sortByDesc(function ($contact) {
                return $contact->chats->first->id_chat;
            })
            ->values();

        return $contacts;
    }

    public function getChat($id)
    {
        $employee = Auth::user();

        $contact = Kontak::with([
                'chats' => function ($query) {
                    $query->with('employee')->orderBy('id_chat', 'DESC');
                }
            ])
            ->find($id);

        if ($contact == null) {
            return response()->json([
                'message' => 'Kontak tidak ditemukan.'
            ], 404);
        }

        Chat::where([
                'room_chat' => $contact->id_kontak,
                'status_chat' => 'Delivery',
                'delete_chat' => 'N'
            ])
            ->where('id_pegawai', '!=', $employee->id_pegawai)
            ->update([
                'status_chat' => 'Read'
            ]);

        LogKontak::where([
                'id_kontak' => $contact->id_kontak,
                'id_pegawai' => $employee->id_pegawai,
                'delete_log_kontak' => 'N'
            ])
            ->update([
                'status_log_kontak' => 'Read'
            ]);

        $logContacts = LogKontak::where([
                'id_pegawai' => $employee->id_pegawai,
                'ajax_log_kontak' => 'Run',
                'delete_log_kontak' => 'N'
            ])
            ->get();
        if ($logContacts->count() > 0) {
            LogKontak::where('delete_log_kontak', 'N')
                ->whereIn('id_log_kontak', $logContacts->pluck('id_log_kontak'))
                ->update([
                    'ajax_log_kontak' => 'Close'
                ]);
        }
        $logContacts = LogKontak::where([
                'id_pegawai' => $employee->id_pegawai,
                'song_log_kontak' => 'Play',
                'delete_log_kontak' => 'N'
            ])
            ->get();
        if ($logContacts->count() > 0) {
            LogKontak::where('delete_log_kontak', 'N')
                ->whereIn('id_log_kontak', $logContacts->pluck('id_log_kontak'))
                ->update([
                    'song_log_kontak' => 'Stop'
                ]);
        }

        return $contact;
    }

    public function sendChat($id, Request $request)
    {
        $contact = Kontak::find($id);

        if ($contact == null) {
            return response()->json([
                'message' => 'Kontak tidak ditemukan.'
            ], 404);
        }

        $complaintId = intval(substr($contact->kode_pengaduan, 4));
        $complaint = Pengaduan::with([
                'headOfficeSection',
                'regionalOfficeSection',
                'branchOfficeSection',
            ])
            ->where([
                'id_pengaduan' => $complaintId,
                'delete_pengaduan' => 'N'
            ])
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'keterangan_chat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $employee = Auth::user();

        $requestController = new Request();
        Session::put('id_pegawai', $employee->id_pegawai);
        $requestController->replace([
            'kontak' => $id,
            'keterangan' => $request->keterangan_chat
        ]);
        $chatController = new Chat();
        $chatController->kirim_chat($requestController);

        try {
            $employeeIds = Pegawai::where(function ($query) use ($complaint) {
                    $query
                        ->where(function ($subQuery) use ($complaint) {
                            $subQuery
                                ->where([
                                    'sebagai_pegawai' => 'Agent',
                                    'kantor_pegawai' => $complaint->kantor_pengaduan,
                                    'id_bagian_kantor_pusat' => $complaint->id_bagian_kantor_pusat,
                                    'id_bagian_kantor_cabang' => $complaint->id_bagian_kantor_cabang,
                                    'id_bagian_kantor_wilayah' => $complaint->id_bagian_kantor_wilayah,
                                ])
                                ->whereIn('level_pegawai', ['Kepala Bagian Unit Kerja', 'Staff']);
                        })
                        ->orWhere(function ($subQuery) use ($complaint) {
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
                            $subQuery
                                ->where([
                                    'sebagai_pegawai' => 'Agent',
                                    'kantor_pegawai' => $complaint->kantor_pengaduan,
                                    'level_pegawai' => 'Kepala Unit Kerja',
                                    'multi_pegawai' => $officeId,
                                ]);
                        })
                        ->orWhere('id_pegawai', $complaint->id_pegawai)
                        ->orWhere(function ($subQuery) use ($complaint) {
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
                            $subQuery
                                ->where([
                                    'sebagai_pegawai' => 'Mitra/Pelanggan',
                                    'kantor_pegawai' => $complaint->kantor_pengaduan,
                                    'level_pegawai' => 'Kepala Unit Kerja',
                                    'multi_pegawai' => $officeId,
                                ]);
                        });
                })
                ->where([
                    ['id_pegawai', '!=', $employee->id_pegawai],
                    'status_pegawai' => 'Aktif',
                    'delete_pegawai' => 'N'
                ])
                ->pluck('id_pegawai');

            FirebaseMessagingController::sendMessageToManyEmployee(
                $employeeIds,
                $employee->nama_pegawai,
                $request->keterangan_chat,
                [
                    'screen' => '/chat-detail',
                    'id' => $contact->id_kontak
                ]
            );

            $contact = Kontak::with([
                    'chats' => function ($query) {
                        $query->with('employee')->orderBy('id_chat', 'DESC');
                    }
                ])
                ->find($id);

            return $contact;
        } catch (Exception $error) {
            Log::error($error);

            return $error;
        }
    }
}
