<?php

namespace App\Services;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class LeaveService
{
    public function calculateDaysUsed($employeeId, $year)
    {
        $leaves = Leave::where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->whereIn('status', ['pending', 'approved'])
            ->get();
        
        $totalDays = 0;
        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            $totalDays += $start->diffInDays($end) + 1;
        }
        
        return $totalDays;
    }

    public function createLeave($employeeId, array $data, UploadedFile $attachment)
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        $requestedDays = $start->diffInDays($end) + 1;

        if ($requestedDays <= 0) {
            throw new \Exception("Tanggal selesai harus setelah atau sama dengan tanggal mulai.");
        }

        $usedDays = $this->calculateDaysUsed($employeeId, $start->year);

        if (($usedDays + $requestedDays) > 12) {
            throw new \Exception("Kuota cuti terlampaui. Sisa cuti Anda untuk tahun ini adalah " . (12 - $usedDays) . " hari.");
        }

        $attachmentPath = $attachment->store('attachments', 'public');

        return Leave::create([
            'employee_id' => $employeeId,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'],
            'leave_type' => $data['leave_type'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);
    }

    public function updateStatus($leaveId, $status)
    {
        $leave = Leave::findOrFail($leaveId);
        $leave->update(['status' => $status]);
        return $leave;
    }
    
    public function getLeavesByEmployee($employeeId)
    {
        return Leave::where('employee_id', $employeeId)->orderBy('created_at', 'desc')->get();
    }
    
    public function getAllLeaves()
    {
        return Leave::with('employee')->orderBy('created_at', 'desc')->get();
    }
}
