<?php

namespace App\Http\Controllers;

use App\Mail\FileDistributionMail;
use App\Models\File;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use ZipArchive;

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

        $smallYear = $dateTime->format('y');

        // Cherche le plus grand numéro existant pour l'année demandée
        $lastProject = Project::where('name', 'LIKE', "B$smallYear.%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(name, '.', -1) AS UNSIGNED) DESC")
            ->first();

        // Détermine le numéro de départ
        $startIndex = 1;
        if ($lastProject) {
            $lastIndex = (int) substr($lastProject->name, strrpos($lastProject->name, '.') + 1);
            $startIndex = $lastIndex + 1;
        }

        $projects = [];

        for ($i = $startIndex; $i < $startIndex + $quantity; $i++) {
            $counter = str_pad($i, 3, '0', STR_PAD_LEFT);

            $projects[] = [
                'name' => "B$smallYear.$counter",
                "created_at" => $now->format('Y-m-d H:i:s'),
                "updated_at" => $now->format('Y-m-d H:i:s'),
            ];
        }

        Project::insert($projects);

        return redirect()->route('home');
    }

    /**
     * Delete all empty Project
     * @return JsonResponse
     */
    public function deleteEmptyProject(Request $request): JsonResponse
    {
        $emptyProjects = Project::whereNull('referent_id')
            ->whereNull('company_id')
            ->get(['id']);

        $ids = $emptyProjects->pluck('id');

        foreach ($emptyProjects as $project) {
            $projectFolder = storage_path('app/public/' . $project->id);
            if (is_dir($projectFolder)) {
                \Log::info("Dossier trouvé pour l'affaire vide {$project->id}, suppression en cours : $projectFolder");
                $this->deleteFolder($projectFolder);
                \Log::info("Dossier de l'affaire vide {$project->id} supprimé : $projectFolder");
            } else {
                \Log::warning("Dossier non trouvé pour l'affaire vide {$project->id} : $projectFolder");
            }

            // Delete the project
            $project->delete();
        }

        return response()->json([
            'success' => true,
            'deleted_ids' => $ids
        ]);
    }


    /**
     * Delete specific Project AND files inside (BDD and Storage)
     * @param Project $project
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Project $project, Request $request): JsonResponse
    {
        \Log::info('Tentative de suppression du projet : ', ['project_id' => $project->id]);

        //delete folder too
        $projectFolder = storage_path('app/public/' . $project->id);

        // Vérification et suppression du dossier
        if (is_dir($projectFolder)) {
            \Log::info("Dossier trouvé, suppression en cours : $projectFolder");
            $this->deleteFolder($projectFolder);
            \Log::info("Dossier du projet supprimé : $projectFolder");
        } else {
            \Log::warning("Dossier non trouvé : $projectFolder");
        }

        $project->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Delete Folder and content inside
     */
    private function deleteFolder(string $folderPath): void {
        $files = array_diff(scandir($folderPath), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->deleteFolder($filePath);
            } else {
                unlink($filePath);
            }
        }
        rmdir($folderPath);
    }

    /**
     * Store (add) new Project
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        \Log::info('Données reçues : ', $request->all());

        try {
            $validated = $request->validate([
                'company_id' => 'nullable|exists:companies,id',
                'project_namelong' => 'nullable|string|max:255',
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
            'namelong' => $validated['project_namelong'],
            'name' => $validated['project_name'],
            'referent_id' => $validated['engineer_id'],
        ]);

        if (!empty($validated['clients'])) {
            $project->clients()->sync($validated['clients']);
        }

        \Log::info("Projet créé avec ID : {$project->id}");

        return response()->json([
            'success' => true,
            'project_id' => $project->id,
            'project_url' => route('projects.project', $project),
            'delete_url' => route('projects.delete', $project),
            'company_name' => $project->company_id ? $project->getCompanyName($project->company_id) : 'Aucune',
            'referent_name' => $project->getReferentName($project->referent_id),
            'editable' => auth()->user()->isBecip(),
            'can_edit' => auth()->user()->isEngineerOrSecretary(),
        ]);
    }

    /**
     * Delete Projects selected
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteSelected(Request $request)
    {
        $request->validate([
            'selected_projects' => 'required|array',
            'selected_projects.*' => 'exists:projects,id'
        ]);

        $projects = Project::whereIn('id', $request->selected_projects)->get();

        foreach ($projects as $project) {
            // Delete folder if exists
            $projectFolder = storage_path('app/public/' . $project->id);
            if (is_dir($projectFolder)) {
                \Log::info("Dossier trouvé pour l'affaire {$project->id}, suppression en cours : $projectFolder");
                $this->deleteFolder($projectFolder);
                \Log::info("Dossier de l'affaire {$project->id} supprimé : $projectFolder");
            } else {
                \Log::warning("Dossier non trouvé pour l'affaire {$project->id} : $projectFolder");
            }

            // Delete the project
            $project->delete();
        }

        return response()->json(['message' => 'Les affaires sélectionnées ont été supprimées !']);
    }


    /**
     * Upload Files in storage and BDD in a project
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function uploadFiles(Request $request, Project $project)
    {
        $request->validate([
            'files.*' => 'required|file'
        ]);

        $uploadedFiles = $request->file('files');
        $storedFiles = [];

        foreach ($uploadedFiles as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $directory = $project->id . '/' . $extension;

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = Str::slug($originalName) . '.' . $extension;

            if (Storage::disk('public')->exists("$directory/$safeName")) {
                //si le nom existe déjà on rajoute une date pour évité l'écrasement ou erreur
                $safeName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            }

            $storedPath = $file->storeAs($directory, $safeName, 'public');
            $storedFiles[] = $storedPath;

            \App\Models\File::create([
                'project_id' => $project->id,
                'user_id'    => auth()->user()->id,
                'name'       => $safeName,
                'extension'  => $extension,
                'is_last_index' => 1,
            ]);
        }

        return response()->json(['success' => true, 'files' => $storedFiles]);
    }

    /**
     * Update fields of a specific project id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'project_namelong' => 'nullable|string|max:255',
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
            'namelong' => $validated['project_namelong'],
            'name' => $validated['project_name'],
            'company_id' => $validated['company_id'],
            'referent_id' => $validated['referent_id'],
            'address' => $validated['address'],
            'comment' => $validated['comment'],
        ]);

        if (!empty($validated['clients'])) {
            $project->clients()->sync($validated['clients']);
        } else {
            $project->clients()->detach();
        }

        return response()->json(['success' => true]);
    }

    public function downloadFiles(Request $request)
    {
        // 1) Récupération des infos
        $fileIds   = $request->input('file_ids', []);
        $projectId = $request->input('project_id');

        if (empty($fileIds) || !$projectId) {
            return response()->json(['error' => 'No files selected or project missing.'], 422);
        }

        // 2) Récupérer les fichiers en base
        $files = File::where('project_id', $projectId)
            ->whereIn('id', $fileIds)
            ->get();

        if ($files->isEmpty()) {
            return response()->json(['error' => 'No files found.'], 404);
        }

        // 3) Créer un Zip en mémoire (fichier temporaire)
        $zipName = 'Download_' . now()->format('Ymd_His') . '_Project' . $projectId . '.zip';
        $tmpZipPath = tempnam(sys_get_temp_dir(), 'zip'); // ex: /tmp/zip1234

        $zip = new ZipArchive();
        if ($zip->open($tmpZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            Log::error("Cannot create ZIP at $tmpZipPath");
            return response()->json(['error' => 'Cannot create ZIP.'], 500);
        }

        // 4) Ajouter les fichiers dans le ZIP
        foreach ($files as $file) {
            $relPath = $file->project_id . '/' . $file->extension . '/' . $file->name;
            $absPath = storage_path('app/public/' . $relPath);

            if (file_exists($absPath)) {
                // Nettoie les caractères spéciaux si besoin
                $safeName = preg_replace('/[^\w\-.]/', '_', $file->name);
                $zip->addFile($absPath, $safeName);
                Log::info("Added to ZIP: $absPath as $safeName");
            } else {
                Log::warning("File not found: $absPath");
            }
        }

        // 5) Fermer le ZIP
        $zip->close();

        // 6) Vérifier que le ZIP n’est pas vide
        if (!filesize($tmpZipPath)) {
            Log::error("ZIP is empty or not created properly.");
            return response()->json(['error' => 'ZIP is empty.'], 500);
        }

        Log::info("ZIP created at $tmpZipPath, size = " . filesize($tmpZipPath));

        // 7) Renvoyer le ZIP en binaire avec headers corrects
        return response()->file($tmpZipPath, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.$zipName.'"',
        ])->deleteFileAfterSend(true);
    }

    public function multipleDownloadPage(Request $request)
    {
        $fileIds   = $request->input('file_ids', []);
        $projectId = $request->input('project_id');

        // Vérifier si rien n'a été envoyé
        if (empty($fileIds) || !$projectId) {
            return back()->with('error', 'Aucun fichier sélectionné ou projet manquant.');
        }

        // Récupérer les fichiers correspondants
        $files = File::where('project_id', $projectId)
            ->whereIn('id', $fileIds)
            ->get();

        if ($files->isEmpty()) {
            return back()->with('error', 'Aucun fichier trouvé.');
        }

        // On peut loguer pour debug
        Log::info("Multiple download page with ".count($files)." file(s).");

        // Retourner la vue qui déclenche les téléchargements un par un
        return view('download', compact('files'));
    }


    /**
     * Send mail to all secretary user with download link
     * @param Request $request
     * @return JsonResponse
     */
    public function distributeFiles(Request $request)
    {
        $fileIds = $request->input('file_ids');
        $projectId = $request->input('project_id');
        $requester = auth()->user();

        if (empty($fileIds) || empty($projectId)) {
            return response()->json(['error' => 'No files selected or project ID missing.'], 422);
        }

        // get project
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        // get fiels
        $files = File::whereIn('id', $fileIds)->where('project_id', $projectId)->get();
        if ($files->isEmpty()) {
            return response()->json(['error' => 'No valid files found.'], 404);
        }
        $fileNames = $files->pluck('name')->toArray(); // get names


        foreach ($files as $file) {
            if ($file->distribution_count < 1) {
                $file->distribution_count += 1;
                $file->save();
            }
        }
        // get all secretary users
        $secretaries = User::where('role', 'secretary')->get();
        if ($secretaries->isEmpty()) {
            return response()->json(['error' => 'No secretary users found.'], 422);
        }

        // generate downloadLink
        $downloadLink = route('files.download', ['project_id' => $projectId, 'file_ids' => implode(',', $fileIds)]);

        // and send mail
        foreach ($secretaries as $secretary) {
            Mail::to($secretary->email)->send(new FileDistributionMail(
                $secretary->name,  // Secretary name
                $project->name,    // Project name
                $requester->name,  // User who made the request
                $project->address, // address
                $fileNames,        // List of file names
                $downloadLink      // Download link
            ));
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update checkbox mask Validated
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMaskValidated(Request $request)
    {
        $project = Project::find($request->project_id);

        if (!$project) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        $project->update([
            'is_mask_valided' => $request->is_mask_valided ? 1 : 0,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update Checkbox mask Distributed
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMaskDistributed(Request $request)
    {
        $project = Project::find($request->project_id);

        if (!$project) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        $project->update([
            'is_mask_distributed' => $request->is_mask_distributed ? 1 : 0,
        ]);

        return response()->json(['success' => true]);
    }

}
