<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveStatusRequest;
use App\Services\LeaveService;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

 
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $leaves = $this->leaveService->getAllLeaves();
        } else {
            if (!$user->employee) {
                return response()->json(['message' => 'Data karyawan tidak ditemukan untuk pengguna ini.'], 404);
            }
            $leaves = $this->leaveService->getLeavesByEmployee($user->employee->id);
        }

        return response()->json([
            'status' => 'success',
            'data' => $leaves
        ]);
    }

    public function store(StoreLeaveRequest $request)
    {
        $user = $request->user();

        if (!$user->employee) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan. Tidak dapat mengajukan cuti.'], 403);
        }

        try {
            $leave = $this->leaveService->createLeave(
                $user->employee->id,
                $request->validated(),
                $request->file('attachment')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Pengajuan cuti berhasil dikirim.',
                'data' => $leave
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateStatus(UpdateLeaveStatusRequest $request, $id)
    {
        try {
            $leave = $this->leaveService->updateStatus($id, $request->status);

            return response()->json([
                'status' => 'success',
                'message' => 'Status cuti berhasil diperbarui.',
                'data' => $leave
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data cuti tidak ditemukan atau pembaruan gagal.'
            ], 404);
        }
    }
}
