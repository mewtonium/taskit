<?php

namespace App\Models\Concerns;

use App\Models\Star;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Starrable
{
    /**
     * Get the model's starrable relationship.
     */
    public function starred(): MorphOne
    {
        return $this->morphOne(Star::class, 'starrable');
    }
}
