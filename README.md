

# 🚗 Fleet Management API

API REST para gerenciamento de uma frota de veículos, permitindo cadastrar carros, controlar aluguéis, finalizar contratos e verificar automaticamente veículos que precisam de manutenção.

O projeto foi desenvolvido utilizando **Laravel** e segue uma organização baseada em responsabilidades, utilizando **Models, Controllers, Form Requests, Actions, Jobs e Scheduler**.

---

## 📋 Sobre o projeto

O sistema foi desenvolvido para controlar o ciclo de aluguel de veículos:

```text
Cadastro do carro
       ↓
Carro disponível
       ↓
Criação do aluguel
       ↓
Carro alugado
       ↓
Finalização do aluguel
       ↓
Cálculo do valor total
       ↓
Carro disponível novamente
````

Além disso, existe uma rotina automática que verifica a quilometragem dos veículos e identifica carros que atingiram o limite definido para manutenção.

---

## 🛠️ Tecnologias

* PHP
* Laravel
* MySQL
* Composer
* REST API
* Laravel Eloquent ORM
* Laravel Scheduler
* Laravel Jobs

---

## 📁 Estrutura do projeto

A aplicação utiliza uma separação de responsabilidades para evitar que Controllers concentrem regras de negócio.

```text
app/
├── Actions/
│   ├── CriarAluguelAction.php
│   └── FinalizarAluguelAction.php
│
├── Http/
│   ├── Controllers/
│   │   ├── CarroController.php
│   │   └── AlugueisController.php
│   │
│   └── Requests/
│       ├── CarroRequest.php
│       └── AluguelRequest.php
│
├── Jobs/
│   └── VerificarManutencaoJob.php
│
└── Models/
    ├── Carro.php
    └── Alugueis.php
```

---

# 🧠 Arquitetura

O projeto utiliza uma arquitetura baseada em responsabilidades.

### Route

Define o endpoint que será acessado pelo cliente.

```text
Route
  ↓
Controller
```

### Controller

Recebe a requisição, coordena o fluxo e retorna a resposta HTTP.

O Controller não deve concentrar regras complexas de negócio.

```text
Controller
  ↓
Action
```

### Action

Responsável por executar um caso de uso específico.

Exemplos:

```text
CriarAluguelAction
FinalizarAluguelAction
```

### Model

Responsável pela representação das entidades e relacionamento com o banco através do Eloquent.

```text
Carro
Aluguel
```

### Form Request

Responsável pela validação dos dados recebidos pela API.

```text
CarroRequest
AluguelRequest
```

### Job

Responsável por tarefas que podem ser executadas de forma assíncrona ou automática.

```text
VerificarManutencaoJob
```

### Scheduler

Define quando um Job será executado.

```text
Scheduler
   ↓
VerificarManutencaoJob
```

---

# 🗄️ Banco de dados

## Carros

Tabela:

```text
carros
```

Principais campos:

| Campo           | Descrição              |
| --------------- | ---------------------- |
| `id`            | Identificador do carro |
| `placa`         | Placa do veículo       |
| `modelo`        | Modelo do veículo      |
| `status`        | Estado atual do carro  |
| `quilometragem` | Quilometragem atual    |
| `created_at`    | Data de criação        |
| `updated_at`    | Data de atualização    |

Status utilizados:

```text
disponivel
alugado
manutencao
```

---

## Aluguéis

Tabela:

```text
alugueis
```

Principais campos:

| Campo          | Descrição                    |
| -------------- | ---------------------------- |
| `id`           | Identificador do aluguel     |
| `carro_id`     | Carro relacionado ao aluguel |
| `data_inicio`  | Início do aluguel            |
| `data_fim`     | Final do aluguel             |
| `valor_diaria` | Valor cobrado por diária     |
| `valor_total`  | Valor final do aluguel       |
| `status`       | Estado do aluguel            |
| `created_at`   | Data de criação              |
| `updated_at`   | Data de atualização          |

Status utilizados:

```text
ativo
finalizado
```

---

# 🔗 Relacionamentos

Um carro pode possuir vários aluguéis ao longo de sua vida útil.

```text
Carro 1 ───────── N Aluguéis
```

### Carro

```php
public function alugueis()
{
    return $this->hasMany(Aluguel::class);
}
```

### Aluguel

```php
public function carro()
{
    return $this->belongsTo(Carro::class);
}
```

---

# ⚙️ Regras de negócio

## Criação de aluguel

Um aluguel só pode ser criado quando:

* o carro existir;
* o carro estiver disponível;
* a data final for posterior à data inicial;
* os dados enviados forem válidos.

Quando o aluguel é criado:

```text
Aluguel → ativo
Carro   → alugado
```

---

## Finalização de aluguel

Ao finalizar um aluguel:

1. O aluguel precisa estar ativo.
2. A quantidade de dias utilizados é calculada.
3. O valor total é calculado.
4. O aluguel passa para `finalizado`.
5. O carro volta para `disponivel`.

### Fórmula

```text
valor_total = dias_de_uso × valor_diaria
```

Exemplo:

```text
Data inicial: 10/09
Data final:   13/09
Diária:       R$ 100,00

Dias utilizados: 3

Valor total:
3 × 100 = R$ 300,00
```

---

# 🔄 Actions

## CriarAluguelAction

Responsável por:

```text
validar disponibilidade
        ↓
criar aluguel
        ↓
alterar status do carro
```

---

## FinalizarAluguelAction

Responsável por:

```text
calcular dias
        ↓
calcular valor total
        ↓
finalizar aluguel
        ↓
