<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\DailyEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Grosv\LaravelPasswordlessLogin\LoginUrl;

class SendDailyEmail extends Command
{
    protected $signature = 'email:daily';
    protected $description = 'Envoie un email quotidien avec la liste des plans non validés.';

    public function handle()
    {
        $users = User::where('role', 'engineer')->get();

        foreach ($users as $user) {
            $ownProjects = Project::where('referent_id', $user->id)
                ->with(['files' => function($query) {
                    $query->where('is_validated', false)
                        ->select('id', 'name', 'project_id', 'user_id', 'created_at')
                        ->with(['uploadedBy' => function($query) {
                            $query->select('id', 'name');
                        }])
                        ->get()
                        ->each(function ($file) {
                            $file->setAttribute('uploaded_recently', $file->created_at->diffInHours(Carbon::now()) <= 24);
                            $file->setAttribute('uploader_name', $file->uploadedBy ? $file->uploadedBy->name : 'Utilisateur inconnu');
                        });
                }])
                ->get();

            $otherProjects = Project::where('referent_id', '!=', $user->id)
                ->with(['files' => function($query) {
                    $query->where('is_validated', false)
                        ->select('id', 'name', 'project_id', 'user_id', 'created_at')
                        ->with(['uploadedBy' => function($query) {
                            $query->select('id', 'name');
                        }])
                        ->get()
                        ->each(function ($file) {
                            $file->setAttribute('uploaded_recently', $file->created_at->diffInHours(Carbon::now()) <= 24);
                            $file->setAttribute('uploader_name', $file->uploadedBy ? $file->uploadedBy->name : 'Utilisateur inconnu');
                        });
                }])
                ->get();
            \URL::forceRootUrl(config('app.url'));
            foreach ($ownProjects as $project) {
                $generator = new LoginUrl($user);
                $generator->setRedirectUrl("/projects/project/{$project->id}");
                $project->passwordless_url = $generator->generate();
            }

            foreach ($otherProjects as $project) {
                $generator = new LoginUrl($user);
                $generator->setRedirectUrl("/projects/project/{$project->id}");
                $project->passwordless_url = $generator->generate();
            }
            Mail::to($user->email)->send(new DailyEmail($user, $ownProjects, $otherProjects));
        }

        $this->info('Emails envoyés avec succès avec les liens passwordless.');
    }


}
