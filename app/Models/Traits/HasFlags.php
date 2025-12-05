<?php

namespace App\Models\Traits;

use App\Models\Flag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasFlags
{
    public function flags(): MorphToMany
    {
        return $this->morphToMany(Flag::class, 'flaggable');
    }

    public function hasFlag(string $flagName): bool
    {
        return $this->flags()->where('name', $flagName)->exists();
    }

    public function addFlag(string $flagName): void
    {
        $flag = Flag::firstOrCreate(['name' => $flagName]);
        $this->flags()->syncWithoutDetaching($flag);
    }

    public function removeFlag(string $flagName): void
    {
        $flag = Flag::where('name', $flagName)->first();
        if ($flag) {
            $this->flags()->detach($flag);
        }
    }

    public function setFlag(string $flagName, bool $value): void
    {
        if ($value) {
            $this->addFlag($flagName);
        } else {
            $this->removeFlag($flagName);
        }
    }
}
