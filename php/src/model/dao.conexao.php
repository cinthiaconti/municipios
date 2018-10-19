<?php

class Connection {

    private $user = "postgres";
    private $password = "postgres";
    private $db = "municipios";
    private $host = "localhost";
    private $port = "5432";
    private $conexao;


    public function __construct() {
        $this->openConnection();
    }

    public function __destruct() {
        $this->closeConnection();
    }

    private function openConnection() {

        try {
            $this->conexao = pg_connect("host=$this->host user=$this->user password=$this->password dbname=$this->db port=$this->port");

            if (pg_connection_status($this->conexao) !== 0) {
                throw new Exception("erroconexao");
            }
            
        } catch (Exception $erro) {
            echo $erro->getMessage();
            exit;
        }
    }
    
    
    private function closeConnection() {
        return pg_connection_status($this->conexao) === 0 ? (!pg_connection_busy($this->conexao) ? @pg_close($this->conexao) : false) : false;
    }

    public function executeSQL($sql) {
        $msgErro = '';

        if (pg_connection_status($this->conexao) === 0) {
            if (strpos(strtoupper($sql), "ELETE") != 1) {
                $result = pg_query($this->conexao, $sql);
                return $result;

            } else {
                $this->executeSQLDelete($sql);
            }
        } else {
            $msgErro = 'Ocorreu um erro [' . pg_last_error() . ']';
            throw new Exception($msgErro);
            exit;
        }
    }



}

?>
