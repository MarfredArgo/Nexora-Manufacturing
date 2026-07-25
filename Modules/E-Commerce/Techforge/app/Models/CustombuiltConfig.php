<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustombuiltConfig extends Model
{
    use BelongsToClient;
    use HasFactory;

    protected $table = 'configurator_configs';
    
    protected $guarded = [];

    public function intelCpu() { return $this->belongsTo(Cpu::class, 'intel_cpu_id'); }
    public function amdCpu() { return $this->belongsTo(Cpu::class, 'amd_cpu_id'); }
    public function intelMotherboard() { return $this->belongsTo(Motherboard::class, 'intel_motherboard_id'); }
    public function amdMotherboard() { return $this->belongsTo(Motherboard::class, 'amd_motherboard_id'); }
    public function intelRam() { return $this->belongsTo(Ram::class, 'intel_ram_id'); }
    public function amdRam() { return $this->belongsTo(Ram::class, 'amd_ram_id'); }
    public function gpu() { return $this->belongsTo(Gpu::class); }
    public function storage() { return $this->belongsTo(Storage::class); }
    public function powerSupply() { return $this->belongsTo(PowerSupply::class); }
    public function pcCase() { return $this->belongsTo(PcCase::class); }
    public function cooler() { return $this->belongsTo(Cooler::class); }
}