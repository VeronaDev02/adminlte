@extends('adminlte::page')

@section('title', 'Meu Perfil')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" style="font-weight: normal;">Perfil</li>
            </ol>
        </div>
    </div>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="row" style="height: 60px;">
                <h3 class="col-sm" style="padding-top: 10px;"><strong> Perfil - {{ Auth::user()->use_name }} </strong></h3>
            </div>
        </div>
        <div class="row">
            <div class="m-4 col-sm-2 d-flex flex-column align-items-center">
                <div style="background-color: black; height:200px; width:200px; border-radius:50%"
                    class="d-flex justify-content-center">
                    <img style="width:200px; height:200px; border-radius:50%"
                        src="{{ Auth::user()->img_user ? Auth::user()->img_user : asset('images/profile.png') }}"
                        alt="img-usuario">
                </div>
                <div class="mt-2"><a href="#" data-toggle="modal" data-target="#exampleModal" title="Alterar Imagem" button=""
                        type="submit">Alterar imagem</a></div>
            </div>
            <div class="col-sm-4 ml-5 mt-4">
                <h5 class="mb-2"><strong>Informações</strong></h5>
                <div> <strong> Nome: </strong>{{ Auth::user()->use_name }} </div>
                <div>
                    <strong> Usuário: </strong>{{ Auth::user()->use_username }}
                </div>
                <div>
                    <strong> Email: </strong>{{ Auth::user()->use_email }}
                </div>
                <div>
                    <strong> Telefone: </strong>{{ Auth::user()->use_cell }}
                </div>
                <div>
                    <strong> Criado em: </strong>{{ date_format(date_create(Auth::user()->created_at), 'd/m/20y') }}
                </div>
            </div>
            <div class="col-sm-4 mt-4 mb-4 ">
                <form action="{{ route('user.redefinirSenhaPerfil') }}" method="POST" id="formRedefineSenha">
                    @method('PUT')
                    @csrf
                    <h5 class="mb-2"><strong>Alterar a senha</strong></h5>
                    <div class="mt-2 mb-2">
                        <label for="old_password">Senha atual: </label>
                        <input class="form-control @if ($message = session('error')) is-invalid @endif" id="old_password"
                            name="old_password" type="password" placeholder="********">
                        @error('old_password')
                            <h5 id="error" style="color: #dc3545" role="alert">
                                {{ $message }}
                            </h5>
                        @enderror
                    </div>
                    <div class="mt-2 mb-2">
                        <label for="new_password">Nova senha (a senha deve conter pelo menos 8 caracteres): </label>
                        <input class="form-control" id="new_password" name="new_password" type="password"
                            placeholder="********">
                        @error('new_password')
                            <h5 id="error" style="color: #dc3545" role="alert">
                                {{ $message }}
                            </h5>
                        @enderror
                    </div>
                    <div class="mt-2 mb-2">
                        <label for="confirm_password">Confirmar nova senha: </label>
                        <input class="form-control" id="confirm_password" name="confirm_password" type="password"
                            placeholder="********">
                        @error('confirm_password')
                            <h5 id="error" style="color: #dc3545" role="alert">
                                {{ $message }}
                            </h5>
                        @enderror
                    </div>
                    <button onclick="enviaFormulario()" class='mt-3 btn btn-primary' type="button">Alterar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Alterar imagem</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('addImgUser') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="container">
                            <h1 class="container-demo__title">Nova imagem de usuário</h1>
                            <div class="container-demo">
                                <div class="col">
                                    <h3>Adicione a imagem aqui</h3>
                                    <input accept="image/*" type="file" id="image">
                                </div>
                                <div class="col">
                                    <h3>Recortar</h3>
                                    <div id="editor"></div>
                                </div>
                                <div class="col">
                                    <h3>Preview</h3>
                                    <canvas style="border-radius: 50%" id="preview"></canvas>
                                </div>
                                <input type="hidden" id="base64" name="base64">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" id="btn_atualiza_img" class='btn bg-success'>Finalizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <input type="hidden" id="md" data-toggle="modal" data-target="#exampleModal" />

@stop

