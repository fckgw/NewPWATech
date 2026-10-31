<?php
require_once __DIR__ . '/../../config/configuracoes.php';

class Conexao {
    private static ?PDO $instancia = null;

    public static function getConexao(): PDO {
        if (self::$instancia === null) {
            $host    = 'pdpwaapppainel.mysql.dbaas.com.br';
            $db      = 'pdpwaapppainel';
            $usuario = 'pdpwaapppainel';
            $senha   = 'BDSoft@102030';

            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

            $opcoes = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT            => 5
            ];

            self::$instancia = new PDO($dsn, $usuario, $senha, $opcoes);
        }

        return self::$instancia;
    }
}