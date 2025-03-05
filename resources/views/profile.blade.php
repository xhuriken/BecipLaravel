@extends('layouts.app')

@section('content')
    <div class="container-profile">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <a href="{{ route('home') }}" class="btn-return">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>

        <div class="field-container" id="name-container">
            <button id="name-edit-btn">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <p class="profileText">Votre nom :</p>
            <span id="name-display">{{ auth()->user()->name }}</span>
            <input type="text" id="name-input" value="{{ auth()->user()->name }}" style="display: none;">
        </div>

        <div class="field-container" id="email-container">
            <button id="email-edit-btn">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <p class="profileText">Votre mail :</p>
            <span id="email-display">{{ auth()->user()->email }}</span>
            <input type="text" id="email-input" value="{{ auth()->user()->email }}" style="display: none;">
        </div>

        <div class="field-container" id="password-change-container">
            <button id="open-change-password" class="btn-filter">Changer le mot de passe</button>
        </div>

        <div class="button logout">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>
            <p>Entreprise : <span class='name'>{{ auth()->user()->getCompanyName(auth()->user()->company_id) }}</span></p>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
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
