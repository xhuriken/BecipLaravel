@extends('layouts.app')

@section('content')
<div class="container-page min-width">
    @if (auth()->user()->role == 'engineer')
        <a href='{{ route('usermanager') }}' class='btn-return'>Gérer les utilisateurs</a>
    @endif
    @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
        <p></p>

        -
        <a href="{{ route('projects.generate', [500, date('Y')]) }}" class="btn-mask">Générer 500 affaires cette année</a>
        <p></p>

        -
        <a href="{{ route('projects.generate', [500, date('Y')+1]) }}" class="btn-mask">Générer 500 affaire l'année suivante</a>
        <p></p>
        <p></p>

        - <button id="toggleButton" class="btn-mask">Ajouter une affaire manuellement</button>

        <form action="" method="POST" id="masquableF">
            <label>Entreprise</label>
            <select class="custom-select" name="company_id">
                @foreach($companies as $company)
                    <option value="{{ $company->name}}">
                        {{$company->name}}
                    </option>
                @endforeach
            </select>

            <label>Nom de l'affaire</label>
            <div class="nom-affaire-container">
                <span class="fixed-prefix">B</span>
                <input type="text" id="affaire-annee" maxlength="2" pattern="\d{2}" required placeholder="00">
                <span class="fixed-dot">.</span>
                <input type="text" id="affaire-numero" maxlength="3" pattern="\d{3}" required placeholder="000">
                <input type="hidden" name="nom_dossier" id="nom_dossier">
            </div>

            <label class="topspace">Clients ayant accès</label>
            <input type="text" id="search-clients" placeholder="Rechercher un client..." class="researchbox">
            <div class="custom-multi-select">
                <div class="select-box" onclick="toggleOptions()">Sélectionner des clients</div>
                <div class="options-container">
                    @foreach($clients as $client)
                        <label>
                            <input type="checkbox" name="clients[]" value="{{$client->name}}" data-value="{{$client->name}}">
                                {{$client->name}} - {{$client->company}}
                        </label>
                    @endforeach
                </div>
            </div>

            <input type="submit" value="Ajouter" name="add-dossier">
        </form>
    @endif
    <h2>Liste des affaires</h2>

    <table id="project-table" class="table table-striped" style="width:100%">
        <thead>
        <tr>
            <th>Entreprise</th>
            <th>Nom de l'affaire</th>
            <th>Actions</th>
            @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                <th data-label="Delete">
                    <i class="fa-solid fa-trash"></i>
                </th>
                <th>
                    <input type="checkbox" id="select-all">
                </th>
            @endif
        </tr>
        </thead>
        <tbody>
        @forelse($projects as $project)
            <tr>
                <td>
                    @if($project->company == null)
                        Aucune
                    @else
                        {{ $project->company }}
                    @endif
                </td>
                <td>{{ $project->name }}</td>
                <td>
                    <a href="{{route('projects.project', $project)}}" class="btn-return">Voir</a>
                    @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                        <span class="responsiveSpan">|</span>
                        <a href="" class="btn-return">Modifier</a>
                    @endif
                </td>

                @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                    <td class="icon-cell">
                        <i class="fa-solid fa-trash delete-icon" data-project-id="{{ $project->id }}"></i>
                    </td>
                    <td>
                        <input type="checkbox" class="delete-checkbox" data-project-id="{{ $project->id }}">
                    </td>
                @endif
            </tr>
        @empty
            <tr><td colspan="5">Aucune affaire trouvée</td></tr>
        @endforelse
        </tbody>
    </table>

    @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
        <button id="delete-selected" class="btn-filter">Supprimer les affaires sélectionnées</button>
    @endif
</div>
@endsection
