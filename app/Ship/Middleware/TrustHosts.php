<?php

namespace App\Ship\Middleware;

use App\Ship\Abstracts\Middleware\TrustHosts as AbstractMiddleware;

class TrustHosts extends AbstractMiddleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts()
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
