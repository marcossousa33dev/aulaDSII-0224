<?php
defined('BASEPATH') or exit('No direct script access allowed');
    /*
    Função para verificar os parâmetros vindos do FrontEnd
    */
    function verificarParam($atributos, $lista) {
        //1º   -Verificar se os elementos do Front estão nos atributos necessários
        foreach($lista as $key => $value) {
            if(array_key_exists($key, get_object_vars($atributos))){
                $estatus = 1;
            }else {
                $estatus = 0;
                break;
            }
        }

        // 2º   -Verificando a quantidade de elementos
        if(count(get_object_vars($atributos)) != count($lista)) {
            $estatus = 0;
        }

        return $estatus;
    }

    //Função para trocar caracteres ' (aspas simples) por ` (acento agudo)
    //para podermos montar uma String
    function troca_caractere($value)
    {
        $retorno = str_replace("'", "`", $value);
        return $retorno;
    }

?>

