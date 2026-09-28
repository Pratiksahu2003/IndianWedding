<?php

namespace App\Livewire\Client;

use App\Models\Gallery as GalleryModel;
use App\Support\ClientPortal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Gallery')]
class Gallery extends Component
{
    public function toggleFavorite(int $itemId): void
    {
        $item = \App\Models\GalleryItem::query()->whereKey($itemId)->firstOrFail();
        $gallery = $item->gallery;

        abort_unless($gallery, 404);
        $this->authorize('view', $gallery);

        $item->update(['is_favorite' => ! $item->is_favorite]);
    }

    public function render()
    {
        $customer = ClientPortal::customer();
        $gallery = $customer
            ? GalleryModel::query()
                ->whereHas('project', fn ($q) => $q->where('customer_id', $customer->id))
                ->where('is_released', true)
                ->with(['albums', 'items.file'])
                ->first()
            : null;

        return view('livewire.client.gallery', compact('gallery'));
    }
}
