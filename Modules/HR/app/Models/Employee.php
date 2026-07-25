<?php

namespace Modules\HR\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    protected $connection = 'hr';

    protected static function booted(): void
    {
        static::addGlobalScope('client', function (Builder $query): void {
            if ($clientId = (int) session('employee_client_id')) {
                $query->where($query->getModel()->qualifyColumn('client_id'), $clientId);
            }
        });

        static::creating(function (self $employee): void {
            if (! $employee->client_id && ($clientId = (int) session('employee_client_id'))) {
                $employee->client_id = $clientId;
            }
        });
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    protected $fillable = [
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'marital_status',
        'nationality',
        'profile_picture',
        'address',
        'phone',
        'department',
        'position',
        'hire_date',
        'work_schedule',
        'email',
        'company_email',
        'temporary_password',
        'must_change_password',
        'client_id',
        'approval_status',
        'birth_certificate',
        'curriculum_vitae',
        'valid_id',
        'medical_certificate',
        'signature',
    ];

    /**
     * work_schedule formats:
     * - "08:00-17:00" (start-end from onboarding)
     * - legacy single time "08:00" / "08:00:00"
     */
    public function parsedWorkSchedule(): array
    {
        $schedule = trim((string) $this->work_schedule);

        if ($schedule !== '' && preg_match(
            '/^(\d{1,2}:\d{2}(?::\d{2})?)\s*-\s*(\d{1,2}:\d{2}(?::\d{2})?)$/',
            $schedule,
            $m
        )) {
            return [
                'start' => Carbon::parse($m[1])->format('H:i:s'),
                'end' => Carbon::parse($m[2])->format('H:i:s'),
            ];
        }

        if ($schedule !== '' && preg_match('/^(\d{1,2}:\d{2})(?::\d{2})?$/', $schedule, $m)) {
            $start = Carbon::parse($m[1]);

            return [
                'start' => $start->format('H:i:s'),
                'end' => $start->copy()->addHours(9)->format('H:i:s'),
            ];
        }

        return [
            'start' => '08:00:00',
            'end' => '17:00:00',
        ];
    }

    public function workScheduleStart(): string
    {
        return $this->parsedWorkSchedule()['start'];
    }

    public function workScheduleEnd(): string
    {
        return $this->parsedWorkSchedule()['end'];
    }

    /** Required clocked minutes from HR start/end schedule. */
    public function allottedWorkMinutes(): int
    {
        $start = Carbon::parse($this->workScheduleStart());
        $end = Carbon::parse($this->workScheduleEnd());

        if ($end->lte($start)) {
            $end->addDay();
        }

        return max((int) $start->diffInMinutes($end), 1);
    }
}
