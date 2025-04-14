@extends('adminlte::page')

@section('title', 'Redefinir Senha')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Redefinição de senha</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-baseline" style="height: 60px;">
                <h2 class="col-sm" style="padding-top: 10px;"> Usuário - {{ Auth::user()->use_name }}</h2>
            </div>
        </div>
        <div class="p-3">
            <div class="col-sm-5 mt-4 mb-4 ">
                <form action="{{ route('user.redefinirSenha') }}" method="POST" id="formRedefineSenha">
                    @csrf
                    @method('PUT')
                    <h4 class="mb-2"> Altere a sua senha para continuar</h4>
                    <div class="mt-2 mb-2 second">
                        <label for="new_password">Nova senha (a senha deve conter pelo menos 8 caracteres, incluíndo letras
                            e números): </label>
                        <input id="new_password" name="new_password" type="password" placeholder="***********"
                            class="form-control @error('new_password') is-invalid @enderror" :value="old('new_password')">
                        @error('new_password')
                            <h5 id="erros" style="color: #dc3545" role="alert">
                                {{ $message }}
                            </h5>
                        @enderror
                    </div>
                    <div class="mt-2 mb-2 third">
                        <label for="confirm_password">Confirmar nova senha: </label>
                        <input id="confirm_password" name="confirm_password" type="password" placeholder="***********"
                            class="form-control @error('confirm_password') is-invalid @enderror">
                        @error('confirm_password')
                            <h5 id="erros" style="color: #dc3545" role="alert">
                                {{ $message }}
                            </h5>
                        @enderror
                    </div>
                    <button class='mt-3 btn btn-primary fourth' id="buttonResetPassword" type="submit">Alterar</button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function showError(input, message) {
            let inputId = input.attr('id');
            let errorClass = inputId + 'Invalid';

            input.addClass('is-invalid');
            input.after(`<span class="invalid-feedback ${errorClass}" role="alert"><strong>` + message +
                '</strong></span>');
            // $('#buttonResetPassword').prop('disabled', true)
        }

        $('#buttonResetPassword').prop('disabled', true)

        $('#new_password, #confirm_password').on('change', function() {
            var newPassword = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();

            $('#new_password, #confirm_password').removeClass('is-invalid');
            $('.new_passwordInvalid, .confirm_passwordInvalid')
                .remove();

            if (newPassword.length < 8) {
                showError($('#new_password'),
                    'A senha deve conter pelo menos 8 caracteres, incluíndo letras e números.');
            } else if (!/\d/.test(newPassword)) {
                showError($('#new_password'), 'A senha deve conter pelo menos 1 número.');
            } else if (!/[a-zA-Z]/.test(newPassword)) {
                showError($('#new_password'), 'A senha deve conter pelo menos 1 letra.');
            } else if (newPassword !== confirmPassword) {
                showError($('#confirm_password'), 'A senha e a confirmação de senha não correspondem.');
            } else {
                $('#buttonResetPassword').prop('disabled', false)
            }
        });
    </script>
@endsection
