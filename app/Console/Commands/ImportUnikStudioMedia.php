<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\Package;
use App\Models\PortfolioItem;
use App\Models\SiteContent;
use App\Support\SiteCopy;
use App\Support\Tenant;
use App\Support\UnikStudioAssets;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportUnikStudioMedia extends Command
{
    protected $signature = 'unik:import-media {--skip-download : Only update database paths}';

    protected $description = 'Download images from unikstudio.in and apply them to the public site';

    public function handle(): int
    {
        $dir = public_path('images/unik');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (! $this->option('skip-download')) {
            foreach (UnikStudioAssets::downloads() as $filename => $remote) {
                $target = $dir.'/'.$filename;
                $this->line('Fetching '.$filename.'…');
                $response = Http::timeout(120)->get($remote);
                if (! $response->successful()) {
                    $this->warn('Failed: '.$remote);

                    continue;
                }
                file_put_contents($target, $response->body());
            }
        }

        $org = Organization::query()->where('is_active', true)->first();
        if (! $org) {
            $this->error('No active organization found.');

            return self::FAILURE;
        }

        Tenant::set($org->id);

        SiteContent::withoutTenant()->updateOrCreate(
            ['organization_id' => $org->id, 'key' => 'home.hero_image'],
            [
                'group' => 'home',
                'label' => 'Hero background image',
                'value' => UnikStudioAssets::url('hero-slide.png'),
                'type' => 'input',
                'sort_order' => 195,
            ]
        );

        PortfolioItem::withoutTenant()->where('organization_id', $org->id)->delete();
        foreach (UnikStudioAssets::portfolioCatalog() as $i => $row) {
            PortfolioItem::query()->create([
                'organization_id' => $org->id,
                'title' => $row['title'],
                'slug' => Str::slug($row['title']).'-'.($i + 1),
                'location' => 'New Delhi',
                'couple' => $row['couple'],
                'story' => $row['story'],
                'event_date' => now()->subMonths(max(1, 20 - $i))->toDateString(),
                'image_path' => UnikStudioAssets::url($row['file']),
                'category' => $row['category'],
                'is_published' => true,
                'sort_order' => $i,
            ]);
        }

        foreach (UnikStudioAssets::serviceCovers() as $slug => $file) {
            Package::withoutTenant()
                ->where('organization_id', $org->id)
                ->where('slug', $slug)
                ->update(['cover_image' => UnikStudioAssets::url($file)]);
        }

        SiteCopy::forget($org->id);
        $this->info('Unik Studio media imported for organization #'.$org->id);

        return self::SUCCESS;
    }
}
