<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function update(File $file, Request $request): \Illuminate\Http\JsonResponse
    {
        // Récupérer les champs à mettre à jour
        $data = $request->validate([
            'field' => 'required|in:is_last_index,type,comment,is_validated',
            'value' => 'required'
        ]);

        // Vérifier l’autorisation selon le champ
        switch ($data['field']) {
            case 'is_last_index':
                // Seuls BECIP (drawer/engineer/secretary/engineer ???) peuvent modifier
                if (!Auth::user()->isBecip()) {
                    return response()->json(['error' => 'Non autorisé à modifier la révision'], 403);
                }
                $file->is_last_index = $data['value'] == 'true';
                break;

            case 'type':
                // Seuls BECIP peuvent modifier
                if (!Auth::user()->isBecip()) {
                    return response()->json(['error' => 'Non autorisé à modifier le type'], 403);
                }
                $file->type = $data['value'];
                break;

            case 'comment':
                // Seul l'user qui a déposé le fichier OU un ingénieur peut modifier
                if (Auth::id() !== $file->user_id && Auth::user()->role !== 'engineer') {
                    return response()->json(['error' => 'Non autorisé à modifier le commentaire'], 403);
                }
                $file->comment = $data['value'];
                break;

            case 'is_validated':
                // Seuls les ingénieurs peuvent valider
                if (Auth::user()->role !== 'engineer') {
                    return response()->json(['error' => 'Non autorisé à valider'], 403);
                }
                $file->is_validated = $data['value'] == 'true';
                break;
        }

        $file->save();

        return response()->json(['success' => true]);
    }

    public function delete(File $file, Request $request): \Illuminate\Http\JsonResponse
    {
        \Log::info('Tentative de suppression du fichier : ', ['file_id' => $file->id]);

        $relativePath = $file->project_id . '/' . $file->extension . '/' . $file->name;

        if (Storage::disk('public')->exists($relativePath)) {
            \Log::info("Fichier trouvé, suppression en cours : " . $file->name);
            //Storage delete
            Storage::disk('public')->delete($relativePath);
            \Log::info("Fichier du projet supprimé : " . $file->name);
        } else {
            \Log::warning("Fichier non trouvé : " . $relativePath);
        }

        //BDD delete
        $file->delete();
        return response()->json(['success' => true]);
    }
}
