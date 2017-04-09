<? if($_GET['a'] == 2) {
    $id = $_GET['id'];

    $_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
    $_ex->bindValue(1, $id);
    $_ex->execute();
    $ex = $_ex->fetch();

    $sql3 = $conn->prepare("SELECT nome FROM awd_categorias WHERE id = ?");
    $sql3->bindValue(1, $ex['id_cat']);
    $sql3->execute();
    $sql4 = $sql3->fetch();

    $sql5 = $conn->prepare("SELECT data_criacao FROM usuarios WHERE nick = ?");
    $sql5->bindValue(1, $ex['autor']);
    $sql5->execute();
    $sql6 = $sql5->fetch();


    if(!$ex) {
        $script_js .= register404();
    }
    ?>
    <div class="box-content">
        <div class="title-section"><?=$mdl['nome'];?></div>

        <button class="btn btn-danger" onclick="deletar(this, 1);" rel="?p=<?=$p;?>&a=3&id=<?=$id;?>">Deletar</button><br><br>

        <div class="well well-lg">
            Indicação realizada por <b><?=$ex['autor'];?></b> em <b><?=date('d/m/Y H:i:s', $ex['data']);?></b> sob o IP <b><?=$ex['ip'];?></b><br><br>
            Nick do indicado: <b><?=$ex['nick'];?></b><br>
            Categoria: <b><?=$core->clear($sql4['nome']) ;?></b>
        </div>

        <?
    //48hr = 172800s
        if($sql6['data_criacao']  > time() - 172800) {
            echo aviso_red("A conta do usuário que realizou a indicação foi criada há menos de 48 horas, caracterizando possível fake.<br>O sistema não pode confirmar se a acusação de fake procede. Verificar manualmente.");
        }

        echo '<b>Lista de usuários que entraram pela última vez na IceHabbo utilizando o mesmo IP do autor da indicação:</b><br>';

        $sql7 = $conn->prepare("SELECT nick FROM usuarios WHERE acesso_ip = ?");
        $sql7->bindValue(1, $ex['ip']);
        $sql7->execute();
        $sql8 = $sql7->fetchAll();

        foreach ($sql8 as $atual) {
            echo '- ' . $atual['nick'] . '<br>';
        }

        ?>
    </div>
    <? } ?>

    <? if($_GET['a'] == 3) {
        $id = $_GET['id'];

        $ids = explode(',', $id);
        $ids = array_filter($ids);

    /*

    $sql = $conn->prepare("SELECT autor FROM $mdl_tabela WHERE id = ?");
    $sql->bindValue(1, $id);
    $sql->execute();
    $autorVoto = $sql->fetchColumn();

    $sql2 = $conn->prepare("DELETE FROM $mdl_tabela WHERE autor = ?");
    $sql2->bindValue(1, $autorVoto);
    $sql2->execute();

    $core->logger("O usuário deletou os votos do Ice Awards do usuário [$autorVoto]", "acao");

    */

    if(count($ids) > 0) {
        $delete = $conn->prepare("DELETE FROM $mdl_tabela WHERE id = ? LIMIT 1");
        $delete->bindParam(1, $id_atual);

        foreach ($ids as $id_atual) {
            $delete->execute();

            $core->logger("O usuário deletou uma indicação do Ice Awards [#$id_atual]", "acao");
        }
    } else {
        $delete_where['id'] = $id;
        $delete = $sqlActions->delete($mdl_tabela, $delete_where);

        $core->logger("O usuário deletou uma indicação do Ice Awards [#$id]", "acao");
    }
} ?>

<? if($_GET['a'] == 4) {
    $id = $_GET['id'];

    $reset = $conn->query("ALTER TABLE $mdl_tabela AUTO_INCREMENT = 1;");
    $core->logger("O usuário resetou o AI de $mdl_tabela", "acao");

    echo "<script>location.replace('?p=$p');</script>";
} ?>

