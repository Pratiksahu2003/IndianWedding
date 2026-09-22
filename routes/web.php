<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Site\ConsultationController;
use App\Http\Controllers\Site\InquiryController;
use App\Http\Controllers\Site\WebsiteController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use App\Http\Controllers\Webhook\WhatsAppWebhookController;
use App\Livewire\Client\Dashboard as ClientDashboard;
use App\Livewire\Client\Gallery as ClientGallery;
use App\Livewire\Client\Payments as ClientPayments;
use App\Livewire\Client\Timeline as ClientTimeline;
use App\Livewire\Platform\Dashboard as PlatformDashboard;
use App\Livewire\Studio\Calendar\Index as CalendarIndex;
use App\Livewire\Studio\Clients\Index as ClientsIndex;
use App\Livewire\Studio\Consultations\Index as ConsultationsIndex;
use App\Livewire\Studio\Dashboard as StudioDashboard;
use App\Livewire\Studio\Files\Index as FilesIndex;
use App\Livewire\Studio\Galleries\Index as GalleriesIndex;
use App\Livewire\Studio\Invoices\Index as InvoicesIndex;
use App\Livewire\Studio\Leads\Form as LeadForm;
use App\Livewire\Studio\Leads\Index as LeadsIndex;
use App\Livewire\Studio\Leads\Pipeline as LeadsPipeline;
use App\Livewire\Studio\Leads\Show as LeadShow;
use App\Livewire\Studio\Messages\Index as MessagesIndex;
use App\Livewire\Studio\Packages\Index as PackagesIndex;
use App\Livewire\Studio\Payments\Index as PaymentsIndex;
use App\Livewire\Studio\Projects\Index as ProjectsIndex;
use App\Livewire\Studio\Projects\Show as ProjectShow;
use App\Livewire\Studio\Reports\Index as ReportsIndex;
use App\Livewire\Studio\Settings\Index as SettingsIndex;
use App\Livewire\Studio\Tasks\Index as TasksIndex;
use App\Livewire\Studio\Team\Index as TeamIndex;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);

Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/{page}', [WebsiteController::class, 'page'])
    ->whereIn('page', ['about', 'services', 'packages', 'portfolio', 'gallery', 'testimonials', 'faq', 'contact', 'book-consultation']);

Route::post('/inquiry', [InquiryController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('inquiry.store');
Route::post('/consultations', [ConsultationController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('consultation.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:8,1');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::post('/webhooks/payments/{provider}', PaymentWebhookController::class)->name('webhooks.payments');
Route::get('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'store']);

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/files/signed', [FileAccessController::class, 'signed'])->name('files.signed');
    Route::get('/files/{file}', [FileAccessController::class, 'show'])->name('files.show');
    Route::get('/invoices/{invoice}/pdf', InvoicePdfController::class)->name('invoices.pdf');

    Route::prefix('app')->name('app.')->group(function () {
        Route::get('/', StudioDashboard::class)->name('dashboard');
        Route::get('/leads', LeadsIndex::class)->name('leads.index');
        Route::get('/leads/pipeline', LeadsPipeline::class)->name('leads.pipeline');
        Route::get('/leads/create', LeadForm::class)->name('leads.create');
        Route::get('/leads/{lead}', LeadShow::class)->name('leads.show');
        Route::get('/clients', ClientsIndex::class)->name('clients.index');
        Route::get('/consultations', ConsultationsIndex::class)->name('consultations.index');
        Route::get('/packages', PackagesIndex::class)->name('packages.index');
        Route::get('/projects', ProjectsIndex::class)->name('projects.index');
        Route::get('/projects/{project}', ProjectShow::class)->name('projects.show');
        Route::get('/calendar', CalendarIndex::class)->name('calendar');
        Route::get('/team', TeamIndex::class)->name('team.index');
        Route::get('/tasks', TasksIndex::class)->name('tasks.index');
        Route::get('/payments', PaymentsIndex::class)->name('payments.index');
        Route::get('/invoices', InvoicesIndex::class)->name('invoices.index');
        Route::get('/galleries', GalleriesIndex::class)->name('galleries.index');
        Route::get('/files', FilesIndex::class)->name('files.index');
        Route::get('/messages', MessagesIndex::class)->name('messages.index');
        Route::get('/reports', ReportsIndex::class)->name('reports.index');
        Route::get('/settings', SettingsIndex::class)->name('settings.index');
    });

    Route::prefix('client')->name('client.')->middleware('role:client')->group(function () {
        Route::get('/', ClientDashboard::class)->name('dashboard');
        Route::get('/payments', ClientPayments::class)->name('payments');
        Route::get('/gallery', ClientGallery::class)->name('gallery');
        Route::get('/timeline', ClientTimeline::class)->name('timeline');
    });

    Route::prefix('platform')->name('platform.')->middleware('superadmin')->group(function () {
        Route::get('/', PlatformDashboard::class)->name('dashboard');
    });
});
