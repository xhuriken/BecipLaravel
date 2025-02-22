<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index($id)
    {
        $project = Project::find($id);
        return view('project', [
            'id'            => $id,
            'project'       => $project,
            'company'       => Project::getCompanyName($project->company_id),
            'company_id'    => $project->company_id,
            'referent'      => Project::getReferentName($project->referent_id),
            'clients'       => $project->clients,
            'files'         => $project->files
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

    public function delete(Project $project, Request $request) {
        \Log::info('Tentative de suppression du projet : ', ['project_id' => $project->id]);

        if (!$project) {
            return response()->json(['error' => 'Projet introuvable.'], 404);
        }

        $project->delete();
        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        \Log::info('Données reçues : ', $request->all());

        try {
            $validated = $request->validate([
                'company_id' => 'nullable|exists:companies,id',
                'project_name' => 'required|string|max:255|unique:projects,name',
                'engineer_id' => 'nullable|exists:users,id',
                'clients' => 'nullable|array',
                'clients.*' => 'exists:users,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($e->validator->errors()->has('project_name')) {
                return response()->json([
                    'error' => 'Ce nom d\'affaire existe déjà !'
                ], 422);
            }
            return response()->json(['error' => 'Erreur dans les données envoyées.'], 422);
        }

        $project = Project::create([
            'company_id' => $validated['company_id'],
            'name' => $validated['project_name'],
            'referent_id' => $validated['engineer_id'] ?? auth()->id(),
        ]);

        if (!empty($validated['clients'])) {
            $project->clients()->sync($validated['clients']);
        }

        \Log::info("Projet créé avec ID : {$project->id}");

        return response()->json(['success' => true, 'project_id' => $project->id]);
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'selected_projects' => 'required|array',
            'selected_projects.*' => 'exists:projects,id'
        ]);

        Project::whereIn('id', $request->selected_projects)->delete();

        return response()->json(['message' => 'Les affaires sélectionnée ont été supprimées !']);
    }

    public function uploadFiles(Request $request, Project $project)
    {
        $request->validate([
            'files.*' => 'required|file'
        ]);

        $uploadedFiles = $request->file('files');
        $storedFiles = [];

        foreach ($uploadedFiles as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            $projectName = $project->name;

            $directory = $projectName . '/' . $extension;

            $storedPath = $file->storeAs($directory, $file->getClientOriginalName(), 'public');
            $storedFiles[] = $storedPath;

            \App\Models\File::create([
                'project_id' => $project->id,
                'user_id'    => auth()->user()->id,
                'name'       => $file->getClientOriginalName(),
                'extension'  => $extension,
                'is_last_index' => 1,
            ]);
        }

        return response()->json(['success' => true, 'files' => $storedFiles]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'project_name' => 'required|string|max:255|unique:projects,name,' . $request->project_id,
            'company_id'   => 'nullable|exists:companies,id',
            'referent_id'  => 'nullable|exists:users,id',
            'address'      => 'nullable|string|max:255',
            'comment'      => 'nullable|string',
            'clients'      => 'nullable|array',
            'clients.*'    => 'exists:users,id'
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $project->update([
            'name' => $validated['project_name'],
            'company_id' => $validated['company_id'],
            'referent_id' => $validated['referent_id'],
            'address' => $validated['address'],
            'comment' => $validated['comment'],
        ]);

        // Synchroniser les clients sélectionnés
        if (!empty($validated['clients'])) {
            $project->clients()->sync($validated['clients']);
        } else {
            $project->clients()->detach();
        }

        return response()->json(['success' => true]);
    }



}
