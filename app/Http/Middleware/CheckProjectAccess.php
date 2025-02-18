<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $projectId = $request->route('id');

        $project = Project::find($projectId);
        if (!$project) {
            abort(404, 'Projet introuvable.');
        }

        if ($user->isBecip() || $user->projectsRelation()->where('projects.id', $projectId)->exists()) { //projectsRelation, problème de type
            return $next($request);
        }

        abort(403, 'Accès interdit.');
    }
}