@section('css')
    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="/css/crop.css">
    <link rel="stylesheet" href="/css/adduserimg.css">
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="/js/cropp.min.js"></script>
    <script>
        var MAX_WIDTH = 200;
        var MAX_HEIGHT = 200;

        function showError(input, message) {
            let inputId = input.attr('id');
            let errorClass = inputId + 'Invalid';

            input.addClass('is-invalid');
            input.after(`<span class="invalid-feedback ${errorClass}" role="alert"><strong>` + message +
                '</strong></span>');
        }

        function verifyError(condition, input) {
            if (condition) {
                if (!input.classList.contains("is-invalid")) input.classList.toggle('is-invalid');
            } else {
                if (input.classList.contains("is-invalid")) input.classList.toggle('is-invalid');
            }
        }

        function alert(msg) {
            let error = document.getElementById('error');
            if (error.classList.contains("desaparece")) error.classList.toggle('desaparece');
            error.innerText = msg;
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');

            $('.invalid-feedback').remove();
        }

        function enviaFormulario() {
            clearErrors();

            const newPassword = document.getElementById('new_password');
            const oldPassword = document.getElementById('old_password');
            const confirmPassword = document.getElementById('confirm_password');

            let errors = [];

            if (newPassword.value.length < 8) {
                errors.push({
                    input: newPassword,
                    message: 'A nova senha deve conter pelo menos 8 caracteres.'
                });
            }
            if (oldPassword.value.length < 8) {
                errors.push({
                    input: oldPassword,
                    message: 'A senha antiga deve conter pelo menos 8 caracteres.'
                });
            }
            if (confirmPassword.value.length < 8) {
                errors.push({
                    input: confirmPassword,
                    message: 'A confirmação da senha deve conter pelo menos 8 caracteres.'
                });
            }

            if (/\s/.test(newPassword.value)) {
                errors.push({
                    input: newPassword,
                    message: 'A nova senha não deve conter espaços em branco.'
                });
            }

            if (newPassword.value === oldPassword.value) {
                errors.push({
                    input: newPassword,
                    message: 'A nova senha deve ser diferente da antiga.'
                });
                errors.push({
                    input: oldPassword,
                    message: 'A nova senha deve ser diferente da antiga.'
                });
            }

            if (newPassword.value !== confirmPassword.value) {
                errors.push({
                    input: newPassword,
                    message: 'A senha e a confirmação devem ser iguais.'
                });
                errors.push({
                    input: confirmPassword,
                    message: 'A senha e a confirmação devem ser iguais.'
                });
            }

            if (errors.length > 0) {
                errors.forEach(error => showError($(error.input), error.message));
                return;
            }

            document.getElementById('formRedefineSenha').submit();
        }


        const createFileFromBlob = blob => {
            const imageResized = new File([blob], image.name, {
                type: 'image/webp',
                lastModified: Date.now()
            });
            getBase64(imageResized)
        };

        function getBase64(file) {
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                document.querySelector('#base64').value = reader.result;
                document.querySelector('#btn_atualiza_img').removeAttribute('disabled', false);
            };
            reader.onerror = function(error) {
                console.log('Error: ', error);
            };
        }

        function compacta(base64) {
            const img = new Image();
            img.src = base64;

            img.onload = () => {
                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                context.drawImage(img, 0, 0);

                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext("2d");

                ctx.drawImage(img, 0, 0, width, height);
                ctx.canvas.toBlob(createFileFromBlob, image.type, 0.1);
            };
        }
        document.addEventListener('DOMContentLoaded', () => {
            const inputImage = document.querySelector('#image');
            const editor = document.querySelector('#editor');
            const miCanvas = document.querySelector('#preview');
            const contexto = miCanvas.getContext('2d');
            let urlImage = undefined;
            inputImage.addEventListener('change', abrirEditor, false);

            function abrirEditor(e) {
                document.querySelector('#btn_atualiza_img').setAttribute('disabled', true);
                urlImage = URL.createObjectURL(e.target.files[0]);

                editor.innerHTML = '';
                let cropprImg = document.createElement('img');
                cropprImg.setAttribute('id', 'croppr');
                editor.appendChild(cropprImg);

                contexto.clearRect(0, 0, miCanvas.width, miCanvas.height);

                document.querySelector('#croppr').setAttribute('src', urlImage);

                new Croppr('#croppr', {
                    aspectRatio: 1,
                    startSize: [70, 70],
                    onCropEnd: recortarImagen
                })
            }

            function recortarImagen(data) {
                const inicioX = data.x;
                const inicioY = data.y;
                const nuevoAncho = data.width;
                const nuevaAltura = data.height;
                const zoom = 1;
                let imagenEn64 = '';
                miCanvas.width = nuevoAncho;
                miCanvas.height = nuevaAltura;
                let miNuevaImagenTemp = new Image();
                miNuevaImagenTemp.onload = function() {
                    contexto.drawImage(miNuevaImagenTemp, inicioX, inicioY, nuevoAncho * zoom, nuevaAltura *
                        zoom, 0, 0, nuevoAncho, nuevaAltura);
                    imagenEn64 = miCanvas.toDataURL("image/jpeg");
                    compacta(imagenEn64);
                }
                miNuevaImagenTemp.src = urlImage;
            }
        });
    </script>
@stop
