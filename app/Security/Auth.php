<?php

class Auth {

    private static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function hashSenha(string $senha): string {
        return hash('sha256', $senha);
    }

    public static function verificarSenha(string $senhaDigitada, string $hashBanco): bool {
        $hashBanco = trim($hashBanco);
        $senhaDigitada = trim($senhaDigitada);

        // 1. SHA-256 Exato
        if (hash_equals(self::hashSenha($senhaDigitada), $hashBanco)) {
            return true;
        }

        // 2. SHA-256 Tolerante para Senhas Temporárias (Maiúsculas / Minúsculas)
        if (hash_equals(self::hashSenha(strtoupper($senhaDigitada)), $hashBanco)) {
            return true;
        }
        if (hash_equals(self::hashSenha(strtolower($senhaDigitada)), $hashBanco)) {
            return true;
        }

        // 3. MD5 Legado (32 caracteres)
        if (strlen($hashBanco) === 32 && md5($senhaDigitada) === $hashBanco) {
            return true;
        }

        // 4. Texto puro
        if ($senhaDigitada === $hashBanco) {
            return true;
        }

        return false;
    }

    public static function isMasterLoggedIn(): bool {
        self::startSession();
        return !empty($_SESSION['master_logged']) && $_SESSION['master_logged'] === true;
    }

    public static function loginMaster(array $dados): void {
        self::startSession();
        $_SESSION['master_logged'] = true;
        $_SESSION['master_id']     = (int)($dados['admin_id'] ?? ($dados['id'] ?? 1));
        $_SESSION['master_nome']   = $dados['admin_nome'] ?? ($dados['nome'] ?? 'Super Admin');
        $_SESSION['master_email']  = $dados['admin_email'] ?? ($dados['email'] ?? '');
    }

    /**
     * Valida se o Lojista está logado e pertence à loja atual (slug)
     */
    public static function isLojistaLoggedIn(string $slug = ''): bool {
        self::startSession();
        if (empty($_SESSION['lojista_logged']) || $_SESSION['lojista_logged'] !== true) {
            return false;
        }
        if (!empty($slug)) {
            $sessaoSlug = strtolower(trim($_SESSION['lojista_slug'] ?? ''));
            $atualSlug  = strtolower(trim($slug));
            return ($sessaoSlug === $atualSlug);
        }
        return true;
    }

    public static function loginLojista(array $user): void {
        self::startSession();
        $_SESSION['lojista_logged']    = true;
        $_SESSION['lojista_id']        = (int)$user['user_id'];
        $_SESSION['lojista_nome']      = $user['user_name'];
        $_SESSION['lojista_slug']      = trim($user['user_virtual_env']);
        $_SESSION['lojista_email']     = $user['user_email'];
        $_SESSION['current_tenant_id'] = (int)$user['user_id'];
    }

    public static function isClienteLoggedIn(): bool {
        self::startSession();
        return !empty($_SESSION['Cliente_ID']);
    }

    public static function logout(): void {
        self::startSession();
        session_unset();
        session_destroy();
    }
}