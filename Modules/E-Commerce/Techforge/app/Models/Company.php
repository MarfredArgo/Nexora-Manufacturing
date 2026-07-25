<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $connection = 'ecommerce';
    protected $table = 'companies';

    protected $fillable = [
        'company_name', 'industry', 'company_email', 'phone_no',
        'admin_name', 'status', 'admin_user_id', 'logo_path',
        'hr_employee_id', 'setup_completed_at', 'ecommerce_slug',
    ];

    /**
     * Find the company for a given HR employee ID.
     */
    public static function forHrEmployee(int $hrEmployeeId): ?self
    {
        return static::where('hr_employee_id', $hrEmployeeId)->first();
    }

    /**
     * Returns a public URL to the company logo, or null if none is set.
     */
    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        // Logo paths are stored as e.g. "company-logos/xxx.png" inside ecommerce storage,
        // but the Techforge logo lives in public/ecommerce/. Check public first.
        $publicPath = public_path('ecommerce/' . basename($this->logo_path));
        if (file_exists($publicPath)) {
            return asset('ecommerce/' . basename($this->logo_path));
        }

        // Fall back to the storage-based path served via the ecommerce disk.
        return asset('storage/' . $this->logo_path);
    }
}
