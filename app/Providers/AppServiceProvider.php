<?php

namespace App\Providers;

use App\Mail\Transport\MicrosoftGraphTransport;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ResearchSubject;
use App\Policies\ApplicationDocumentPolicy;
use App\Policies\ApplicationPolicy;
use App\Policies\ResearchSubjectPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(ResearchSubject::class, ResearchSubjectPolicy::class);
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(ApplicationDocument::class, ApplicationDocumentPolicy::class);

        Mail::extend('graph', function () {
            $config = config('services.microsoft_graph');

            return new MicrosoftGraphTransport(
                tenantId: $config['tenant_id'],
                clientId: $config['client_id'],
                clientSecret: $config['client_secret'],
                sender: $config['sender'],
            );
        });
    }
}
