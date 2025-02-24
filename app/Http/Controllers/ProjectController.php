<?php

namespace App\Http\Controllers;

use App\Mail\FileDistributionMail;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Http\JsonResponse;
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

    public function delete(Project $project, Request $request): JsonResponse
    {
        \Log::info('Tentative de suppression du projet : ', ['project_id' => $project->id]);

//        if (!$project) {
//            return response()->json(['error' => 'Projet introuvable.'], 404);
//        }

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
     * Supprime un dossier et tout son contenu
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

            $directory = $project->id . '/' . $extension;

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

    public function downloadFiles(Request $request)
    {
        $fileIds = $request->input('file_ids'); // Array of file IDs
        $projectId = $request->input('project_id'); // Project ID

        if (empty($fileIds) || empty($projectId)) {
            return response()->json(['error' => 'No files selected.'], 422);
        }

        // Retrieve file records (adjust your File model as needed)
        $files = \App\Models\File::whereIn('id', $fileIds)->where('project_id', $projectId)->get();
        \Log::info("téléchargement initié avec " . $files->count() .' fichier');

        if ($files->count() == 0) {
            return response()->json(['error' => 'Files not found.'], 404);
        }

        if ($files->count() == 1) {
            // Download the single file
            $file = $files->first();
            $relativePath = $file->project_id . '/' . $file->extension . '/' . $file->name;
            if (Storage::disk('public')->exists($relativePath)) {
                \Log::info($relativePath);
                return response()->download(storage_path('app/public/' . $relativePath));
            } else {
                return response()->json(['error' => 'File does not exist.'], 404);
            }
        } else {
            // Create a ZIP file for multiple files
            $zip = new ZipArchive();
            $zipName = 'files_' . Carbon::now()->format('Y-m-d_H-i-s') . '_' . $projectId . '.zip';
            $zipPath = storage_path('app/public/' . $zipName);

            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                return response()->json(['error' => 'Error creating ZIP file.'], 500);
            }

            foreach ($files as $file) {
                $relativePath = $file->project_id . '/' . $file->extension . '/' . $file->name;
                if (Storage::disk('public')->exists($relativePath)) {
                    // Add file to ZIP; second parameter sets the name inside ZIP
                    $zip->addFile(storage_path('app/public/' . $relativePath), $file->name);
                    \Log::info($relativePath. 'zip');

                }
            }
            $zip->close();

            // Return ZIP for download and then delete the ZIP file after sending
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
    }

    // Distribute selected files: send email to all secretaries.
    public function distributeFiles(Request $request)
    {
        $fileIds = $request->input('file_ids'); // Array of file IDs
        $projectId = $request->input('project_id'); // Project ID
        $requesterId = auth()->id();

        if (empty($fileIds) || empty($projectId)) {
            return response()->json(['error' => 'No files selected.'], 422);
        }

        // Get project and requester info
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Project not found.'], 404);
        }

        $requester = auth()->user();

        // Get files names
        $files = \App\Models\File::whereIn('id', $fileIds)->where('project_id', $projectId)->get();
        $fileNames = $files->pluck('name')->toArray();

        // Get secretary users
        $secretaries = \App\Models\User::where('role', 'secretary')->get();
        if ($secretaries->isEmpty()) {
            return response()->json(['error' => 'No secretary users found.'], 422);
        }

        // Send email to each secretary using a Mailable class
        foreach ($secretaries as $secretary) {
            Mail::to($secretary->email)->send(new FileDistributionMail([
                'secretaryName'   => $secretary->name,
                'requesterName'   => $requester->name,
                'projectName'     => $project->name,
                'fileNames'       => $fileNames,
                // You can also add a link to download all files if needed.
                'downloadLink'    => route('projects.download', ['project' => $project->id]) // or a custom route with file IDs
            ]));
        }

        return response()->json(['success' => true]);
    }

}
