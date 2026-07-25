<?php

namespace Modules\HR\Http\Controllers;

use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $validated = $request->validate([
            // A bare exists rule would query the ITSM default database rather
            // than the dedicated HR connection.
            'employee_id' => 'required|string',
            'action' => 'nullable|in:clock_in,clock_out',
            'photo' => 'required|string',
        ]);

        $employeeCode = trim($validated['employee_id']);
        $employee = Employee::query()->where('employee_id', $employeeCode)->first();

        // Sessions created before employee_code was stored may still prefill
        // the HR primary key; preserve that existing attendance flow.
        if (! $employee && ctype_digit($employeeCode)) {
            $employee = Employee::query()->whereKey((int) $employeeCode)->first();
        }

        if (! $employee) {
            return back()
                ->withErrors(['employee_id' => 'The selected employee ID is invalid.'])
                ->withInput(['employee_id' => $employeeCode]);
        }
        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString();
        $action = $request->input('action', 'clock_in');

        $photoPath = $this->storeCompressedAttendancePhoto(
            $request->input('photo'),
            $employee->id,
            $action === 'clock_out' ? 'out' : 'in'
        );

        if (! $photoPath) {
            return back()
                ->with('error', 'Invalid or missing attendance photo. Please capture again.')
                ->withInput(['employee_id' => $employeeCode]);
        }

        if ($action === 'clock_out') {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('attendance_date', $today)
                ->first();

            if (! $attendance || ! $attendance->time_in) {
                return back()->with('error', 'No clock-in found for today.');
            }

            if ($attendance->time_out) {
                return back()->with('error', 'This employee already clocked out today.');
            }

            $attendance->time_out = $now->format('H:i:s');
            $attendance->time_out_image = $photoPath;
            $attendance->save();

            return redirect()->route('hr.clockinout')
                ->with('success', 'Clock out recorded for employee #' . $employeeCode)
                ->with('employee_id', $employeeCode)
                ->with('clock_in', $attendance->time_in)
                ->with('clock_out', $attendance->time_out)
                ->with('clocked_in', true)
                ->with('clocked_out', true)
                ->withInput(['employee_id' => $employeeCode]);
        }

        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'attendance_date' => $today,
        ]);

        if ($attendance->exists && $attendance->time_in) {
            if ($attendance->time_out) {
                return back()->with('error', 'This employee already clocked out today.');
            }

            return back()->with('error', 'This employee already clocked in today.');
        }

        $attendance->time_in = $now->format('H:i:s');
        $attendance->time_in_image = $photoPath;
        $attendance->status = 'Present';
        $attendance->save();

        return redirect()->route('hr.clockinout')
            ->with('success', 'Clock in recorded for employee #' . $employeeCode)
            ->with('employee_id', $employeeCode)
            ->with('clock_in', $attendance->time_in)
            ->with('clocked_in', true)
            ->withInput(['employee_id' => $employeeCode]);
    }

    /**
     * Save a compressed JPEG from a canvas data URL.
     * Max edge ~480px, quality ~55 for a small file size.
     * Falls back to raw write if GD is unavailable.
     */
    private function storeCompressedAttendancePhoto(string $dataUrl, int $employeeId, string $type): ?string
    {
        $dataUrl = trim($dataUrl);

        if ($dataUrl === '' || ! preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/i', $dataUrl, $matches)) {
            return null;
        }

        $extension = strtolower($matches[1] === 'jpg' ? 'jpeg' : $matches[1]);

        // application/x-www-form-urlencoded turns "+" into spaces and corrupts base64.
        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $base64 = str_replace(' ', '+', $base64);
        $binary = base64_decode($base64, true);

        if ($binary === false || strlen($binary) < 100) {
            return null;
        }

        // Reject oversized payloads before processing (~2.5MB raw).
        if (strlen($binary) > 2_500_000) {
            return null;
        }

        $directory = public_path('attendance_photos');
        File::ensureDirectoryExists($directory);

        $filename = sprintf(
            'emp%s_%s_%s_%s.jpg',
            $employeeId,
            $type,
            now('Asia/Manila')->format('Ymd_His'),
            Str::lower(Str::random(6))
        );
        $fullPath = $directory.DIRECTORY_SEPARATOR.$filename;

        // Preferred path: resize + compress with GD.
        if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
            $source = @imagecreatefromstring($binary);

            if ($source) {
                $srcW = imagesx($source);
                $srcH = imagesy($source);
                $maxEdge = 480;

                $scale = min(1, $maxEdge / max($srcW, $srcH));
                $dstW = max(1, (int) round($srcW * $scale));
                $dstH = max(1, (int) round($srcH * $scale));

                $resized = imagecreatetruecolor($dstW, $dstH);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

                $saved = imagejpeg($resized, $fullPath, 55);
                imagedestroy($source);
                imagedestroy($resized);

                if ($saved && File::exists($fullPath)) {
                    return $filename;
                }
            }
        }

        // Fallback: write original bytes (rename extension if not jpeg).
        if ($extension !== 'jpeg') {
            $filename = sprintf(
                'emp%s_%s_%s_%s.%s',
                $employeeId,
                $type,
                now('Asia/Manila')->format('Ymd_His'),
                Str::lower(Str::random(6)),
                $extension === 'png' ? 'png' : ($extension === 'webp' ? 'webp' : 'jpg')
            );
            $fullPath = $directory.DIRECTORY_SEPARATOR.$filename;
        }

        if (File::put($fullPath, $binary) === false) {
            return null;
        }

        return $filename;
    }
}
