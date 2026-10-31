-- 1. Tabela de Lojas (Tenants) - O coração do SaaS
CREATE TABLE tenants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_fantasia VARCHAR(100) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL, -- Ex: 'hugo-lanches'
    cnpj VARCHAR(18),
    whatsapp_numero VARCHAR(20),
    cor_principal VARCHAR(7) DEFAULT '#F5C400',
    logo VARCHAR(255),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categorias (vinculadas à loja)
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    ordem INT DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

-- 3. Ingredientes (para a montagem e adicionais)
CREATE TABLE ingredientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) DEFAULT 0.00,
    categoria_ingrediente ENUM('Pão', 'Carne', 'Queijo', 'Molho', 'Salada', 'Adicional') NOT NULL,
    imagem_montagem VARCHAR(255), -- PNG transparente
    imagem_cardapio VARCHAR(255),
    estoque INT DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

-- 4. Produtos
CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    categoria_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    preco_promocional DECIMAL(10,2),
    imagem VARCHAR(255),
    permite_montagem TINYINT(1) DEFAULT 0,
    destaque TINYINT(1) DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- 5. Clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    nome VARCHAR(100),
    telefone VARCHAR(20),
    data_ultimo_pedido DATETIME,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

-- 6. Endereços
CREATE TABLE enderecos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    cep VARCHAR(10),
    rua VARCHAR(255),
    numero VARCHAR(20),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    referencia VARCHAR(255),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- 7. Pedidos
CREATE TABLE pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    cliente_id INT NOT NULL,
    endereco_id INT, -- NULL se for retirada
    numero_pedido VARCHAR(10) UNIQUE NOT NULL, -- Ex: #1042
    tipo_entrega ENUM('Entrega', 'Retirada') NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    taxa_entrega DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    forma_pagamento VARCHAR(50),
    troco_para DECIMAL(10,2),
    status ENUM('Novo', 'Confirmado', 'Em Preparação', 'Pronto', 'Saiu para Entrega', 'Entregue', 'Cancelado') DEFAULT 'Novo',
    observacao TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    impresso TINYINT(1) DEFAULT 0,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- 8. Itens do Pedido
CREATE TABLE pedido_itens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- 9. Ingredientes do Item (para rastrear o que foi montado no lanche)
CREATE TABLE pedido_item_ingredientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_item_id INT NOT NULL,
    ingrediente_id INT NOT NULL,
    quantidade INT DEFAULT 1,
    preco_unitario DECIMAL(10,2),
    FOREIGN KEY (pedido_item_id) REFERENCES pedido_itens(id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id)
);

-- 10. Usuários do Sistema (Admin Master e Lojistas)
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT, -- NULL para o Super Admin (Você)
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin_master', 'dono_loja', 'atendente', 'cozinha') NOT NULL,
    ativo TINYINT(1) DEFAULT 1
);