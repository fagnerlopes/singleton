<?php

final class Connection {
  private static $instance;

  private function __construct() 
  {
    //Classe privada para não permitir a instancia do objeto
  }

  /**
   * Método estático privado que permite o carregamento do arquivo
   * @param $arquivo string
   * @return array
   */
  private static function load(): array
  {
      $fileIni = 'database/configdb.ini';
  
      if(file_exists($fileIni)) {
          $data = parse_ini_file($fileIni);
      } else {
          throw new Exception('Erro: Arquivo não encontrado');
      }
      return $data;
  }

  /**
   * Método montar string de conexao e gerar o objeto PDO
   * @param $dados array
   * @return PDO
   */ 
  private static function make(array $data): PDO
  {
      // capturar dados
      $sgdb     = isset($data['sgdb']) ? $data['sgdb'] : NULL;
      $usuario  = isset($data['usuario']) ? $data['usuario'] : NULL;
      $senha    = isset($data['senha']) ? $data['senha'] : NULL;
      $banco    = isset($data['banco']) ? $data['banco'] : NULL;
      $servidor = isset($data['servidor']) ? $data['servidor'] : NULL;
      $porta    = isset($data['porta']) ? $data['porta'] : NULL;
  
      if(!is_null($sgdb)) {
          // selecionar banco - criar string de conexão
          switch (strtoupper($sgdb)) {
              case 'MYSQL' : $porta = isset($porta) ? $porta : 3306 ; return new PDO("mysql:host={$servidor};port={$porta};dbname={$banco}", $usuario, $senha);
                break;
              case 'MSSQL' : $porta = isset($porta) ? $porta : 1433 ;return new PDO("mssql:host={$servidor},{$porta};dbname={$banco}", $usuario, $senha);
                break;
              case 'PGSQL' : $porta = isset($porta) ? $porta : 5432 ;return new PDO("pgsql:dbname={$banco}; user={$usuario}; password={$senha}, host={$servidor};port={$porta}");
                break;
              case 'SQLITE' : return new PDO("sqlite:{$banco}");
                break;
              case 'OCI8' : return new PDO("oci:dbname={$banco}", $usuario, $senha);
                break;
              case 'FIREBIRD' : return new PDO("firebird:dbname={$banco}",$usuario, $senha);
                break;
          }
      } else {
          throw new Exception('Erro: tipo de banco de dados não informado');
      }
  }

  /**
 * Método estático que devolve a instancia ativa
 *
 */
  public static function getInstance() 
  {
      if (!isset(self::$instance)) {
          self::$instance = self::make(self::load()); // new PDO('mysql:dbname=mydatabase;host=localhost', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
          self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }

      return self::$instance;
  }

}