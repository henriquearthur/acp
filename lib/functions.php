<?php


/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

include 'class.core.php';
include 'class.sqlactions.php';
include 'class.upload.php';

$ip = $_SERVER['REMOTE_ADDR']; // Definido no class.core
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$timestamp = time();

$form_return = '';

$_config = $conn->query("SELECT * FROM config");
$config = $_config->fetch();

if(isset($_SESSION['login'])) {
    $sql = $conn->prepare("SELECT * FROM acp_usuarios WHERE nick = ? LIMIT 1");
    $sql->bindValue(1, $_SESSION['login']);
    $sql->execute();
    $dados = $sql->fetch();

    $sql4 = $conn->prepare("SELECT * FROM usuarios WHERE nick = ? LIMIT 1");
    $sql4->bindValue(1, $dados['nick']);
    $sql4->execute();
    $dados2 = $sql4->fetch();

    $autor = $dados['nick'];

    if($dados['nick'] != $_SESSION['login'] || $dados['senha'] != $_SESSION['acp_senha']) {
        $core->deslogarPainel();
        header("Location: /acp");
    }

    if(empty($_SESSION['token'])) {
        $_SESSION['token'] = $_COOKIE['PHPSESSID'];
    }

    /**
     * Permissões de acesso - Distribuição por cargo
     */

    $cargos_user = explode('|', $dados['cargos']);
    $permissoes = array();
    $i = 0;

    foreach ($cargos_user as $cargo_atual) {
        $sql2 = $conn->query("SELECT * FROM acp_cargos WHERE id = '$cargo_atual' LIMIT 1");
        $sql3 = $sql2->fetch();

        $per_cargo = explode('|', $sql3['permissoes']);

        foreach ($per_cargo as $per_atual) {
            if($per_atual == 's') {
                $permissoes[$i] = 's';
            }

            if($per_atual == 'n' && $permissoes[$i] != 's') {
             $permissoes[$i] = 'n';
         }

         $i++;
     }

     $i = 0;
 }

    /**
     * Usuários online
     */

    $query = "SELECT * FROM acp_online WHERE id_usuario='".$dados['id']."'";
    $rows = $core->getRows($query);

    if($rows > 0) {
        $on_up_data['tempo'] = $timestamp;
        $on_up_data['url'] = $core->url_unica;
        $on_wh_data['id_usuario'] = $dados['id'];

        $sql12 = $sqlActions->update("acp_online", $on_up_data, $on_wh_data);
        unset($on_up_data);
        unset($on_wh_data);
    } else {
        $id_usuario = $dados['id'];

        if($id_usuario > 0) {
            $on_in_data['id_usuario'] = $id_usuario;
            $on_in_data['tempo'] = $timestamp;
            $on_in_data['url'] = $core->url_unica;

            $sql12 = $sqlActions->insert("acp_online", $on_in_data);
            unset($on_in_data);
        }
    }

    $horario_limite = time() - 180; // 3 min
    $sql13 = $conn->query("DELETE FROM acp_online WHERE tempo < $horario_limite");
}

/**
 * Funções
 */
function red_alert($conteudo) {return '<div class="alert alert-red">'.$conteudo.'</div>';}
function blue_alert($conteudo) {return '<div class="alert alert-blue">'.$conteudo.'</div>';}
function green_alert($conteudo) {return '<div class="alert alert-green">'.$conteudo.'</div>';}
function yellow_alert($conteudo) {return '<div class="alert alert-yellow">'.$conteudo.'</div>';}

function aviso_red($conteudo) {return '<div class="alert alert-red">'.$conteudo.'</div>';}
function aviso_blue($conteudo) {return '<div class="alert alert-blue">'.$conteudo.'</div>';}
function aviso_green($conteudo) {return '<div class="alert alert-green">'.$conteudo.'</div>';}
function aviso_yellow($conteudo) {return '<div class="alert alert-yellow">'.$conteudo.'</div>';}

function well($conteudo) {return '<div class="well">'.$conteudo.'</div>';}

function erro404() {
    $retorno  = '<div class="box-content">';
    $retorno .= '<div class="title-section">Página não encontrada</div>';
    $retorno .= aviso_red("Esta página não existe ou você não possui permissão para visualizá-la.");
    $retorno .= '</div>';

    echo $retorno;
}

function register404() {
    $retorno = <<<EOD
    $(document).ready(function() {
        $( "#notfound-dialog" ).dialog({
            title: "Registro não encontrado",
            modal: true,
            show: { effect: "fade", duration: 400 },
        });
setTimeout("history.go(-1)", 3000);
});
EOD;

return $retorno;
}

function noticia403() {
    $retorno = <<<EOD
    pgExitDisable();
    $(document).ready(function() {
        $( "#fzt-dialog" ).dialog({
            title: "Acesso negado",
            modal: true,
            show: { effect: "fade", duration: 400 },
        });
setTimeout("history.go(-1)", 2000);
});
EOD;

return $retorno;
}

function getSearchForm() {
    $retorno  = '<div class="search">';
    $retorno .= '<form action="'.$_SERVER['REMOTE_URI'].'" method="post">';

    $retorno .= '<div class="form-group">';
    $retorno .= '<label class="form-label" for="busca"><i class="icon-search"></i></label>';
    $retorno .= '<input class="form-input search-input" type="text" name="busca" id="busca" placeholder="Pesquisar...">';
    $retorno .= '<br>';
    $retorno .= '</div>';

    $retorno .= '<div class="form-group submit">';
    $retorno .= '<button type="submit" class="btn btn-info search-submit">Pesquisar</button>';
    $retorno .= '</div>';

    $retorno .= '<input type="hidden" name="search" value="search">';
    $retorno .= '</form>';
    $retorno .= '<br></div>';

    return $retorno;
}

function getMarkedSelect($p, $a) {
    $url = "?p=$p&a=$a&id=";

    $retorno  = '<br><div class="form-group marked">';

    $retorno .= '<select id="table-check-select" class="form-input" rel="'.$url.'">';
    $retorno .= '<option value="0" selected default>Com marcados...</option>';
    $retorno .= '<option value="1">- Inativar</option>';
    $retorno .= '</select>';

    $retorno .= '</div>';

    return $retorno;
}