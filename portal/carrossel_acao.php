<?php
require 'conexao.php';

// ADICIONAR
if ($_SERVER['REQUEST_METHOD']=='POST' && $_POST['acao']=='adicionar') {
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $imagem = '';
    $permitidas = ['jpg','jpeg','png','gif','webp'];

    if (!empty($_FILES['imagem']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $permitidas)) {
            echo "<div class='alert alert-danger'>Extensão não permitida!</div>";
            exit;
        }
        $nomeArq = uniqid()."_".basename($_FILES['imagem']['name']);
        $dest = "uploads/".$nomeArq;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dest)) {
            $imagem = $dest;
        }
    }
    if ($titulo && $descricao && $imagem) {
        $stmt = $conn->prepare("INSERT INTO carrossel (imagem, titulo, descricao) VALUES (?,?,?)");
        $stmt->bind_param("sss",$imagem, $titulo, $descricao);
        $stmt->execute();
        echo "<div class='alert alert-success'>Banner adicionado!</div>";
    } else {
        echo "<div class='alert alert-danger'>Todos os campos são obrigatórios.</div>";
    }
    exit;
}

// LISTAR
if ($_GET['acao']=='listar') {
    $res = $conn->query("SELECT * FROM carrossel ORDER BY id DESC");
    while($row = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='".htmlspecialchars($row['imagem'])."' class='carrossel-img'></td>";
        echo "<td>".htmlspecialchars($row['titulo'])."</td>";
        echo "<td>".htmlspecialchars($row['descricao'])."</td>";
        echo "<td>
        <button class='btn btn-info btn-xs btn-editar' data-id='{$row['id']}'>Editar</button>
        <button class='btn btn-danger btn-xs btn-excluir' data-id='{$row['id']}'>Excluir</button>
        </td>";
        echo "</tr>";
    }
    exit;
}

// EXCLUIR
if ($_POST['acao']=='excluir') {
    $id = intval($_POST['id']);
    $res = $conn->query("SELECT imagem FROM carrossel WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if (file_exists($row['imagem'])) unlink($row['imagem']);
    }
    $conn->query("DELETE FROM carrossel WHERE id=$id");
    echo "<div class='alert alert-success'>Excluído!</div>";
    exit;
}

// FORMULÁRIO EDIÇÃO
if ($_GET['acao']=='form_editar') {
    $id = intval($_GET['id']);
    $row = $conn->query("SELECT * FROM carrossel WHERE id=$id")->fetch_assoc();
    ?>
    <div class="modal-dialog"><div class="modal-content">
      <form id="formEditarCarrossel" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <div class="modal-header">
          <h4 class="modal-title">Editar Banner</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <label>Imagem atual:<br>
            <img src="<?= htmlspecialchars($row['imagem']) ?>" style="max-width:150px;">
          </label>
          <input type="file" name="imagem" class="form-control">
          <label>Título</label>
          <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($row['titulo']) ?>" required>
          <label>Descrição</label>
          <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($row['descricao']) ?>" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Salvar</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div></div>
    <?php
    exit;
}

// SALVAR EDIÇÃO
if ($_SERVER['REQUEST_METHOD']=='POST' && $_POST['acao']=='editar') {
    $id = intval($_POST['id']);
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $imgSql = "";

    if (!empty($_FILES['imagem']['name'])) {
        $permitidas = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $permitidas)) {
            echo "<div class='alert alert-danger'>Extensão não permitida!</div>";
            exit;
        }
        $nomeArq = uniqid()."_".basename($_FILES['imagem']['name']);
        $dest = "uploads/".$nomeArq;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dest)) {
            $row = $conn->query("SELECT imagem FROM carrossel WHERE id=$id")->fetch_assoc();
            if ($row && file_exists($row['imagem'])) unlink($row['imagem']);
            $imgSql = ", imagem='$dest'";
        }
    }
    if ($titulo && $descricao) {
        $sql = "UPDATE carrossel SET titulo='$titulo', descricao='$descricao' $imgSql WHERE id=$id";
        $conn->query($sql);
        echo "<div class='alert alert-success'>Atualizado!</div>";
    } else {
        echo "<div class='alert alert-danger'>Campos obrigatórios.</div>";
    }
    exit;
}
?>
