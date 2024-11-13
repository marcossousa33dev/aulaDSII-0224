<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_unidmedida extends CI_Model {

	public function inserir($sigla, $descricao, $usuario){
		try{
			//Atributo $retorno recebe array com informações (Correção Desafio)
			$ret_usuario = $this->verificaUsuario($usuario);

			//Validação se o usuário está com estatus valido para
			//inserir o registro
			if ($ret_usuario['codigo'] == 8 || $ret_usuario['codigo'] == 9 ){
				//retorno para controller usuário com problema
				$dados = $ret_usuario;
			}else{
				//Verificar se a unidade de medida já não está cadastrada
				$ret_unidMedida = $this->verificaUM($sigla);

				if ($ret_unidMedida['codigo'] == 2){

					//Query de inserção dos dados
					$sql = "insert into unid_medida (sigla, descricao, usucria)
					values ('$sigla', '$descricao', '$usuario')";

					$this->db->query($sql);

					//Verificar se a inserção ocorreu com sucesso
					if($this->db->affected_rows() > 0){
						//Fazemos a inserção no Log na nuvem
						//Fazemos a instância da model M_log
						$this->load->model('m_log');

						//Fazemos a chamada do método de inserção do Log
						$retorno_log = $this->m_log->inserir_log($usuario, $sql);

						if ($retorno_log['codigo'] == 1){
							$dados = array('codigo' => 1,
										'msg' => 'Unidade de medida cadastrada corretamente');
						}else{
							$dados = array('codigo' => 7,
										'msg' => 'Houve algum problema no salvamento do Log, porém, 
													Unidade de Medida cadastrada corretamente');
						}
						
					}else{
						$dados = array('codigo' => 6,
									'msg' => 'Houve algum problema na inserção na tabela de unidade de medida');
					}
				}else{
					$dados = $ret_unidMedida;
				}					
			}
		}catch (Exception $e) {
			$dados = array('codigo' => 00,
							'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',  
									$e->getMessage(), "\n");
		}
		//Envia o array $dados com as informações tratadas
		//acima pela estrutura de decisão if
		return $dados;
	}

	public function consultar($codigo, $sigla, $descricao){
		//--------------------------------------------------
		//Função que servirá para quatro tipos de consulta:
		//  * Para todos as unidades de medida;
		//  * Para uma determinada sigla de unidade;
		//  * Para um código de unidade de medida;
		//  * Para descrição da unidade de medida;
		//--------------------------------------------------
		try{
			//Query para consultar dados de acordo com parâmetros passados		
			$sql = "select * from unid_medida where estatus = '' ";

			if($codigo != '' && $codigo != 0) {
				$sql = $sql . "and cod_unidade = '$codigo' ";
			}

			if($sigla != ''){
				$sql = $sql . "and sigla = '$sigla' ";
			}
			
			if($descricao != ''){
				$sql = $sql . "and descricao like '%$descricao%' ";
			}

			$retorno = $this->db->query($sql);

			//Verificar se a consulta ocorreu com sucesso
			if($retorno->num_rows() > 0){
				$dados = array('codigo' => 1,
							'msg' => 'Consulta efetuada com sucesso',
							'dados' => $retorno->result());			
			}else{
				$dados = array('codigo' => 6,
							'msg' => 'Dados não encontrados');
			}
		}catch (Exception $e) {
			$dados = array('codigo' => 00,
							'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',  
									$e->getMessage(), "\n");
		}
		//Envia o array $dados com as informações tratadas
		//acima pela estrutura de decisão if
		return $dados;
	}

	public function alterar($codigo, $sigla, $descricao, $usuario){
		try{
			if (trim($sigla) != ''){
				//Verificar se a unidade de medida já não está cadastrada
				$ret_unidMedida = $this->verificaUM($sigla);

				if ($ret_unidMedida['codigo'] == 2){
					$dados = $ret_unidMedida;	
					return $dados;
				}
			}

			//Query de atualização dos dados
			if (trim($sigla) != '' && trim($descricao) != '') {
				$sql = "update unid_medida set sigla = '$sigla', descricao = '$descricao'
						where sigla = '$sigla'";			
			}elseif (trim($sigla) != '') {
				$sql = "update unid_medida set sigla = '$sigla' where sigla = '$sigla'";
			}else{
				$sql = "update unid_medida set descricao = '$descricao' where sigla = '$sigla'";
			}

			$this->db->query($sql);

			//Verificar se a atualização ocorreu com sucesso
			if($this->db->affected_rows() > 0){
				//Fazemos a inserção no Log na nuvem
				//Fazemos a instância da model M_log
				$this->load->model('m_log');

				//Fazemos a chamada do método de inserção do Log
				$retorno_log = $this->m_log->inserir_log($usuario, $sql);

				if ($retorno_log['codigo'] == 1){
					$dados = array('codigo' => 1,
								'msg' => 'Unidade de medida atualizada corretamente');
				}else{
					$dados = array('codigo' => 7,
								'msg' => 'Houve algum problema no salvamento do Log, porém, 
											unidade de medida cadastrada corretamente');
				}			
			}else{
				$dados = array('codigo' => 6,
							'msg' => 'Não houve alteração no registro especificado.');
			}

		}catch (Exception $e) {
			$dados = array('codigo' => 00,
							'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',  
									$e->getMessage(), "\n");
		}
		//Envia o array $dados com as informações tratadas
		//acima pela estrutura de decisão if
		return $dados;
	}

	public function desativar($codigo, $usuario){
		try{
			//Há necessidade de verificar se existe algum produto com
			//essa unidade de medida já cadastrado, se tiver não podemos
			//desativar essa unidade
			$sql = "select * from produtos where unid_medida = $codigo and estatus = '' ";

			$retorno = $this->db->query($sql);

			//Verificar se a consulta trouxe algum produto
			if($retorno->num_rows() > 0){
				//Não posso fazer a desativação
				$dados = array('codigo' => 3,
							'msg' => 'Não podemos desativar, existem produtos com essa unidade de medida cadastrado(s).');
			}else{
				//Query de atualização dos dados
				$sql2 = "update unid_medida set estatus = 'D' where cod_unidade = '$codigo'";

				$this->db->query($sql2);

				//Verificar se a atualização ocorreu com sucesso
				if($this->db->affected_rows() > 0){
					//Fazemos a inserção no Log na nuvem
					//Fazemos a instância da model M_log
					$this->load->model('m_log');

					//Fazemos a chamada do método de inserção do Log
					$retorno_log = $this->m_log->inserir_log($usuario, $sql2);			

					if ($retorno_log['codigo'] == 1){
						$dados = array('codigo' => 1,
									'msg' => 'Unidade de medida DESATIVADA corretamente');
					}else{
						$dados = array('codigo' => 8,
									'msg' => 'Houve algum problema no salvamento do Log, porém, 
												usuário desativado corretamente');
					}
					
				}else{
					$dados = array('codigo' => 7,
								'msg' => 'Houve algum problema na DESATIVAÇÃO da unidade de medida');
				}
			}
		}catch (Exception $e) {
			$dados = array('codigo' => 00,
							'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',  
									$e->getMessage(), "\n");
		}
		//Envia o array $dados com as informações tratadas
		//acima pela estrutura de decisão if
		return $dados;
	}

	private function verificaUsuario($usuario) {
		try{
			//Função PRIVADA só para verificar se o usuário existe
			//em nosso banco de dados na tabela de usuários
			//Retornos:
			//1 - Usuário cadastrado na base de dados
			//8 - Usuário desativado no banco de dados
			//9 - Usuário não encontrado		

			$sql = "select * from usuarios where usuario = '$usuario'  ";

			$retorno = $this->db->query($sql);

			//Verificar se a consulta trouxe algum usuario
			if($retorno->num_rows() > 0){
				//Verifico o status do usuário
				if($retorno->row()->estatus == 'D'){
					//Não se pode cadastrar o usuário
					$dados = array('codigo' => 8,
							'msg' => 'Não pode cadastrar, usuário informado está DESATIVADO');
				}else{
					$dados = array('codigo' => 1,
								'msg' => 'Usuário ativo na base de dados');
				}
			}else{
				$dados = array('codigo' => 9,
								'msg' => 'Usuário não encontrado na base de dados');
			}
		}catch (Exception $e) {
			$dados = array('codigo' => 00,
							'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',  
									$e->getMessage(), "\n");
		}
		//Envia o array $dados com as informações tratadas
		//acima pela estrutura de decisão if
		return $dados;
	}

	public function verificaUM($unidadeMedida){
		try{
			//Query para consultar a unidade de medida		
			$sql = "select * from unid_medida 
					where sigla = '$unidadeMedida'
					and estatus = '' ";
			
			$retorno = $this->db->query($sql);

			if($retorno->num_rows() > 0){
				$dados = array('codigo' => 10,
							   'msg' => 'Unidade de medida já cadastrada na base de dados.');
			}else{
				$dados = array('codigo' => 2,
							'msg' => 'Unidade de medida não cadastrada.');	
			}
		}catch (Exception $e) {
			$dados = array('codigo' => 00,
							'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',  
									$e->getMessage(), "\n");
		}
		//Envia o array $dados com as informações tratadas
		//acima pela estrutura de decisão if
		return $dados;
	}
}
