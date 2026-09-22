<?php

namespace App\Livewire\Platform;

use App\Models\Faq;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PortfolioItem;
use App\Models\SiteContent;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\SiteCopy;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Website content')]
class WebsiteEditor extends Component
{
    public string $tab = 'copy';

    /** @var array<string, string> */
    public array $fields = [];

    public string $team_name = '';
    public string $team_role = '';
    public string $testimonial_author = '';
    public string $testimonial_role = '';
    public string $testimonial_quote = '';
    public string $faq_question = '';
    public string $faq_answer = '';
    public string $portfolio_title = '';
    public string $portfolio_image = '';
    public string $portfolio_category = 'wedding';

    public function mount(): void
    {
        abort_unless(auth()->user()?->canInOrganization('settings.manage', Tenant::current()), 403);
        $this->loadFields();
    }

    public function loadFields(): void
    {
        $org = $this->organization();
        $this->fields = SiteContent::withoutTenant()
            ->where('organization_id', $org->id)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->key => (string) $row->value])
            ->all();
    }

    public function saveCopy(): void
    {
        $org = $this->organization();
        foreach ($this->fields as $key => $value) {
            SiteContent::withoutTenant()->where('organization_id', $org->id)->where('key', $key)->update([
                'value' => $value,
            ]);
        }
        SiteCopy::forget($org->id);
        session()->flash('status', 'Website copy saved. The public site now shows your words.');
    }

    public function addTeamMember(): void
    {
        $this->validate(['team_name' => ['required', 'string', 'max:120'], 'team_role' => ['nullable', 'string', 'max:120']]);
        TeamMember::query()->create([
            'organization_id' => $this->organization()->id,
            'name' => $this->team_name,
            'role' => $this->team_role,
            'is_published' => true,
            'sort_order' => TeamMember::query()->count() + 1,
        ]);
        $this->reset('team_name', 'team_role');
    }

    public function deleteTeamMember(int $id): void
    {
        TeamMember::query()->whereKey($id)->delete();
    }

    public function addTestimonial(): void
    {
        $this->validate([
            'testimonial_author' => ['required', 'string', 'max:120'],
            'testimonial_quote' => ['required', 'string'],
        ]);
        Testimonial::query()->create([
            'organization_id' => $this->organization()->id,
            'author' => $this->testimonial_author,
            'role' => $this->testimonial_role,
            'quote' => $this->testimonial_quote,
            'is_published' => true,
            'rating' => 5,
        ]);
        $this->reset('testimonial_author', 'testimonial_role', 'testimonial_quote');
    }

    public function deleteTestimonial(int $id): void
    {
        Testimonial::query()->whereKey($id)->delete();
    }

    public function addFaq(): void
    {
        $this->validate(['faq_question' => ['required'], 'faq_answer' => ['required']]);
        Faq::query()->create([
            'organization_id' => $this->organization()->id,
            'question' => $this->faq_question,
            'answer' => $this->faq_answer,
            'is_published' => true,
        ]);
        $this->reset('faq_question', 'faq_answer');
    }

    public function deleteFaq(int $id): void
    {
        Faq::query()->whereKey($id)->delete();
    }

    public function addPortfolio(): void
    {
        $this->validate(['portfolio_title' => ['required'], 'portfolio_image' => ['required', 'url']]);
        PortfolioItem::query()->create([
            'organization_id' => $this->organization()->id,
            'title' => $this->portfolio_title,
            'slug' => \Illuminate\Support\Str::slug($this->portfolio_title).'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4)),
            'image_path' => $this->portfolio_image,
            'category' => $this->portfolio_category ?: 'wedding',
            'is_published' => true,
            'sort_order' => PortfolioItem::query()->count() + 1,
        ]);
        $this->reset('portfolio_title', 'portfolio_image');
    }

    public function deletePortfolio(int $id): void
    {
        PortfolioItem::query()->whereKey($id)->delete();
    }

    public function render()
    {
        $org = $this->organization();
        Tenant::set($org->id);

        $groups = SiteContent::withoutTenant()
            ->where('organization_id', $org->id)
            ->orderBy('sort_order')
            ->get()
            ->reject(fn ($row) => str_starts_with((string) $row->key, 'home.instagram'))
            ->groupBy('group');

        return view('livewire.platform.website-editor', [
            'groups' => $groups,
            'team' => TeamMember::query()->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::query()->latest()->get(),
            'faqs' => Faq::query()->orderBy('sort_order')->get(),
            'portfolio' => PortfolioItem::query()->orderBy('sort_order')->get(),
            'packages' => Package::query()->orderBy('sort_order')->get(),
        ]);
    }

    protected function organization(): Organization
    {
        return Tenant::current()
            ?? Organization::query()->where('is_active', true)->firstOrFail();
    }
}