liberar carro
```

As operações críticas utilizam transação de banco para manter a consistência dos dados.

```text
Transaction
    ↓
Tudo certo → COMMIT
Erro       → ROLLBACK
```

---

# ⏰ Manutenção automática

O projeto possui um Job:

```text
VerificarManutencaoJob
```

Sua responsabilidade é localizar veículos que atingiram:

```text
10.000 km ou mais
```

Exemplo:

```text
Carro A → 8.500 km
Carro B → 10.200 km  ⚠️
Carro C → 15.000 km  ⚠️
```

O Job identifica os veículos que precisam de manutenção.

O Scheduler está configurado para executar a rotina diariamente.

```php
Schedule::job(new VerificarManutencaoJob)
    ->dailyAt('02:00');
```

Durante o desenvolvimento, o Scheduler pode ser executado com:

```bash
php artisan schedule:work
```

---

# 🌐 Endpoints

Base da API:

```text
http://127.0.0.1:8000/api
```

## 🚗 Carros

### Listar carros

```http
GET /api/carros
```

### Criar carro

```http
POST /api/carros
```

Exemplo:

```json
{
    "placa": "ABC-1234",
    "modelo": "Civic",
    "quilometragem": 5000
}
```

### Buscar um carro

```http
GET /api/carros/{carro}
```

Exemplo:

```http
GET /api/carros/1
```

### Atualizar carro

```http
PUT /api/carros/{carro}
```

### Excluir carro

```http
DELETE /api/carros/{carro}
```

---

# 🚘 Aluguéis

### Listar aluguéis

```http
GET /api/alugueis
```

### Criar aluguel

```http
POST /api/alugueis
```

Exemplo:

```json
{
    "carro_id": 1,
    "data_inicio": "2026-09-15",
    "data_fim": "2026-09-18",
    "valor_diaria": 100
}
```

### Buscar aluguel

```http
GET /api/alugueis/{aluguel}
```

### Finalizar aluguel

```http
POST /api/alugueis/{aluguel}/finalizar
```

Exemplo:

```http
POST /api/alugueis/1/finalizar
```

---

# 🔀 Fluxo de uma requisição

## Criar aluguel

```text
POST /api/alugueis
        ↓
AluguelRequest
        ↓
AlugueisController
        ↓
CriarAluguelAction
        ↓
Carro + Aluguel
        ↓
Banco de dados
```

## Finalizar aluguel

```text
POST /api/alugueis/1/finalizar
        ↓
Route
        ↓
AlugueisController
        ↓
FinalizarAluguelAction
        ↓
Calcula dias
        ↓
Calcula valor
        ↓
Finaliza aluguel
        ↓
Libera carro
```

## Verificação de manutenção

```text
Scheduler
    ↓
VerificarManutencaoJob
    ↓
Busca carros >= 10.000 km
    ↓
Identifica veículos para manutenção
```

---

# 🚀 Instalação

## 1. Clonar o projeto

```bash
git clone <URL_DO_REPOSITORIO>
cd fleet-management-api
```

## 2. Instalar dependências

```bash
composer install
```

## 3. Criar arquivo `.env`

```bash
cp .env.example .env
```

No Windows, caso necessário, copie o arquivo `.env.example` manualmente.

## 4. Gerar a chave da aplicação

```bash
php artisan key:generate
```

## 5. Configurar o banco

No `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fleet_management
DB_USERNAME=root
DB_PASSWORD=admin
```

Ajuste os valores conforme a configuração local do MySQL.

## 6. Executar as migrations

```bash
php artisan migrate
```

Para recriar o banco completamente durante o desenvolvimento:

```bash
php artisan migrate:fresh
```

---

# ▶️ Executando a aplicação

Inicie o servidor:

```bash
php artisan serve
```

A API estará disponível em:

```text
http://127.0.0.1:8000
```

Para executar o Scheduler:

```bash
php artisan schedule:work
```

---

# 🧪 Testando a API

As requisições podem ser testadas utilizando:

* Postman
* Insomnia
* qualquer cliente HTTP

Exemplo:

```http
POST http://127.0.0.1:8000/api/carros
Content-Type: application/json
```

```json
{
    "placa": "ABC-1234",
    "modelo": "Civic",
    "quilometragem": 5000
}
```

Depois:

```http
POST http://127.0.0.1:8000/api/alugueis
Content-Type: application/json
```

```json
{
    "carro_id": 1,
    "data_inicio": "2026-09-15",
    "data_fim": "2026-09-18",
    "valor_diaria": 100
}
```

E finalmente:

```http
POST http://127.0.0.1:8000/api/alugueis/1/finalizar
```

---

# 📌 Próximas evoluções

Algumas funcionalidades podem ser adicionadas futuramente:

* autenticação de usuários;
* autorização por perfil;
* cadastro de clientes;
* histórico de manutenção;
* notificações por e-mail;
* filas para processamento dos Jobs;
* relatórios financeiros;
* testes automatizados;
* documentação OpenAPI / Swagger;
* paginação dos endpoints;
* filtros e busca de veículos.

---

# 👨‍💻 Objetivo do projeto

Este projeto foi desenvolvido com foco em praticar:

* desenvolvimento de APIs REST;
* Laravel;
* Eloquent ORM;
* relacionamentos entre Models;
* Form Requests;
* Actions;
* Jobs;
* Scheduler;
* regras de negócio;
* transações de banco de dados;
* organização e separação de responsabilidades.

---

## 📄 Licença

Este projeto é destinado a fins de estudo e desenvolvimento.

```
```
