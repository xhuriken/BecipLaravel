<?php

namespace App\Http\Controllers;

use App\Models\File;
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

    /**
     * @param Project $project
     * @return RedirectResponse
     */
    public function delete(Project $project) {
        $project->delete();

        return redirect()->back();
    }

    public function store(Request $request)
    {
        \Log::info('Données reçues : ', $request->all());

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'project_name' => 'required|string|unique:projects,name|max:255',
            'engineer_id' => 'nullable|exists:users,id',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:users,id',
        ]);

        if (empty($validated['company_id']) || empty($validated['project_name'])) {
            return redirect()->back()->with('error', 'Erreur dans les données envoyées.');
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

        return redirect()->route('home')->with('success', 'Affaire ajoutée avec succès.');
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
        // Validation : chaque fichier doit être présent et être un fichier
        $request->validate([
            'files.*' => 'required|file'
        ]);

        $uploadedFiles = $request->file('files');
        $storedFiles = [];

        foreach ($uploadedFiles as $file) {
            // Récupérer l'extension en minuscules
            $extension = strtolower($file->getClientOriginalExtension());

            // On utilise le nom du projet tel quel, par exemple "B25.001"
            $projectName = $project->name;

            // Le chemin de stockage sera par exemple : "B25.001/png/"
            $directory = $projectName . '/' . $extension;

            // Stocker le fichier en conservant son nom original
            $storedPath = $file->storeAs($directory, $file->getClientOriginalName(), 'public');
            $storedFiles[] = $storedPath;

            // Enregistrer le fichier dans la table files
            File::create([
                'project_id' => $project->id,
                'user_id'    => auth()->user()->id,
                'name'       => $file->getClientOriginalName(),
                'extension'  => $extension,
                'is_last_index' => 1, // par défaut, si c'est la dernière version
            ]);
        }

        return response()->json(['success' => true, 'files' => $storedFiles]);
    }


}
