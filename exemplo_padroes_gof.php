<?php

/**
 * Exemplo de Uso dos Padrões GOF
 *
 * Este arquivo demonstra como os três padrões GOF implementados
 * funcionam em conjunto no sistema.
 *
 * ATENÇÃO: Este é um arquivo de exemplo/demonstração.
 * Não deve ser executado em produção.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/autoloader.php';

use App\Models\Database;
use App\Models\ConteudoEstudo;
use App\Models\Meta;
use App\Controllers\UsuarioController;

echo "==============================================\n";
echo "   DEMONSTRAÇÃO DOS PADRÕES GOF\n";
echo "==============================================\n\n";

// ============================================================================
// 1. PADRÃO SINGLETON - Database
// ============================================================================
echo "1. PADRÃO SINGLETON (Database)\n";
echo "----------------------------------------------\n";

// Obtém a primeira instância
$db1 = Database::getInstance();
echo "✓ Primeira instância criada\n";

// Obtém a "segunda" instância (na verdade, a mesma)
$db2 = Database::getInstance();
echo "✓ Segunda instância 'criada'\n";

// Verifica se são a mesma instância
if ($db1 === $db2) {
  echo "✓ SUCESSO: Ambas são a MESMA instância! (Singleton funcionando)\n";
} else {
  echo "✗ ERRO: Instâncias diferentes (Singleton não funcionou)\n";
}

// Tenta criar instância diretamente (vai falhar)
echo "\nTentando criar instância com 'new Database()'...\n";
try {
  // $db3 = new Database(); // Descomente para ver o erro
  echo "✗ Isso não deveria funcionar (construtor é privado)\n";
} catch (Error $e) {
  echo "✓ SUCESSO: Erro capturado - Construtor privado impede instanciação direta\n";
}

echo "\n";

// ============================================================================
// 2. PADRÃO FACADE - UsuarioController
// ============================================================================
echo "2. PADRÃO FACADE (UsuarioController)\n";
echo "----------------------------------------------\n";

echo "O UsuarioController esconde a complexidade da autenticação:\n";
echo "  • Validação de dados\n";
echo "  • Token CSRF\n";
echo "  • Consulta ao banco\n";
echo "  • Hash de senha\n";
echo "  • Gerenciamento de sessão\n";
echo "  • Mensagens flash\n";
echo "  • Redirecionamentos\n\n";

echo "Interface SIMPLES para o roteador:\n";
echo "  \$controller = new UsuarioController();\n";
echo "  \$controller->login(); // Toda complexidade escondida!\n\n";

echo "✓ Facade fornece interface simplificada para subsistema complexo\n";
echo "\n";

// ============================================================================
// 3. PADRÃO OBSERVER - ConteudoEstudo e Meta
// ============================================================================
echo "3. PADRÃO OBSERVER (ConteudoEstudo → Meta)\n";
echo "----------------------------------------------\n";

// Simulação do fluxo (comentado para não executar no banco real)
echo "FLUXO DE EXECUÇÃO:\n\n";

echo "1️⃣  Usuário marca um ConteudoEstudo como 'CONCLUÍDO'\n";
echo "    \$conteudo->alterarStatus(5, 'concluido');\n\n";

echo "2️⃣  ConteudoEstudo atualiza status no banco de dados\n\n";

echo "3️⃣  ConteudoEstudo carrega observadores (Metas relacionadas)\n";
echo "    \$this->carregarObservadores(\$conteudoId);\n\n";

echo "4️⃣  ConteudoEstudo NOTIFICA todos os observadores\n";
echo "    \$this->notify(['evento' => 'conteudo_concluido', ...]);\n\n";

echo "5️⃣  Cada MetaObserver recebe a notificação\n";
echo "    MetaObserver::update(\$subject, \$data);\n\n";

echo "6️⃣  MetaObserver verifica se conteúdo pertence à sua Meta\n\n";

echo "7️⃣  Meta marca conteúdo como concluído\n";
echo "    \$meta->marcarConteudoConcluido(\$metaId, \$conteudoId);\n\n";

echo "8️⃣  Meta RECALCULA automaticamente seu progresso\n";
echo "    \$meta->calcularProgresso(\$metaId);\n\n";

echo "9️⃣  Se Meta atingiu 100%, é marcada como concluída!\n\n";

echo "✓ Observer permite notificação automática sem acoplamento direto\n";

echo "\n";

// ============================================================================
// EXEMPLO PRÁTICO COMENTADO
// ============================================================================
echo "==============================================\n";
echo "   EXEMPLO PRÁTICO (Código Comentado)\n";
echo "==============================================\n\n";

echo "// Para testar na prática, descomente o código abaixo:\n\n";

echo "/*\n";
echo "// Conecta ao banco usando Singleton\n";
echo "\$db = Database::getInstance()->getConnection();\n\n";

echo "// Cria um conteúdo de estudo\n";
echo "\$conteudo = new ConteudoEstudo();\n";
echo "\$conteudoId = \$conteudo->criar([\n";
echo "    'titulo' => 'Estudar Padrões GOF',\n";
echo "    'usuario_id' => 1,\n";
echo "    'status' => 'em_andamento'\n";
echo "]);\n\n";

echo "// Cria uma meta\n";
echo "\$meta = new Meta();\n";
echo "\$metaId = \$meta->criar([\n";
echo "    'titulo' => 'Concluir curso de Design Patterns',\n";
echo "    'usuario_id' => 1,\n";
echo "    'data_alvo' => '2025-12-31'\n";
echo "]);\n\n";

echo "// Vincula conteúdo à meta\n";
echo "\$meta->adicionarConteudo(\$metaId, \$conteudoId);\n\n";

echo "// Marca conteúdo como concluído\n";
echo "// O padrão OBSERVER entra em ação automaticamente!\n";
echo "\$conteudo->alterarStatus(\$conteudoId, ConteudoEstudo::STATUS_CONCLUIDO);\n\n";

echo "// Resultado: Meta atualiza seu progresso AUTOMATICAMENTE! 🎉\n";
echo "echo 'Progresso da meta: ' . \$meta->findById(\$metaId)['percentual_progresso'] . '%';\n";
echo "*/\n\n";

// ============================================================================
// BENEFÍCIOS DOS PADRÕES
// ============================================================================
echo "==============================================\n";
echo "   BENEFÍCIOS DOS PADRÕES IMPLEMENTADOS\n";
echo "==============================================\n\n";

echo "📌 SINGLETON (Database)\n";
echo "  ✅ Uma única conexão com o banco\n";
echo "  ✅ Economia de recursos\n";
echo "  ✅ Controle centralizado\n";
echo "  ✅ Lazy Loading\n\n";

echo "📌 FACADE (UsuarioController)\n";
echo "  ✅ Interface simples para autenticação\n";
echo "  ✅ Complexidade escondida\n";
echo "  ✅ Fácil manutenção\n";
echo "  ✅ Código organizado\n\n";

echo "📌 OBSERVER (ConteudoEstudo/Meta)\n";
echo "  ✅ Notificação automática\n";
echo "  ✅ Desacoplamento total\n";
echo "  ✅ Atualização reativa\n";
echo "  ✅ Escalável para múltiplos observadores\n\n";

echo "==============================================\n";
echo "Consulte README_GOF.md para documentação completa\n";
echo "==============================================\n";
