@extends('layouts.app')

@section('content')
<div class="container-page large">

    {{-- comment mettre en place ce 'success' ? --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{route("home")}}" class="btn-return"><i class="fa-solid fa-arrow-left"></i> Retour</a>


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
        <select name="role" id="role-select" required onchange="updateEntrepSelect()">
            <option value="engineer">Ingénieur</option>
            <option value="drawer">Dessinateur</option>
            <option value="secretary">Secrétaire</option>
            <option value="client">Client</option>
        </select>

        <div id="entrep-container">
            <label>Entreprise</label>
            <input type="text" id="search-box" placeholder="Rechercher une entreprise...">
            <select name="entrep_id" id="entrep-select">
                @foreach($companies as $company)
                    <option value="{{$company->id}}"> {{$company->name}} </option>
                @endforeach
            </select>
        </div>
        <input type="submit" value="Ajouter">
    </form>

    <form action="{{ route('usermanager.addcompany') }}" method="POST">
        @csrf
        <h2>Ajouter une nouvelle entreprise</h2>
        <label>Nom de l'entreprise</label>
        <input type="text" name="name" required>
        <input type="submit" value="Ajouter">
    </form>

    <h2>Modifier les entreprises</h2>
    <table class="table-responsive">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom de l'entreprise</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($companies as $company)
            <tr>
                <td>{{$company->id}}</td>
                <td>{{$company->name}}</td>
                <td>MARCHPA</td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <table class="table-responsive">
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
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->role}}</td>
                <td>
                    @if($user->company == null)
                        Aucune
                    @else
                        {{$user->company}}
                    @endif
                </td>
                <td>MARCHPA</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection
