@extends('layouts.app')

@section('content')
<div class="container-page">
    {{-- comment mettre en place ce 'success' ? --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{route("home")}}" class="btn-return"><i class="fa-solid fa-arrow-left"></i> Retour</a>

    <h1>Détails de l'affaire</h1>
    <header>
        <div class="affinfo">
            <p><strong>Entreprise : </strong>
                @if($company_id == null)
                    Pas encore définit
                @else
                @endif
                    {{$company}}
            </p>

            <p><strong>Nom de l'affaire : </strong>{{$project->name}}</p>
            <p><strong>Ingénieur référent : </strong>{{$referent}}</p>
            <p><strong>Clients : </strong>{{ $clients->pluck('name')->join(', ') }}</p>
        </div>
        @if(auth()->user()->isBecip())
            <div class="settingsbtn">
                <i id="settings-icon" class="fa-solid fa-gear" data-dossier-id="{{$id}}"></i>
            </div>
        @endif

        <table id="files-table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    @if(auth()->user()->isBecip())
                        <th data-label="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </th>
                    @endif
                    <th>
                        <span class="tooltip">
                            <button type="button" id="sort-index-btn" class="sort-button">
                                <!-- Rev -->
                                <span class="icon-container">
                                    <i class="fa-solid fa-arrow-up-wide-short icon-visible"></i>
                                    <i class="fa-solid fa-arrow-down-short-wide icon-hidden"></i>
                                </span>
                            </button>
                            <span class="tooltiptext">Dernière indice de révision</span>
                        </span>
                    </th>
                    <th>
                        Nom
                    </th>
                    <th>
                        <div class="flexname">
                            <button type="button" id="filter-type-btn" class="type-button">
                                Type
                                <span class="icon-container">
                                        <i class="fa-solid fa-filter icon-visible"></i>
                                        <i class="fa-solid fa-filter-circle-xmark icon-hidden"></i>
                                    </span>
                            </button>
                            <select class="filter-type-select transition-hidden">
                                <option value="all">Tout</option>
                                <option value="undefine">Undefine</option>
                                <option value="coffrage">Coffrage</option>
                                <option value="ferraillage">Ferraillage</option>
                                <option value="divers">Divers</option>
                            </select>
                        </div>
                    </th>
                    <th>Commentaire</th>
                    <th>Déposé par</th>
                    <th>
                        <button type="button" id="sort-date-btn" class="sort-button">
                            Date
                            <span class="icon-container">
                                    <i class="fa-solid fa-arrow-up-wide-short icon-visible"></i>
                                    <i class="fa-solid fa-arrow-down-short-wide icon-hidden"></i>
                                </span>
                        </button>
                    </th>
                    <th>
                        <button type="button" id="sort-valide-btn" class="sort-button">
                            Validé
                            <span class="icon-container">
                                    <i class="fa-solid fa-arrow-up-wide-short icon-visible"></i>
                                    <i class="fa-solid fa-arrow-down-short-wide icon-hidden"></i>
                                </span>
                        </button>
                    </th>
                    <th data-label="Télécharger">
                            <span class="tooltip">
                                <i class="fa-solid fa-download"></i>
                                <span class="tooltiptext">
                                    Télécharger: <br>Séléctionner plusieurs document à télécharger
                                </span>
                            </span>
                    </th>
                    <th data-label="Distribuer">
                            <span class="tooltip">
                                <i class="fa-solid fa-print"></i>
                                <span class="tooltiptext">
                                    Distribuer: <br> Séléctionner plusieurs documents pour faire une demande de distribution
                                </span>
                            </span>
                    </th>
                    <th data-label="Impressions">
                        <span class="tooltip">
                            <i class="fa-solid fa-sheet-plastic"></i>
                            <span class="tooltiptext">
                                Nombre d'impressions: <br> Besoin d'information Florent :)
                            </span>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                    <tr data-id="{{$file->id}}">
                        @if(auth()->user()->isBecip())
                            <td data-label="Delete" class="icon-cell">
                                <i class="fa-solid fa-trash delete-icon"></i>
                            </td>
                        @endif
                        <td data-label="Révision">
                            @if(auth()->user()->role == 'drawer' || auth()->user()->role == 'engineer')
                                <input
                                    type="checkbox"
                                    class="update-last-index"
                                    {{ $file->last_index ? 'checked' : '' }}
                                />
                            @else
                                <input
                                    type="checkbox"
                                    class="update-last-index"
                                    disabled
                                    {{ $file->last_index ? 'checked' : '' }}
                                />
                            @endif
                        </td>
                        <td data-label="Nom">{{$file->name}}</td>
                        <td data-label="Type">
                            @if(auth()->user()->role == 'drawer' || auth()->user()->role == 'engineer')
                                <select class="file-type-select">
                                    <option value="undefine"        {{ $file->type === 'undefine' ? 'selected' : ''}}>      Undefine</option>
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
                                <textarea placeholder="Ajoutez un commentaire..." name="comment[{{$file->id}}]" class="comment-textarea">{{$file->comment}}</textarea>
                            @else
                                {{--ICI IL FAUT LIMITER LE COMMENTAIRE A 10 char--}}
                                {{$file->comment}}
                            @endif
                        </td>
                        <td data-label="Déposé par">
                            {{--Inconnu n'arriveras jamais (we never know)--}}
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
                        <td data-label="Distribuer">
                            <span class="tooltip">
                                <input type="checkbox" name="print_files[]" value="{{$file->id}}"
                                       class="distribution-checkbox"
                                        {{($file->print_count >= 1) ? 'disabled' : '' }}
                                />
                                @if($file->print_count >= 1)
                                    <span class="tooltiptext">Demandez à l'équipe BECIP pour une réimpression.</span>
                                @endif
                            </span>
                        </td>
                        <td data-label="Impressions">
                            {{--Rendre ce chiffre dynamique avec la checkbox distribute--}}
                            {{$file->print_count}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isBecip() ? 12 : 11 }}">Aucun fichier</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </header>
</div>
@endsection
