@extends('layouts.app')

@section('content')
<div class="container-page project">
    {{-- comment mettre en place ce 'success' ? --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{route("home")}}" class="btn-return"><i class="fa-solid fa-arrow-left"></i> Retour</a>

    <h1>Détails de l'affaire</h1>
    <header>
        <div class="affinfo"><!-- Pour le css plus tard -->
            <p><strong>Entreprise : </strong>
                {{$company}}
            </p>
            <p><strong>Nom de l'affaire : </strong>{{$project->name}}</p>
            <p><strong>Ingénieur référent : </strong>{{$referent}}</p>
            <p><strong>Clients : </strong>{{ $clients->pluck('name')->join(', ') }}</p>
        </div>
        @if(auth()->user()->role == 'engineer')
            <div class="masks"> <!--need flex-->
                <div class="mask">
                    <input type="checkbox" data-label="mask-valid" {{ $project->is_mask_valided ? 'checked' : '' }}>
                    <label>Masquer les non validé au clients</label>
                </div>
                <div class="mask">
                    <input type="checkbox" data-label="mask-distrib" {{ $project->is_mask_distributed ? 'checked' : '' }}>
                    <label>Masquer la distribution</label>
                </div>
            </div>
        @endif
        @if(auth()->user()->isBecip())
            <div id="project-container" data-project-id="{{ $project->id }}" data-route="{{ route('projects.upload', $project->id) }}">
                <h2>Upload de fichiers</h2>
                <!-- Drag & Drop -->
                <div id="dropzone" class="dropzone" style="border: 2px dashed #ccc; padding: 20px; text-align: center; cursor: pointer;">
                    Glissez-déposez vos fichiers ici ou cliquez pour sélectionner.
                    <input type="file" id="file-input" name="files[]" multiple style="display: none;">
                </div>
            </div>
        @endif


        <table id="files-table" class="table-responsive table table-striped" style="width:100%">
            <thead>
                <tr>
                    @if(auth()->user()->isBecip())
                        <th data-label="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </th>
                    @endif
                    <th>
                        Rev
                    </th>
                    <th>
                        Nom
                    </th>
                    <th>
                        Type
                    </th>
                    <th>Commentaire</th>
                    <th>Déposé par</th>
                    <th>
                        Date
                    </th>
                    <th>
                        Validé
                    </th>
                    <th data-label="Télécharger">
                        <i class="fa-solid fa-download"></i>
                    </th>
                    @if(!$project->is_mask_distributed)
                        <th data-label="Distribuer">
                            <i class="fa-solid fa-print"></i>
                        </th>
                        <th data-label="Impressions">
                            <i class="fa-solid fa-sheet-plastic"></i>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                    {{--j'en fait une fonction ?--}}
                    @php
                        $hideFile = auth()->user()->role === 'client' && $project->is_mask_valided && !$file->is_validated;
                    @endphp
                    @if (!$hideFile)
                    <tr data-id="{{$file->id}}">
                        @if(auth()->user()->isBecip())
                            <td data-label="Delete" class="icon-cell">
                                <a href="#" class="delete-file-btn" data-delete-url="{{ route('file.delete', $file) }}">
                                    <i class="fa-solid fa-trash delete-icon"></i>
                                </a>
                            </td>
                        @endif
                        <td data-label="Révision">
                            @if(auth()->user()->role == 'drawer' || auth()->user()->role == 'engineer')
                                <input
                                    type="checkbox"
                                    class="update-last-index"
                                    {{ $file->is_last_index ? 'checked' : '' }}
                                />
                            @else
                                <input
                                    type="checkbox"
                                    class="update-last-index"
                                    disabled
                                    {{ $file->is_last_index ? 'checked' : '' }}
                                />
                            @endif
                        </td>
                        <td data-label="Nom">{{$file->name}}</td>
                        <td data-label="Type">
                            @if(auth()->user()->role == 'drawer' || auth()->user()->role == 'engineer')
                                <select class="file-type-select form-control">
                                    <option value="undefine"        {{ $file->type === 'undefine' ? 'selected' : ''}}>      Pas définie</option>
                                    <option value="coffrage"        {{ $file->type === 'coffrage' ? 'selected' : ''}}>      Coffrage</option>
                                    <option value="ferraillage"     {{ $file->type === 'ferraillage' ? 'selected' : ''}}>   Ferraillage</option>
                                    <option value="divers"          {{ $file->type === 'divers' ? 'selected' : ''}}>        Divers</option>
                                </select>
                            @else
                                {{$file->type}}
                            @endif
                        </td>
                        <td data-label="Commentaire">
                            @if($file->user_id == auth()->user()->id)
                                <textarea placeholder="Ajoutez un commentaire..." name="comment[{{$file->id}}]" class="comment-textarea form-control">{{$file->comment}}</textarea>
                            @else
                                @php
                                    $comment = $file->comment;
                                    if (strlen($comment) > 10) {
                                        $shortComment = substr($comment, 0, 10) . '...';
                                    } else {
                                        $shortComment = $comment;
                                    }
                                @endphp

                                {{ $shortComment }}

                                @if(strlen($comment) > 10)
                                    <a href="#" class="view-comment" data-comment="{{ $comment }}">Voir plus</a>
                                @endif
                            @endif
                        </td>
                        <td data-label="Déposé par">
                            {{--Seulement si l'utilisateur est supprimé--}}
                            {{ $file->uploadedBy ? $file->uploadedBy->name : 'Inconnu' }}
                        </td>
                        <td>
                            {{date('d/m/Y', strtotime($file->created_at))}}
                        </td>
                        <td data-label="Validé">
                            @if(auth()->user()->role == 'engineer')
                            <input type="checkbox" class="is-validated-checkbox" {{ $file->is_validated ? 'checked' : ''}}>
                            @else
                            <input type="checkbox" class="is-validated-checkbox" {{ $file->is_validated ? 'checked' : ''}} disabled>
                            @endif
                        </td>
                        <td data-label="Télécharger">
                            @if(auth()->user()->role == 'engineer' || $file->is_validated)
                                <input type="checkbox" name="download_files[]" value="{{$file->id}}">
                            @else
                                <input type="checkbox" name="download_files[]" value="{{$file->id}}" disabled>
                            @endif
                        </td>
                        @if(!$project->is_mask_distributed)
                            <td data-label="Distribuer">
                                <input type="checkbox" name="print_files[]" value="{{$file->id}}"
                                       class="distribution-checkbox"
                                        {{($file->distribution_count >= 1) ? 'disabled' : '' }}
                                />
                                @if($file->distribution_count >= 1)
                                    {{--Trouver un moyen de faire des tooltip avec DataTable--}}
                                @endif
                            </td>
                            <td data-label="Impressions">
                                {{$file->distribution_count}}
                            </td>
                        @endif
                    </tr>
                    @endif
                @empty
                    <tr class="no-data">
{{--                        <td colspan="{{ auth()->user()->isBecip() ? 12 : 11 }}">Aucun fichier</td>--}}
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        @if(auth()->user()->isBecip())
                            <td></td>
                        @endif
                    </tr>
                @endforelse
            </tbody>
        </table>
        <button id="download-btn" class="btn btn-primary">Download</button>

        <button id="distribute-btn" class="btn btn-warning">Distribute</button>
    </header>

    {{--Comment 'Voir Plus' Modal--}}
    <div class="modal fade" id="commentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Commentaire complet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="commentModalText"></p>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        window.fileUpdateRoute = '{{ route("files.update", ["file" => "FILE_ID"]) }}';
        window.downloadProjectUrl = '{{ route("projects.download") }}';
        window.distributeProjectUrl = '{{ route("projects.distribute") }}';
        window.maskValidedRoute = '{{ route("projects.updateMaskValidated") }}';
        window.maskDistributedRoute = '{{ route("projects.updateMaskDistributed") }}';
    </script>
@endsection
