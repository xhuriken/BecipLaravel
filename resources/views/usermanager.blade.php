@extends('layouts.app')

@section('content')
    <div class="container-page large">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{route("home")}}" class="btn-return"><i class="fa-solid fa-arrow-left"></i> Retour</a>

        {{-- Formulaire d'ajout d'utilisateur --}}
        <form action="{{ route('usermanager.adduser') }}" method="POST">
            @csrf
            <h2>Ajouter un utilisateur</h2>

            <label>Nom et Prénom</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>
            <span class="tooltip">Mot de passe ⓘ
                <span class="tooltiptext">Ce mot de passe a été généré automatiquement. Vous pouvez le modifier si vous le souhaitez.</span>
            </span>
            </label>
            <input type="password" name="password" value="{{ $newPasswordGenerated }}" required>

            <label>Rôle</label>
            <select name="role" required>
                <option value="engineer">Ingénieur</option>
                <option value="drawer">Dessinateur</option>
                <option value="secretary">Secrétaire</option>
                <option value="client">Client</option>
            </select>

            <label>Entreprise</label>
            <select name="company_id">
                <option value="">Aucune</option>
                @foreach($companies as $company)
                    <option value="{{$company->id}}">{{$company->name}}</option>
                @endforeach
            </select>

            <input type="submit" value="Ajouter">
        </form>

        {{-- Formulaire d'ajout d'entreprise --}}
        <form action="{{ route('usermanager.addcompany') }}" method="POST">
            @csrf
            <h2>Ajouter une nouvelle entreprise</h2>
            <label>Nom de l'entreprise</label>
            <input type="text" name="name" required>
            <input type="submit" value="Ajouter">
        </form>

        <h2>Modifier les entreprises</h2>
        <table id="companies-table" class="table-responsive table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom de l'entreprise</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($companies as $company)
                <tr data-company-id="{{ $company->id }}">
                    <td>{{ $company->id }}</td>
                    <td class="company-name">{{ $company->name }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-company" data-route="{{ route('usermanager.updatecompany') }}"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-sm btn-danger delete-company" data-route="{{ route('usermanager.deletecompany') }}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <h2>Modifier les utilisateurs</h2>
        <table id="users-table" class="table-responsive table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom et Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Entreprise</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr data-user-id="{{ $user->id }}">
                    <td>{{ $user->id }}</td>
                    <td class="user-name">{{ $user->name }}</td>
                    <td class="user-email">{{ $user->email }}</td>
                    <td class="user-role">{{ $user->role }}</td>
                    <td class="user-company">
                        @if($user->company_id)
                            {{ $user->getCompanyName($user->company_id) }}
                        @else
                            Aucune
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-user" data-route="{{ route('usermanager.updateuser') }}"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-sm btn-danger delete-user" data-route="{{ route('usermanager.deleteuser') }}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        window.allCompanies = @json($companies);
        window.allRoles = ['engineer', 'drawer', 'secretary', 'client'];
    </script>
@endsection
