<?php

namespace Modules\OrderFulfillment\Models;

use Modules\OrderFulfillment\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class PackingError extends Model
{
    use BelongsToClient;
    /**
     * packing_errors lives on the default connection (same as orders),
     * NOT the "inventory" connection ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â unlike PackingMaterial.
     */
    protected $table = 'packing_errors';

    protected $fillable = [
        'order_id',
        'material',
        'reason',
    ];
}




