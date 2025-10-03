/*
  # Create Hamburgueria System Tables

  1. New Tables
    - `produtos`
      - `id` (serial, primary key)
      - `nome` (text, product name)
      - `descricao` (text, product description)
      - `preco` (decimal, product price)
      - `categoria` (text, product category)
      - `ativo` (boolean, active status)
      - `created_at` (timestamp)
      
    - `motoboys`
      - `id` (serial, primary key)
      - `nome` (text, motoboy name)
      - `telefone` (text, phone number)
      - `placa_moto` (text, motorcycle plate)
      - `ativo` (boolean, active status)
      - `created_at` (timestamp)
      
    - `entregas`
      - `id` (serial, primary key)
      - `motoboy_id` (integer, foreign key to motoboys)
      - `cliente_nome` (text, customer name)
      - `cliente_telefone` (text, customer phone)
      - `endereco` (text, delivery address)
      - `valor_entrega` (decimal, delivery fee)
      - `status` (text, delivery status)
      - `data_entrega` (timestamp)
      - `created_at` (timestamp)
      
    - `pedidos`
      - `id` (serial, primary key)
      - `entrega_id` (integer, foreign key to entregas)
      - `produto_id` (integer, foreign key to produtos)
      - `quantidade` (integer, quantity)
      - `preco_unitario` (decimal, unit price)
      - `subtotal` (decimal, item subtotal)
      - `created_at` (timestamp)
      
    - `caixa`
      - `id` (serial, primary key)
      - `data_abertura` (timestamp, opening date)
      - `data_fechamento` (timestamp, closing date)
      - `valor_inicial` (decimal, initial amount)
      - `valor_final` (decimal, final amount)
      - `status` (text, cashier status: aberto/fechado)
      - `created_at` (timestamp)
      
    - `movimentacoes_caixa`
      - `id` (serial, primary key)
      - `caixa_id` (integer, foreign key to caixa)
      - `tipo` (text, transaction type: entrada/saida)
      - `descricao` (text, description)
      - `valor` (decimal, amount)
      - `created_at` (timestamp)

  2. Security
    - Enable RLS on all tables
    - Add policies for authenticated access
*/

-- Create produtos table
CREATE TABLE IF NOT EXISTS produtos (
  id SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  descricao TEXT,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  categoria TEXT DEFAULT '',
  ativo BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now()
);

ALTER TABLE produtos ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow all operations on produtos"
  ON produtos FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

-- Create motoboys table
CREATE TABLE IF NOT EXISTS motoboys (
  id SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  telefone TEXT DEFAULT '',
  placa_moto TEXT DEFAULT '',
  ativo BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now()
);

ALTER TABLE motoboys ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow all operations on motoboys"
  ON motoboys FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

-- Create entregas table
CREATE TABLE IF NOT EXISTS entregas (
  id SERIAL PRIMARY KEY,
  motoboy_id INTEGER REFERENCES motoboys(id) ON DELETE SET NULL,
  cliente_nome TEXT NOT NULL,
  cliente_telefone TEXT DEFAULT '',
  endereco TEXT NOT NULL,
  valor_entrega DECIMAL(10,2) DEFAULT 0.00,
  status TEXT DEFAULT 'pendente',
  data_entrega TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ DEFAULT now()
);

ALTER TABLE entregas ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow all operations on entregas"
  ON entregas FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

-- Create pedidos table
CREATE TABLE IF NOT EXISTS pedidos (
  id SERIAL PRIMARY KEY,
  entrega_id INTEGER REFERENCES entregas(id) ON DELETE CASCADE,
  produto_id INTEGER REFERENCES produtos(id) ON DELETE SET NULL,
  quantidade INTEGER NOT NULL DEFAULT 1,
  preco_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMPTZ DEFAULT now()
);

ALTER TABLE pedidos ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow all operations on pedidos"
  ON pedidos FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

-- Create caixa table
CREATE TABLE IF NOT EXISTS caixa (
  id SERIAL PRIMARY KEY,
  data_abertura TIMESTAMPTZ DEFAULT now(),
  data_fechamento TIMESTAMPTZ,
  valor_inicial DECIMAL(10,2) DEFAULT 0.00,
  valor_final DECIMAL(10,2) DEFAULT 0.00,
  status TEXT DEFAULT 'aberto',
  created_at TIMESTAMPTZ DEFAULT now()
);

ALTER TABLE caixa ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow all operations on caixa"
  ON caixa FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

-- Create movimentacoes_caixa table
CREATE TABLE IF NOT EXISTS movimentacoes_caixa (
  id SERIAL PRIMARY KEY,
  caixa_id INTEGER REFERENCES caixa(id) ON DELETE CASCADE,
  tipo TEXT NOT NULL,
  descricao TEXT DEFAULT '',
  valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMPTZ DEFAULT now()
);

ALTER TABLE movimentacoes_caixa ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow all operations on movimentacoes_caixa"
  ON movimentacoes_caixa FOR ALL
  TO authenticated
  USING (true)
  WITH CHECK (true);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_entregas_motoboy ON entregas(motoboy_id);
CREATE INDEX IF NOT EXISTS idx_entregas_status ON entregas(status);
CREATE INDEX IF NOT EXISTS idx_pedidos_entrega ON pedidos(entrega_id);
CREATE INDEX IF NOT EXISTS idx_pedidos_produto ON pedidos(produto_id);
CREATE INDEX IF NOT EXISTS idx_movimentacoes_caixa ON movimentacoes_caixa(caixa_id);
CREATE INDEX IF NOT EXISTS idx_caixa_status ON caixa(status);
