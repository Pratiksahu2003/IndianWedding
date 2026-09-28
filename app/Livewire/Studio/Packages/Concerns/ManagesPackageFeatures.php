<?php

namespace App\Livewire\Studio\Packages\Concerns;

use App\Models\Package;

trait ManagesPackageFeatures
{
    /** @var list<string> */
    public array $featureItems = [];

    public string $newFeature = '';

    public function addFeature(): void
    {
        $value = trim($this->newFeature);

        if ($value === '') {
            return;
        }

        $this->featureItems[] = $value;
        $this->newFeature = '';
    }

    public function removeFeature(int $index): void
    {
        unset($this->featureItems[$index]);
        $this->featureItems = array_values($this->featureItems);
    }

    protected function syncFeatureItems(Package $package): void
    {
        $package->items()->delete();

        foreach ($this->featureItems as $index => $name) {
            $name = trim($name);

            if ($name === '') {
                continue;
            }

            $package->items()->create([
                'organization_id' => $package->organization_id,
                'name' => $name,
                'sort_order' => $index,
            ]);
        }
    }
}
