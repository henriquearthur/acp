<?
if($_POST['form'] == 'form') {
    $nick = $_POST['nick'];
    $colante = $core->clear($_POST['colante']);
    $prosseguir = true;

    if(empty($nick)) {
        $form_return .= aviso_red("Preencha todos os campos.");
        $prosseguir = false;
    }

    if($prosseguir) {
        $nicks = explode("\n", $nick);
        $nicks = array_filter($nicks);

        foreach ($nicks as $atual) {
            $atual = trim(preg_replace('/\s\s+/', ' ', $atual));
            $sql3 = $conn->prepare("SELECT * FROM usuarios WHERE nick = ?");
            $sql3->bindParam(1, $atual);
            $sql3->execute();
            $sql4 = $sql3->fetch();

            if($sql4) {
                $sql5 = $conn->prepare("SELECT count(id) FROM hm_colantes_comprados WHERE id_usuario = ? AND id_colante = ?");
                $sql5->bindValue(1, $sql4['id']);
                $sql5->bindValue(2, $colante);
                $sql5->execute();
                $rows = $sql5->fetchColumn();

                if($rows > 0) {
                    $form_return .= aviso_red("O colante não foi dado ao usuário $atual pois ele já o possui.");
                } else {
                    $insert_data['id_usuario'] = $sql4['id'];
                    $insert_data['id_colante'] = $colante;
                    $insert_data['autor'] = '[sys] dado por ' . $core->autor;
                    $insert_data['data'] = $timestamp;

                    $insert = $sqlActions->insert("hm_colantes_comprados", $insert_data);

                    if($insert) {
                        $_emblema = $conn->prepare("SELECT nome, imagem FROM hm_colantes WHERE id = ?");
                        $_emblema->bindValue(1, $colante);
                        $_emblema->execute();
                        $embl = $_emblema->fetch();

                        $core->sendNtfUser($sql4['nick'], "Você ganhou o colante <b>{$core->clear($embl['nome'])}</b>.", "/home/" . $sql4['nick'], $embl['imagem']);
                        $core->logger("O usuário deu um colante ao usuário $atual (c. #$colante)", "acao");
                    } else {
                        $form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
                    }
                }
            } else {
                $form_return .= aviso_red("O colante não foi dado ao usuário $atual pois ele não existe.");
            }
        }
    }

    if($prosseguir) {
        $form_return .= aviso_green("Sucesso!");
        foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
    }
}
?>
<div class="box-content">
    <div class="title-section"><?=$mdl['nome'];?></div>

    <? echo $form_return;

    $form = new Form('form-submit', '');

    $form->createTextarea('Nick do(s) usuário(s)', 'nick', '', '', '', '', 'Separe os nicks dos usuários que receberão o colante por linha.');

    $categorias = array();
    $sql = $conn->query("SELECT * FROM hm_colantes ORDER BY id DESC");
    while($sql2 = $sql->fetch()) {
        $sql3 = $conn->prepare("SELECT nome FROM hm_colantes_cat WHERE id = ?");
        $sql3->bindValue(1, $sql2['cat_id']);
        $sql3->execute();
        $sql4 = $sql3->fetch();

        $atual = array("label" => $core->clear($sql2['nome']) . ' (' . $core->clear($sql4['nome']) . ')', "value" => $sql2['id']);
        $categorias[] = $atual;
    }

    $form->createSelect('Colante', 'colante', $categorias, $ex['cat_id']);

    $form->generateForm();
    echo $form->form; ?>
</div>