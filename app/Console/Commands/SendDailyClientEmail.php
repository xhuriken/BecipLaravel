<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\DailyClientEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Grosv\LaravelPasswordlessLogin\LoginUrl;

class SendDailyClientEmail extends Command
{
    protected $signature = 'email:daily_client';
    protected $description = 'Envoie un email aux clients avec les nouveaux plans validés.';

    public function handle()
    {
        $clients = User::where('role', 'client')->get();

        foreach ($clients as $client) {
            $projects = $client->projectsClients()
                ->with(['files' => function ($query) {
                    $query->where('is_validated', true)
                        ->where('validated_time', '>=', Carbon::now()->subDay()) // Fichiers validés < 24h
                        ->with(['uploadedBy' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->get();

            $hasNewFiles = $projects->contains(function ($project) {
                return $project->files->isNotEmpty();
            });

            if (!$hasNewFiles) {
                continue;
            }
            \URL::forceRootUrl(config('app.url'));
            foreach ($projects as $project) {
                $generator = new LoginUrl($client);
                $generator->setRedirectUrl("/projects/project/{$project->id}");
                $project->passwordless_url = $generator->generate();
            }

            Mail::to($client->email)->send(new DailyClientEmail($client, $projects));
        }

        $this->info('Emails clients envoyés avec succès.');
    }
}
