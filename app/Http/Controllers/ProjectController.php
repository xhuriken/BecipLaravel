<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(int $id)
    {
        $project = Project::findOrFail($id);
        return view('project', [
            'id' => $id,
            'project' => $project,
            'company' => Project::getCompanyName($project->company_id),
            'referent' => Project::getReferentName($project->referent_id),
            'clients' => Project::getAllClients($id),
            'files' => Project::getAllFiles($id)
        ]);
    }

    /**
     * @param int $quantity
     * @param int $year
     * @return RedirectResponse
     */
    public function generate(int $quantity, int $year)
    {
        $dateTime = Carbon::now()->setYear($year);
        $now = Carbon::now();
        $projects = [];

        for ($i = 1; $i <= $quantity; $i++) {
            $counter = str_pad($i, 3, '0', STR_PAD_LEFT);

            $smallYear = $dateTime->format('y');
            $projects[] = [
                'name' => "B$smallYear.$counter",
                "created_at" => $now->format('Y-m-d H:i:s'),
                "updated_at" => $now->format('Y-m-d H:i:s'),
            ];
        }

        Project::insert($projects);

        return redirect()->route('home');
    }


    public function deleteEmptyProject()
    {
        Project::whereNull('referent_id')
            ->whereNull('company_id')
            ->delete();

        return redirect()->back()->with('success', 'Les affaires vides ont été supprimées.');
    }

}
