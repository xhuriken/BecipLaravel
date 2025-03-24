@extends('layouts.app')

@section('content')
<div class="container-page project">
    <div class="section-loader">
        <div class="loader"></div>
        <p class="loading-text">Chargement en cours...</p>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{route("home")}}" class="btn-return"><i class="fa-solid fa-arrow-left"></i> Retour</a>

    <h1>Détails de l'affaire</h1>
    <header>
        <div class="affinfo"><!-- Pour le css plus tard -->
            <p><strong>Entreprise : </strong>{{$company}}</p>
            <p><strong>Nom : </strong>{{$project->namelong}}</p>
            <p><strong>Numéro : </strong>{{$project->name}}</p>
            <p><strong>Ingénieur référent : </strong>{{$referent}}</p>
            <p><strong>Clients : </strong>{{ $clients && !$clients->isEmpty() ? $clients->pluck('name')->join(', ') : 'Aucun' }}</p>
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
            <div id="project-container" data-project-id="{{ $project->id }}" data-route="{{ route('projects.upload', $project->id) }}">
                <h2>Upload de fichiers</h2>
                <!-- Drag & Drop -->
                @if(auth()->user()->isBecip())
                    <div id="dropzone" class="dropzone" style="border: 2px dashed #ccc; padding: 20px; text-align: center; cursor: pointer;">
                        Glissez-déposez vos fichiers ici ou cliquez pour sélectionner.
                        <input type="file" id="file-input" name="files[]" multiple style="display: none;">
                    </div>
                 @endif
            </div>


        <table id="files-table" class="table-responsive table table-striped" style="width:100%">
            <thead>
                <tr>
                    @if(auth()->user()->isBecip())
                        <th data-label="Delete" data-orderable="false">
                            <i class="fa-solid fa-trash"></i>
                        </th>
                    @endif
                    <th data-label="Rev">
                        Rev
                    </th>
                    <th data-label="Nom">
                        Nom
                    </th>
                    <th data-orderable="false" data-label="Type">
                        <select id="fileTypeFilter" class="form-select form-select-sm">
                            <option value="">Type</option>
                            <option value="coffrage">Coffrage</option>
                            <option value="ferraillage">Ferraillage</option>
                            <option value="divers">Divers</option>
                            <option value="undefine">Pas définie</option>
                        </select>
                    </th>
                    <th data-orderable="false"
                        data-label="Commentaire">
                        Commentaire
                    </th>
                    <th data-label="Déposé par">
                        Déposé par
                    </th>
                    <th data-label="Date">
                        Date
                    </th>
                    <th data-label="Validé">
                        <i class="fa-solid fa-check"></i>
                    </th>
                    <th data-label="Télécharger" data-orderable="false">
                        <i class="fa-solid fa-download"></i>
                    </th>
                    @if(!$project->is_mask_distributed)
                        <th data-label="Distribuer" data-orderable="false">
                            <i class="fa-solid fa-print"></i>
                        </th>
                        <th data-label="Impressions" data-orderable="false">
                            <i class="fa-solid fa-sheet-plastic"></i>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                    @php
                        $hideFile = auth()->user()->role === 'client' && $project->is_mask_valided && !$file->is_validated;
                    @endphp
                    @if (!$hideFile)
                    <tr data-id="{{$file->id}}">
                        @if(auth()->user()->isBecip())
                            <td data-label="Delete" class="icon-cell">
                                <a href="javascript:void(0);" class="delete-file-btn" data-delete-url="{{ route('file.delete', $file) }}" data-file-id="{{$file->id}}">
                                    <i class="fa-solid fa-trash delete-icon"></i>
                                </a>
                            </td>
                        @endif
                        <td data-label="Rev">
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
                        <td data-label="Type" data-search="{{ $file->type }}">
                            @if(auth()->user()->role == 'drawer' || auth()->user()->role == 'engineer')
                                <select class="file-type-select form-control">
                                    <option value="undefine" {{ $file->type === 'undefine' ? 'selected' : ''}}>Pas définie</option>
                                    <option value="coffrage" {{ $file->type === 'coffrage' ? 'selected' : ''}}>Coffrage</option>
                                    <option value="ferraillage" {{ $file->type === 'ferraillage' ? 'selected' : ''}}>Ferraillage</option>
                                    <option value="divers" {{ $file->type === 'divers' ? 'selected' : ''}}>Divers</option>
                                </select>
                            @else
                                {{ $file->type }}
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
                                    <br/>
                                    <a href="#" class="view-comment" data-comment="{{ $comment }}">Voir plus</a>
                                @endif
                            @endif
                        </td>
                        <td data-label="Déposé par">
                            {{--Seulement si l'utilisateur est supprimé--}}
                            {{ $file->uploadedBy ? $file->uploadedBy->name : 'Inconnu' }}
                        </td>
                        <td data-label="Date">
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
                                    <input type="checkbox" name="download_files[]"
                                           value="{{ $file->id }}"
                                           data-file-path="{{ asset("storage/{$file->project_id}/{$file->extension}/" . urlencode($file->name)) }}"
                                           data-filename="{{ $file->name }}"
                                    >
                                @else
                                    <input type="checkbox" name="download_files[]" value="{{$file->id}}" disabled>
                                @endif
                            </td>
                        @if(!$project->is_mask_distributed)
                            <td data-label="Distribuer">
                                <input type="checkbox" name="print_files[]" value="{{$file->id}}"
                                       class="distribution-checkbox"
                                        {{($file->distribution_count >= 1) || !$file->is_validated || auth()->user()->role === "drawer" ? 'disabled' : '' }}
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
                @endforelse
            </tbody>
        </table>

        <div class="project-action">
            <div class="action">
                <button id="download-btn" class="btn btn-primary">Télécharger</button>
            </div>
            <div class="action">
                <button id="distribute-btn" class="btn btn-primary">Demande d'impression</button>
            </div>
        </div>
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
<!-- Overlay popup -->
<div id="download-overlay" class="download-overlay">
    <div class="overlay-content">
        <h2>Veuillez patienter...</h2>
        <p>Le téléchargement de vos fichiers est en cours. Ne fermez pas la page avant la fin.</p>
        <div class="loader"></div>
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