<? if($_GET['a'] == '') { ?>
<div class="box-content">
    <div class="title-section"><?=$mdl['nome'];?></div>
    <? if($core->allAccess()) { ?><a href="?p=<?=$_GET['p'];?>&a=4"><button class="btn btn-danger">Resetar AI [DEV]</button></a><? } ?>
    <button class="btn btn-info" onclick="searchShow();">Pesquisar</button>
    <? if($_POST['search'] == 'search') { ?><a href="?p=<?=$_GET['p'];?>"><button class="btn btn-warning">Limpar busca</button></a><? } ?>
    <br><br>

    <?php

    $search = getSearchForm();
    echo $search;

    ?>

    <?
    $table = new Table();
    $table->head(array('#', 'Nick', 'Categoria', 'Indicado por', 'Informações', 'Ações'));

    $table->startBody();

    $limite = 100;
    $pagina = $_GET['pag'];
    ((!$pagina)) ? $pagina = 1 : '';
    $inicio = ($pagina * $limite) - $limite;

    $query = "$mdl_tabela WHERE valido = 's' ORDER BY id DESC";

    if($_POST['search'] == 'search') {
        $busca = $core->clear($_POST['busca']);
        $limite = 5000;

        //$campo = "nick";

        $query = "$mdl_tabela WHERE autor LIKE ? OR nick LIKE ? ORDER BY id DESC";
        $sql = $conn->prepare("SELECT * FROM $query LIMIT $inicio,$limite");
        $sql->bindValue(1, '%'.$busca.'%');
        $sql->bindValue(2, '%'.$busca.'%');
        $sql->execute();

        $_rows = $conn->prepare("SELECT count(id) FROM $query");
        $_rows->bindValue(1, '%'.$busca.'%');
        $_rows->bindValue(2, '%'.$busca.'%');
        $_rows->execute();
        $total_registros = $_rows->fetchColumn();

        echo '<div class="searching">Pesquisando por: <b>'.$busca.'</b></div>';
    } else {
        $sql = $conn->query("SELECT * FROM $query LIMIT $inicio,$limite");
        $total_registros = $core->getRows("SELECT * FROM $query");
    }

    while($sql2 = $sql->fetch()) {
        $sql3 = $conn->prepare("SELECT nome FROM awd_categorias WHERE id = ?");
        $sql3->bindValue(1, $sql2['id_cat']);
        $sql3->execute();
        $sql4 = $sql3->fetch();

        $sql5 = $conn->prepare("SELECT data_criacao, acesso_ip FROM usuarios WHERE nick = ?");
        $sql5->bindValue(1, $sql2['autor']);
        $sql5->execute();
        $sql6 = $sql5->fetch();

        $sql7 = $conn->prepare("SELECT count(id) FROM usuarios WHERE acesso_ip = ?");
        $sql7->bindValue(1, $sql6['acesso_ip']);
        $sql7->execute();
        $sql8 = $sql7->fetchColumn();

        $infos = '';

        //48 horas = 172800
        if($sql6['data_criacao']  > time() - 172800 || $sql8 > 1) {
            $infos .= '<span class="label label-danger">Possível fake</span> ';
        }

        $table->insertBody(array($sql2['id'], $core->clear($sql2['nick']), $core->clear($sql4['nome']), $core->clear($sql2['autor']), $infos, 'actions'));
    }

    $table->closeTable();
    echo $table->table;

    if($total_registros == 0) {
        echo aviso_red("Nenhum registro encontrado.");
    } else {
        echo '<ul class="pagination">';

        $total_paginas = ceil($total_registros / $limite);

        $links_laterais = ceil($limite / 2);

        $inicio = $pagina - $links_laterais;
        $limite = $pagina + $links_laterais;

        for ($i = $inicio; $i <= $limite; $i++){
            if ($i == $pagina) {
                echo '<li class="active"><a href="#">'.$i.'</a></li>';
            } else {
                if ($i >= 1 && $i <= $total_paginas){
                    $link = '?' . $_SERVER["QUERY_STRING"];
                    $link = ereg_replace('&pag=(.*)', '', $link);
                    echo '<li><a href="'.$link.'&pag='.$i.'">'.$i.'</a></li>';
                }
            }
        }

        echo '</ul>';
    } ?>

    <?php

    if($total_registros > 0) {
        $marked = getMarkedSelect($p, 3);
        echo $marked;
    }

    ?>
</div>
<? } ?>