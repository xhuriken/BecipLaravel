@extends('layouts.app')

@section('content')
    <div class="container-page min-width">
        <div class="section-loader">
            <div class="loader"></div>
            <p class="loading-text">Chargement en cours...</p>
        </div>
        <div class="items-container">
            <div class="group">

                @if (auth()->user()->role == 'engineer')
                    <a href='{{ route('usermanager') }}' class='btn-return'>Gérer les utilisateurs</a>
                @endif
                @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')

                <!-- Bouton qui ouvre le modal d'ajout -->
                    <button id="toggleButton" class="btn-mask">Ajouter une affaire manuellement</button>
                </div>
                <div class="group generate">
                    <a href="{{ route('projects.generate', [500, date('Y')]) }}" class="btn-mask">
                        Générer 500 affaires cette année
                    </a>
                    <a href="{{ route('projects.generate', [500, date('Y')+1]) }}" class="btn-mask">
                        Générer 500 affaires l'année suivante
                    </a>
                </div>
            @endif
        </div>

        <h2>Liste des affaires</h2>
            <table id="project-table" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th data-label="Nom">Numéro</th>
                        <th data-label="NomLong">Nom</th>
                        <th data-label="Entreprise">Entreprise</th>
                        <th data-label="Referent">Référent</th>
                        <th data-orderable="false" data-label="ActionsH">Actions</th>
                        @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                            <th data-label="Delete" data-orderable="false"><i class="fa-solid fa-trash delete-icon"></i></th>
                            <th data-orderable="false" data-label="Check"><input type="checkbox" id="select-all"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
{{--                        Add Project row id for DOM reload in js--}}
                        <tr id="project-row-{{ $project->id }}">
                            <td data-label="Nom">{{ $project->name }}</td>
                            <td data-label="NomLong"
{{--                                Pour trié en dernier si la valeur n'a rien  --}}
                                data-order="{{ $project->namelong ? $project->namelong : 'zzz' }}">
                                {{ $project->namelong ? $project->namelong : 'Pas de nom'}}
                            </td>
                            <td data-label="Entreprise"
{{--                                Pour trié en dernier si la valeur n'a rien  --}}
                                data-order="{{ $project->company_id ? $project->getCompanyName($project->company_id) : 'zzz' }}">
                                {{ $project->company_id ? $project->getCompanyName($project->company_id) : 'Aucune' }}
                            </td>
                            <td data-label="Referent">{{ $project->getReferentName($project->referent_id) }}</td>
                            <td data-label="ActionsH">
                                <a href="{{ route('projects.project', $project) }}" class="btn-return">Voir</a>
                                @if(auth()->user()->isBecip())
                                    <span class="responsiveSpan">|</span>
                                    <a href="#" class="btn-return edit-project"
                                       data-project-id="{{ $project->id }}"
                                       data-project-namelong="{{ $project->namelong }}"
                                       data-project-name="{{ $project->name }}"
                                       data-company-id="{{ $project->company_id }}"
                                       data-referent-id="{{ $project->referent_id }}"
                                       data-address="{{ $project->address }}"
                                       data-comment="{{ $project->comment }}"
                                       data-clients="{{ json_encode($project->clients->pluck('id')->toArray()) }}">
                                        Modifier
                                    </a>
                                @endif
                            </td>
                            @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                                <td class="icon-cell" data-label="Delete">
                                    <a href="javascript:void(0);" class="delete-project-btn" data-delete-url="{{ route('projects.delete', $project) }}" data-project-id="{{ $project->id }}">
                                        <i class="fa-solid fa-trash delete-icon"></i>
                                    </a>
                                </td>
                                <td data-label="Check">
                                    <input type="checkbox" class="delete-checkbox" data-project-id="{{ $project->id }}">
                                </td>
                            @endif
                        </tr>
                    @empty
                    @endforelse
                </tbody>
                <tfoot style="display:none;"></tfoot>
            </table>
        @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
            <div class="button-container">
                <button
                    id="delete-selected"
                    class="btn-filter"
                    data-route="{{ route('projects.delete-selected') }}">
                    Supprimer les affaires sélectionnées
                </button>
                <button
                    id="delete-empty"
                    class="btn-filter"
                    data-route="{{ route('projects.delete-empty') }}">
                    Supprimer les affaires vides
                </button>
            </div>
        @endif
    </div>


    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Ajouter une affaire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="add-project-form">
                        @csrf
                        <div class="mb-3">
                            <label for="add-project-company" class="form-label">Entreprise</label>
                            <select class="form-select" name="company_id" id="add-project-company">
                                <option value="">- Aucune -</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add-project-engineer" class="form-label">Ingénieur référent</label>
                            <select class="form-select" name="engineer_id" id="add-project-engineer">
                                <option value="">- Aucun -</option>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nom de l'affaire</label>
                            <input type="text" class="form-control" id="add-project-namelong" name="project_namelong" placeholder="Nom de l'affaire...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Numéro</label>
                            <div class="d-flex align-items-center">
                                <span class="me-1">B</span>
                                <input type="text" id="add-project-year" maxlength="2" pattern="\d{2}" required placeholder="00" class="form-control me-1" style="max-width: 60px;">
                                <span class="me-1">.</span>
                                <input type="text" id="add-project-number" maxlength="3" pattern="\d{3}" required placeholder="000" class="form-control" style="max-width: 80px;">
                            </div>
                            <!-- Champ caché pour le nom complet -->
                            <input type="hidden" name="project_name" id="add-project-name">
                        </div>
                        <div class="mb-3">
                            <label for="add-project-clients" class="form-label">Clients ayant accès</label>
                            <select id="add-project-clients" name="clients[]" multiple="multiple" class="form-select">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }} - {{ \App\Models\User::getCompanyName($client->company_id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="submit-add-project-btn">Ajouter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Project Modal-->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Modifier l'affaire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-project-form">
                        @csrf
                        <input type="hidden" name="project_id" id="edit-project-id">

                        <div class="mb-3">
                            <label class="form-label">Nom de l'affaire</label>
                            <input type="text" class="form-control" id="edit-project-namelong" name="project_namelong" placeholder="Nom de l'affaire...">
                        </div>

                        @if(auth()->user()->role !== "drawer")
                            <!-- Nom de l'affaire -->
                            <div class="mb-3">
                                <label class="form-label">Numéro</label>
                                <div class="d-flex align-items-center">
                                    <span class="me-1">B</span>
                                    <input type="text" id="edit-project-year" maxlength="2" pattern="\d{2}" required placeholder="00" class="form-control me-1" style="max-width: 60px;">
                                    <span class="me-1">.</span>
                                    <input type="text" id="edit-project-number" maxlength="3" pattern="\d{3}" required placeholder="000" class="form-control" style="max-width: 80px;">
                                </div>
                                <input type="hidden" name="project_name" id="edit-project-name">
                            </div>
                        @else
                            <div class="d-none">
                                <label class="form-label">Numéro</label>
                                <div class="d-flex align-items-center">
                                    <span class="me-1">B</span>
                                    <input type="text" id="edit-project-year" maxlength="2" pattern="\d{2}" required placeholder="00" class="form-control me-1" style="max-width: 60px;">
                                    <span class="me-1">.</span>
                                    <input type="text" id="edit-project-number" maxlength="3" pattern="\d{3}" required placeholder="000" class="form-control" style="max-width: 80px;">
                                </div>
                                <input type="hidden" name="project_name" id="edit-project-name">
                            </div>
                        @endif
                        <!-- Entreprise -->
                        <div class="mb-3">
                            <label for="edit-project-company" class="form-label">Entreprise</label>
                            <select class="form-select" name="company_id" id="edit-project-company">
                                <option value="">- Aucune -</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Référent -->
                        <div class="mb-3">
                            <label for="edit-project-referent" class="form-label">Référent</label>
                            <select class="form-select" name="referent_id" id="edit-project-referent">
                                <option value="">- Aucun -</option>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(auth()->user()->role !== "drawer")
                            <!-- Adresse -->
                            <div class="mb-3">
                                <label for="edit-project-address" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="edit-project-address" name="address" placeholder="Adresse (facultatif)">
                            </div>

                            <!-- Commentaire -->
                            <div class="mb-3">
                                <label for="edit-project-comment" class="form-label">Commentaire</label>
                                <textarea class="form-control" id="edit-project-comment" name="comment" rows="3" placeholder="Commentaire (facultatif)"></textarea>
                            </div>
                        @else
                            <!-- Adresse -->
                            <div class="d-none">
                                <label for="edit-project-address" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="edit-project-address" name="address" placeholder="Adresse (facultatif)">
                            </div>

                            <!-- Commentaire -->
                            <div class="d-none">
                                <label for="edit-project-comment" class="form-label">Commentaire</label>
                                <textarea class="form-control" id="edit-project-comment" name="comment" rows="3" placeholder="Commentaire (facultatif)"></textarea>
                            </div>
                        @endif
                        <!-- Clients ayant accès -->
                        <div class="mb-3">
                            <label for="edit-project-clients" class="form-label">Clients ayant accès</label>
                            <select id="edit-project-clients" name="clients[]" multiple="multiple" class="form-select">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }} - {{ \App\Models\User::getCompanyName($client->company_id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="save-project-btn">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.storeProjectUrl = '{{ route("projects.store") }}';
        window.updateProjectUrl = '{{ route("projects.update") }}';
    </script>
@endsection
