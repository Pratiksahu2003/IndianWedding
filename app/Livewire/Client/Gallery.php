<?php

namespace App\Livewire\Client;

use App\Models\Customer;
use App\Models\Gallery as GalleryModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Gallery')]
class Gallery extends Component
{
    public function toggleFavorite(int $itemId): void
    {
        $customer = Customer::query()->where('user_id', auth()->id())->firstOrFail();
        $item = \App\Models\GalleryItem::query()->whereKey($itemId)->firstOrFail();
        $gallery = $item->gallery;
        abort_unless($gallery && $this->authorize('view', $gallery) || $gallery->project?->customer_id === $customer->id, 403);
        $item->update(['is_favorite' => ! $item->is_favorite]);
    }

    public function render()
    {
        $customer = Customer::query()->where('user_id', auth()->id())->firstOrFail();
        $gallery = GalleryModel::query()
            ->whereHas('project', fn ($q) => $q->where('customer_id', $customer->id))
            ->where('is_released', true)
            ->with(['albums', 'items.file'])
            ->first();

        return view('livewire.client.gallery', compact('gallery'));
    }
}
