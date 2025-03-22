@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-center mt-4">

        <div class="card p-4 shadow-sm text-center" style="max-width: 450px; width: 100%; min-width: 30rem">
            <div class="section-loader">
                <div class="loader"></div>
                <p class="loading-text">Chargement en cours...</p>
            </div>
            <div class="d-flex justify-content-start mb-2">
                <a href="{{route("home")}}" class="btn-return"><i class="fa-solid fa-arrow-left"></i> Retour</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('status'))
                <div class="alert alert-success" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row align-items-center mb-3">
                <div class="col-auto">
                    <button id="name-edit-btn" class="btn btn-primary btn-square">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </div>
                <div class="col text-start">
                    <label class="fw-bold">Votre nom :</label>
                    <span id="name-display">{{ auth()->user()->name }}</span>
                    <input type="text" id="name-input" class="form-control d-none mt-2" value="{{ auth()->user()->name }}">
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-auto">
                    <button id="email-edit-btn" class="btn btn-primary btn-square">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </div>
                <div class="col text-start">
                    <label class="fw-bold">Votre mail :</label>
                    <span id="email-display">{{ auth()->user()->email }}</span>
                    <input type="email" id="email-input" class="form-control d-none mt-2" value="{{ auth()->user()->email }}">
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-auto">
                    <button id="phone-edit-btn" class="btn btn-primary btn-square">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </div>
                <div class="col text-start">
                    <label class="fw-bold">Votre téléphone :</label>
                    <span id="phone-display">{{ auth()->user()->phone ? auth()->user()->getPhone(): 'Aucun' }}</span>
                    <input type="text" id="phone-input" class="form-control d-none mt-2" value="{{ auth()->user()->phone }}">
                </div>
            </div>

            <div class="d-flex justify-content-center mb-3">
                <button id="open-change-password" class="btn btn-success w-100">Changer le mot de passe</button>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('logout') }}" class="text-danger fw-bold"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Déconnexion
                </a>
                <p class="mb-0">Entreprise : <span class="fw-bold">{{ auth()->user()->getCompanyName(auth()->user()->company_id) }}</span></p>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>

    <div id="changePasswordModal" class="modal fade">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Changer le mot de passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    @include('auth.passwords.email_content')
                </div>
            </div>
        </div>
    </div>
    <script>
        window.updateProfileUrl = '{{ route("profile.update") }}';
    </script>
@endsection
