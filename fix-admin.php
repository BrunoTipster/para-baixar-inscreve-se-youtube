<?php
/**
 * fix-admin.php — Corrige a senha do admin
 * APAGUE ESTE ARQUIVO APÓS USO!
 */

require_once __DIR__ . '/config/database.php';

$db = getDB();

// Gera o hash correto de 'admin123'
$senhaCorreta = password_hash('admin123', PASSWORD_BCRYPT);

// Atualiza no banco
$stmt = $db->prepare("UPDATE admins SET senha = ? WHERE usuario = 'admin'");
$resultado = $stmt->execute([$senhaCorreta]);

if ($resultado) {
    echo "<h2 style='font-family:Arial;color:green;'>✅ Senha do admin corrigida com sucesso!</h2>";
    echo "<p style='font-family:Arial;'>Agora acesse: <a href='/admin/login.php'>/admin/login.php</a></p>";
    echo "<p style='font-family:Arial;'><strong>Usuário:</strong> admin<br><strong>Senha:</strong> admin123</p>";
    echo "<p style='font-family:Arial;color:red;'><strong>⚠️ APAGUE ESTE ARQUIVO AGORA!</strong> (fix-admin.php)</p>";
} else {
    echo "<h2 style='font-family:Arial;color:red;'>❌ Erro ao atualizar a senha.</h2>";
}
?>
