<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class PrebuiltConfig extends Model
{
    use BelongsToClient;
    protected $guarded = [];

    public function cpu()
    {
        return $this->belongsTo(Cpu::class);
    }

    public function gpu()
    {
        return $this->belongsTo(Gpu::class);
    }

    public function motherboard()
    {
        return $this->belongsTo(Motherboard::class);
    }

    public function ram()
    {
        return $this->belongsTo(Ram::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function powerSupply()
    {
        return $this->belongsTo(PowerSupply::class);
    }

    public function pcCase()
    {
        return $this->belongsTo(PcCase::class);
    }

    public function cooler()
    {
        return $this->belongsTo(Cooler::class);
    }
}
