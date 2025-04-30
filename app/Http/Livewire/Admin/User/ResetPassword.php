<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use App\Events\Admin\User\Edit;

class ResetPassword extends Component
{
    public $user;
    public $newPassword;

    public function gerarSenha(
        $tamanho,
        $maiusculas,
        $minusculas,
        $numeros,
        $simbolos
    ) {
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
        $mi = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
        $nu = "0123456789"; // $nu contem os números
        $si = "!@#$%&*"; // $si contem os símbolos
        $senha = "";
        if ($maiusculas) {
            // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($ma);
        }
        if ($minusculas) {
            // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($mi);
        }
        if ($numeros) {
            // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($nu);
        }
        if ($simbolos) {
            // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($si);
        }
        // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável $tamanho
        return substr(str_shuffle($senha), 0, $tamanho);
    }

    public function resetPassword()
    {
        $this->newPassword = "senha123";
        
        $this->user->update([
            "use_password" => "senha123", // O hash é aplicado pelo mutator no model
            "use_status_password" => false,
        ]);
        
        event(new Edit($this->user->use_id, request()->ip()));
    }

    public function render()
    {
        return view("livewire.admin.user.reset-password");
    }
}