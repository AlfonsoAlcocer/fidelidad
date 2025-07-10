@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <!-- Cambié type a button para evitar envío automático -->
                                <button id="enviarBTN" type="button" class="btn btn-primary">
                                    Iniciar sesión
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="speechModal" tabindex="-1" aria-labelledby="speechModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Verificación por voz') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <p>{{ __('Por favor, pronuncia la siguiente palabra:') }}</p>
        <h3 id="wordToSay" class="mb-4" style="font-weight: bold;"></h3>
        <button id="startRecognition" class="btn btn-success">{{ __('Hablar') }}</button>
        <p id="speechResult" class="mt-3"></p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const words = ['manzana', 'perro', 'cielo', 'montaña', 'río']; 
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('enviarBTN'); // corregido aquí
    const speechModal = new bootstrap.Modal(document.getElementById('speechModal'));
    const wordToSayElem = document.getElementById('wordToSay');
    const startRecognitionBtn = document.getElementById('startRecognition');
    const speechResultElem = document.getElementById('speechResult');

    let currentWord = '';

    // Al hacer click en el botón Iniciar sesión, mostramos el modal y elegimos palabra
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        currentWord = words[Math.floor(Math.random() * words.length)];
        wordToSayElem.textContent = currentWord;
        speechResultElem.textContent = '';
        speechModal.show();
    });

    // Configurar SpeechRecognition
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        startRecognitionBtn.disabled = true;
        speechResultElem.textContent = 'Tu navegador no soporta reconocimiento por voz.';
        return;
    }
    const recognition = new SpeechRecognition();
    recognition.lang = 'es-ES';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    startRecognitionBtn.addEventListener('click', () => {
        speechResultElem.textContent = 'Escuchando...';
        recognition.start();
    });

    recognition.addEventListener('result', (event) => {
        const spokenWord = event.results[0][0].transcript.toLowerCase().trim();
        speechResultElem.textContent = `Dijiste: "${spokenWord}"`;

        if (spokenWord === currentWord.toLowerCase()) {
            speechResultElem.textContent += ' ✅ Palabra correcta. Enviando formulario...';
            setTimeout(() => {
                speechModal.hide();
                loginForm.submit(); // enviar formulario
            }, 1000);
        } else {
            speechResultElem.textContent += ' ❌ Palabra incorrecta. Intenta de nuevo.';
            setTimeout(() => {
                speechModal.hide();
            }, 2000);
        }
    });

    recognition.addEventListener('error', (event) => {
        speechResultElem.textContent = 'Error en el reconocimiento: ' + event.error;
    });
});
</script>
@endsection
