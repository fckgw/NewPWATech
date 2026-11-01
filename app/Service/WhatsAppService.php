<?php

class WhatsAppService {

    /**
     * Formata o telefone para o padrão internacional (DDI 55 + DDD + Número)
     */
    public static function formatarNumero(string $telefone): string {
        $num = preg_replace('/\D/', '', $telefone);
        if (strlen($num) === 10 || strlen($num) === 11) {
            $num = '55' . $num;
        }
        return $num;
    }

    /**
     * Gera o link do WhatsApp com mensagem personalizada para cada fase
     */
    public static function gerarLinkStatus(array $pedido, string $nomeLoja): string {
        $tel = self::formatarNumero($pedido['telefone'] ?? '');
        $codigo = $pedido['codigo_pedido'] ?? $pedido['id'];
        $cliente = $pedido['nome'] ?? 'Cliente';
        $status = strtolower(trim($pedido['status'] ?? ''));

        switch ($status) {
            case 'em andamento':
            case 'em preparo':
                $msg = "Olá *{$cliente}*! 👋\nSeu pedido *#{$codigo}* no *{$nomeLoja}* já foi aceito e está *EM PREPARO* na nossa cozinha! 🍔🔥\nEm breve avisamos quando sair.";
                break;

            case 'saiu para entrega':
                $msg = "Olá *{$cliente}*! 🛵💨\nBoas notícias: seu pedido *#{$codigo}* no *{$nomeLoja}* *SAIU PARA ENTREGA* e está a caminho do seu endereço!\nFique atento para receber.";
                break;

            case 'finalizado':
            case 'entregue':
                $msg = "Olá *{$cliente}*! ✅\nSeu pedido *#{$codigo}* foi *FINALIZADO* com sucesso!\nAgradecemos pela preferência no *{$nomeLoja}*. Bom apetite e volte sempre! ⭐";
                break;

            case 'cancelado':
                $msg = "Olá *{$cliente}*.\nInformamos que seu pedido *#{$codigo}* no *{$nomeLoja}* foi cancelado. Se tiver dúvidas, entre em contato conosco.";
                break;

            case 'aberto':
            case 'recebido':
            default:
                $msg = "Olá *{$cliente}*! 📋\nRecebemos o seu pedido *#{$codigo}* no *{$nomeLoja}* e já estamos conferindo.";
                break;
        }

        return "https://api.whatsapp.com/send?phone={$tel}&text=" . urlencode($msg);
    }
}