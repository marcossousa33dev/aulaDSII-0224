<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_log extends CI_Model {

	public function inserir_log($usuario, $comando){
		//Instacia do banco de Log
		$dblog = $this->load->database('log', TRUE);

		//Chamada da função na Helper para nos auxiliar
		$comando = troca_caractere($comando);

		//Query de inserção dos dados
		$dblog->query("insert into log(usuario, comando)
							values ('$usuario', '$comando')");

		//Verificar se a inserção ocorreu com sucesso
		if($dblog->affected_rows() > 0){
			$dados = array('codigo' => 1,
							'msg' => 'Log cadastrado corretamente');
			
		}else{
			$dados = array('codigo' => 6,
							'msg' => 'Houve algum problema na inserção do log');
		}
		
		//Fecho a conexão com o banco de log
		$dblog->close();
		
		//Retorno o array $dados com as informações tratadas
		return $dados;
	}
}

