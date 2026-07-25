<?php

namespace Modules\Ecommerce\Support;

class EcommerceClientContext
{
    private ?int $clientId = null;

    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function clientId(): ?int
    {
        return $this->clientId;
    }
}
