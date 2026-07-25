<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class EcommerceAdmin extends Authenticatable
{
    protected $connection = 'hr';
    protected $table = 'employees';

    public function isEcommerceEmployee(): bool
    {
        return (int) $this->client_id > 0
            && strtolower((string) $this->approval_status) === 'active'
            && in_array(strtolower(trim((string) $this->department)), [
                'e-commerce', 'ecommerce', 'electronic commerce', 'crm',
            ], true);
    }

    public function getAuthPassword(): string
    {
        return (string) $this->temporary_password;
    }

    public function getAuthPasswordName(): string
    {
        return 'temporary_password';
    }

    /**
     * Look up the Company record for this admin via hr_employee_id.
     */
    public function getCompany(): ?\App\Models\Company
    {
        return \App\Models\Company::find($this->client_id);
    }
}
