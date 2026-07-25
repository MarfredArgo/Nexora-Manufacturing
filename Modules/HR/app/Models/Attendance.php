<?php

namespace Modules\HR\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $connection = 'hr';

    /** Fallback allotted work time when employee schedule is missing. */
    public const ALLOTTED_WORK_MINUTES = 9 * 60;

    /** Fallback HR-assigned start time. */
    public const EXPECTED_TIME_IN = '08:00:00';

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'time_in',
        'time_in_image',
        'time_out',
        'time_out_image',
        'status',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function timeInImageUrl(): ?string
    {
        return $this->imageUrl($this->time_in_image);
    }

    public function timeOutImageUrl(): ?string
    {
        return $this->imageUrl($this->time_out_image);
    }

    private function imageUrl(?string $filename): ?string
    {
        if (! $filename) {
            return null;
        }

        return asset('attendance_photos/'.$filename);
    }

    public function displayStatus(): string
    {
        if ($this->status === 'Absent' || $this->status === 'Leave' || ! $this->time_in) {
            return 'Absent';
        }

        return 'Present';
    }

    public function formattedTimeIn(): string
    {
        return $this->time_in
            ? Carbon::parse($this->time_in)->format('h:i A')
            : 'â€”';
    }

    public function formattedTimeOut(): string
    {
        return $this->time_out
            ? Carbon::parse($this->time_out)->format('h:i A')
            : 'â€”';
    }

    /** HR-assigned start time from employee work schedule. */
    public function expectedTimeIn(): string
    {
        if ($this->employee) {
            return $this->employee->workScheduleStart();
        }

        return self::EXPECTED_TIME_IN;
    }

    /** Required minutes from employee start/end schedule. */
    public function allottedWorkMinutes(): int
    {
        if ($this->employee) {
            return $this->employee->allottedWorkMinutes();
        }

        return self::ALLOTTED_WORK_MINUTES;
    }

    /** Raw minutes between time in and time out. */
    public function elapsedWorkMinutes(): ?int
    {
        if (! $this->time_in || ! $this->time_out) {
            return null;
        }

        $start = Carbon::parse($this->attendance_date.' '.$this->time_in);
        $end = Carbon::parse($this->attendance_date.' '.$this->time_out);

        if ($end->lt($start)) {
            $end->addDay();
        }

        return (int) $start->diffInMinutes($end);
    }

    public function formattedElapsedDuration(?int $minutes = null): string
    {
        if ($minutes === null) {
            return 'â€”';
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%dhr %02dm', $hours, $mins);
    }

    /**
     * Work Hours column: "{elapsed} / {HR allotted from schedule}"
     * Example: 1hr 15m / 9 hrs
     */
    public function formattedWorkHours(): string
    {
        $allottedMinutes = $this->allottedWorkMinutes();
        $allottedHours = (int) round($allottedMinutes / 60);
        $elapsed = $this->elapsedWorkMinutes();

        return $this->formattedElapsedDuration($elapsed).' / '.$allottedHours.' hrs';
    }

    public function metRequiredWorkSpan(): bool
    {
        $elapsed = $this->elapsedWorkMinutes();

        return $elapsed !== null && $elapsed >= $this->allottedWorkMinutes();
    }

    public function isLateCheckIn(): bool
    {
        if (! $this->time_in) {
            return false;
        }

        return Carbon::parse($this->time_in)->gt(Carbon::parse($this->expectedTimeIn()));
    }

    /**
     * Time In red when late vs HR start; clears once allotted hours are met.
     */
    public function showsLateTimeInWarning(): bool
    {
        if (! $this->isLateCheckIn()) {
            return false;
        }

        if ($this->time_out && $this->metRequiredWorkSpan()) {
            return false;
        }

        return true;
    }

    /** Time Out red when under the HR allotted schedule hours. */
    public function showsShortTimeOutWarning(): bool
    {
        if (! $this->time_out) {
            return false;
        }

        return ! $this->metRequiredWorkSpan();
    }
}
