@extends('layouts.app')

@section('content')
    <div class="container-page large">
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

        <div class="flex">
            {{-- Formulaire d'ajout d'entreprise --}}
            <form action="{{ route('usermanager.addcompany') }}" method="POST" class="form-becip">
                @csrf
                <div class="mb-2">
                    <h2>Ajouter une entreprise</h2>
                </div>
                <div class="mb-2">
                    <label class="form-label">Nom de l'entreprise</label>
                    <input class="form-control" type="text" name="name" placeholder="SuperEntreprise" required>
                </div>
                <div class="mb-2">
                    <input type="submit" value="Ajouter" class="large-btn">
                </div>
            </form>

            {{-- Formulaire d'ajout d'utilisateur --}}
            <form action="" method="POST" class="form-becip" id="adduser">
                @csrf
                <h2 class="mb-2">Ajouter un utilisateur</h2>

                <div class="mb-2">
                    <label for="name" class="form-label">NOM Prénom</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="NOM Prénom...">
                </div>

                <div class="mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email valide...">
                </div>

                <div class="mb-2">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Numéro de téléphone...">
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label">
                      Mot de passe
                    </label>
                    <input type="password" class="form-control" id="password" name="password"
                           value="{{ $newPasswordGenerated }}" placeholder="SuperMot2Passe!">
                </div>

                <div class="mb-2">
                    <label for="role" class="form-label">Rôle</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="engineer">Ingénieur</option>
                        <option value="drawer">Dessinateur</option>
                        <option value="secretary">Secrétaire</option>
                        <option value="client">Client</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label for="company_id" class="form-label">Entreprise</label>
                    <select name="company_id" id="company_id" class="form-select">
                        <option value="">Aucune</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="submit" value="Ajouter" class="large-btn">
            </form>
        </div>

        <h2>Modifier les entreprises</h2>
        <table id="companies-table" class="table-responsive table table-striped">
            <thead>
            <tr>
{{--                <th>ID</th>--}}
                <th>Nom de l'entreprise</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($companies as $company)
                <tr data-company-id="{{ $company->id }}">
{{--                    <td>{{ $company->id }}</td>--}}
                    <td class="company-name">{{ $company->name }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-company" data-route="{{ route('usermanager.updatecompany') }}">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-company" data-route="{{ route('usermanager.deletecompany') }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <h2>Modifier les utilisateurs</h2>
        <table id="users-table" class="table-responsive table table-striped">
            <thead>
            <tr>
{{--                <th>ID</th>--}}
                <th>Nom et Prénom</th>
                <th data-label="phone">Téléphone</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Entreprise</th>
                <th data-label="Action">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr data-user-id="{{ $user->id }}">
{{--                    <td>{{ $user->id }}</td>--}}
                    <td class="user-name">{{ $user->name }}</td>
                    <td data-label="phone" class="user-phone">{{ $user->phone ? $user->getPhone(): 'Aucun' }}</td>
                    <td class="user-email">{{ $user->email }}</td>
                    <td class="user-role" data-role="{{ $user->role }}">
                        {{ [
                            'engineer' => 'Ingénieur',
                            'drawer' => 'Dessinateur',
                            'secretary' => 'Secrétaire',
                            'client' => 'Client'
                        ][$user->role] ?? $user->role }}
                    </td>
                    <td class="user-company">
                        @if($user->company_id)
                            {{ $user->getCompanyName($user->company_id) }}
                        @else
                            Aucune
                        @endif
                    </td>
                    <td  data-label="Action">
                        <button class="btn btn-sm btn-primary edit-user" data-route="{{ route('usermanager.updateuser') }}">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-user" data-route="{{ route('usermanager.deleteuser') }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script>
        window.addUserRoute = "{{route('usermanager.adduser')}}";
        window.allCompanies = @json($companies);
        window.allRoles = {
            'engineer': 'Ingénieur',
            'drawer': 'Dessinateur',
            'secretary': 'Secrétaire',
            'client': 'Client'
        };
    </script>
@endsection
