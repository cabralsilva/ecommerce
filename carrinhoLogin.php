<?php
	session_start();

	include("class/constantes.php");
	include("actions/funcoes.php");
	include("actions/funcoesWS.php");
	require_once("class/PHPMailer-master/class.phpmailer.php");
	verificaSSL(true);
	
	$_tituloPagina = "Identificação - Carrinho de Compras";
	$_carrinho = true;
	if (@isset ($_SESSION ["CLIENTE"]) && !isset($_SESSION["PEDIDO"]["codigo"])){ //já está logado pelo painel, fazer login somente pelo ID do Cliente
		
		require_once 'actions/IntegracaoPainel.php';
		$login = new loginCarrinho();
		$login->logarCarrinhoByIdCliente($_SESSION[$_SESSION["PEDIDO"]["USUARIO"]]["codigo"]);
		die();
	}elseif (@$_SESSION["PEDIDO"]["USUARIO"]!="" && @$_SESSION["PEDIDO"]["USUARIO"]=="CADASTRADO"){ //tudo se baseia nessa variável agora
		$_chaves = array_keys($_SESSION["CART"]);
		$items = array();
		for ($_i=0; $_i<count($_chaves); $_i++){
		  $item = array(
			'codigo_pedido' => $_SESSION["PEDIDO"]["codigo"],
			'codigo_produto_grade' => $_SESSION["CART"][$_chaves[$_i]]["codigo_produto_grade"],
			'quantidade' => $_SESSION["CART"][$_chaves[$_i]]["quantidade"],
			'valor' => $_SESSION["CART"][$_chaves[$_i]]["valor"],
			'descricao' => $_SESSION["CART"][$_chaves[$_i]]["descricao"]
		  );
		  array_push($items, $item);
    	}
		$pedido = array(
			'codigoPedido' => $_SESSION["PEDIDO"]["codigo"],
			'lstItems' => $items,
			'usr' => 'pdroqtl',
			'pwd' => 'jck9com*'
		);
	   
		$json_object = json_encode($pedido);
		$return = sendWsJson($json_object, UrlWs . "updateItems");
		if ($return->codStatus != 1) {
			if($return != null){
				$_conteudo = "<strong><u>Mensagem de Log Erro do site www.plander.com.br</u></strong><br><br><br>";
				$_conteudo .= "<strong>Descrição: </strong>" . $return->msg . "<br><br>";
				$_conteudo .= "<strong>Data: </strong>" . date('d/M/y G:i:s') . "<br><br>";
				$_conteudo .= "<strong>Página Anterior: </strong>" . $_SERVER['HTTP_REFERER'] . "<br><br>";
				$_conteudo .= "<strong>Página Atual: </strong>" . $_SERVER['PHP_SELF'] . "<br><br>";
				$_conteudo .= "<strong>URL: </strong>" . $_SERVER['SERVER_NAME'] . $_SERVER ['REQUEST_URI'] . "<br><br>";
				$_conteudo .= "<strong>IP Cliente: </strong>" . $_SERVER["REMOTE_ADDR"] . "<br><br>";
				$_conteudo .= "<strong>Browser: </strong>" . getBrowser() . "<br><br>";
				$_conteudo .= "<strong>Sistema Operacional: </strong>" . php_uname() . "<br><br>";
				sendEmailLog($_conteudo);
				echo "<script> alert('Ops, tivemos uma falha no carrinho. Tente novamente.'); </script>";
			}
			echo "<script> window.location.href='" . URL . "carrinho'; </script>";
		}
		echo "<script> window.location.href='" . URL_SSL . "carrinhoPagamento'; </script>";
		die();
	}elseif (@$_SESSION["email"]!="" && @$_GET["voltar"]==""){
		echo "<script> window.location.href='" . URL_SSL . "carrinhoCadastro'; </script>";
		die();
	}elseif (count($_SESSION["CART"])==0) {
		echo "<script> alert('Carrinho vazio.'); </script>";
		echo "<script> parent.window.location.href='" . URL_SSL . "produtos'; </script>";
		die();
	}
?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="pt-br"> <!--<![endif]-->
<head>
<?php
include("includes/headerHtmlSSL.php");

/*include_once("analise/rastreamento.php"); 

include_once("analise/googleSearchConsole.php");
include_once("analise/analytics.php");*/
?>
</head>
<body itemscope itemtype="http://schema.org/WebPage">

<?php
//include_once("analise/googleTagManager.php");
?>

<!-- abre .carrinho -->
<div class="carrinho">

  <?php include("includes/cabecalhoSSL.php"); ?>

  <!-- abre .conteudo -->
  <div class="conteudo">

    <!-- abre .limites -->
    <div class="limites">

      <header>
        <h2>Identificação</h2>
        <ol class="passos">
          <li class="check"><svg width="24px" height="20px"><use xlink:href="#icone-carrinho" class="icone" /></svg> Carrinho</li>
          <li class="ativo"><svg width="20px" height="20px"><use xlink:href="#icone-usuario" class="icone" /></svg> Identificação</li>
          <li><svg width="24px" height="20px"><use xlink:href="#icone-cartoes" class="icone" /></svg> Pagamento</li>
          <li><svg width="26px" height="20px"><use xlink:href="#icone-check" class="icone" /></svg> Confirmação</li>
        </ol>
      </header>

      <form action="<?php echo URL_SSL; ?>actions/login.php?acao=novo" method="POST" class="novo-cliente">
        <fieldset>
          <h3>Sou novo cliente, esta é minha primeira compra</h3>
          <label for="email-cadastro">Insira seu endereço de e-mail:</label>
          <input type="email" name="email-cadastro" id="email-cadastro">
          <label><input type="checkbox" name="newsletter" id="newsletter" value="1"> Sim, gostaria de receber novidades da Plander em meu e-mail.</input></label>
          <button type="submit">Continuar <svg width="8px" height="15px"><use xlink:href="#icone-seta" class="icone" /></svg></a>
        </fieldset>
      </form>

      <form action="<?php echo URL_SSL; ?>actions/login.php?acao=login" method="POST" class="login">
        <fieldset>
          <h3>Já tenho cadastro</h3>
          <ol>
            <li><label for="email-login">Insira seu endereço de e-mail:</label><input type="email" name="email-login" id="email-login"></li>
            <li>
              <label for="senha-login">Senha:</label><input class="pwd" type="password" name="senha-login" id="senha-login">
              <label><input type="checkbox" name="mostrar-senha" id="mostrar-senha" aria-label="Mostrar senha como texto aberto. Aviso: esta funcionalidade exibirá a senha abertamente em sua tela"> Mostrar senha</label>
            </li>
          </ol>
          <a href="javascript:void(0);" id="esqueci" title="Esqueci minha senha">Esqueci minha senha</a>
          <button type="submit">Continuar <svg width="8px" height="15px"><use xlink:href="#icone-seta" class="icone" /></svg></a>
        </fieldset>
      </form>
	  	<div class="novo-cliente">
			<fieldset>
				<h3>Comprar sem cadastro</h3>
				<p>Realize sua compra sem criar login em nosso site.</p>
				<a href="carrinhoPagamentoSemCadastro" class="botao continuar"
					title="Realizar a compra sem cadastro">Comprar sem cadastro <svg
						width="8px" height="15px">
						<use xlink:href="#icone-seta" class="icone" /></svg></a>
			</fieldset>
		</div>
	  <a href="<?php echo URL_SSL; ?>carrinho" class="botao voltar" title="Voltar ao carrinho de compras"><svg width="8px" height="15px"><use xlink:href="#icone-seta" class="icone" /></svg> Voltar</a>
    </div>
    <!-- fecha .limites -->

  </div>
  <!-- fecha .conteudo -->

  <?php include("includes/rodapeSSL.php"); ?>

</div>
<!-- fecha .carrinho -->

<!-- abre ícones SVG -->
<svg class="hide" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><symbol viewBox="0 0 49 46" id="icone-alerta"><title>Shape</title><g id="alerta-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="alerta-Produto" transform="translate(-999 -482)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="alerta-detalhes" transform="translate(28 374)" sketch:type="MSLayerGroup"> <g id="alerta-indispon&#xED;vel" sketch:type="MSShapeGroup" transform="translate(971 108.5)"> <g id="alerta-triangle38"> <g id="alerta-Group"> <path id="alerta-Shape" d="m47.879 40.902l-21.04-39.303c-0.524-0.9823-1.552-1.599-2.671-1.599h-0.003c-1.119 0-2.144 0.61372-2.672 1.5963l-21.134 39.303c-0.50293 0.943-0.4757 2.074 0.07017 2.981 0.54886 0.91 1.5375 1.469 2.6019 1.469h42.17c1.068 0 2.053-0.556 2.602-1.469 0.549-0.91 0.577-2.041 0.076-2.978zm-23.714-1.599c-1.674 0-3.033-1.355-3.033-3.024 0-1.668 1.359-3.023 3.033-3.023 1.671 0 3.032 1.355 3.032 3.023 0 1.669-1.358 3.024-3.032 3.024zm3.035-12.045c0 1.672-1.361 3.023-3.032 3.023-1.674 0-3.033-1.351-3.033-3.023v-12.093c0-1.669 1.359-3.024 3.033-3.024 1.671 0 3.032 1.355 3.032 3.024v12.093z"/> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 23 21" id="icone-atendimento"><title>Shape</title><g id="atendimento-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="atendimento-P&#xE1;gina-Inicial" transform="translate(-589 -28)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="atendimento-topo" sketch:type="MSLayerGroup"> <g id="atendimento-nav" sketch:type="MSShapeGroup" transform="translate(28 25)"> <g id="atendimento-atendimento" transform="translate(561.5 3.1855)"> <g id="atendimento-Group"> <path id="atendimento-Shape" d="m15.675 0.014366c-2.713 0-5.052 1.4318-6.0886 3.48-0.2021-0.0291-0.4003-0.0349-0.5236-0.0349-0.448 0-1.6156 0.0785-2.7824 0.8054-0.6406 0.399-0.9399 0.9663-0.9708 1.8391-0.0001 0.0027-0.0001 0.0052-0.0001 0.008h0.0022l-0.0013 1.4644-0.0005 0.0002c-0.0104 0.003-0.0206 0.0068-0.0303 0.0111-0.1519 0.0684-0.2314 0.0376-0.2503 0.3566-0.0215 0.3634 0.0417 1.3235 0.4338 1.563 0.0586 0.0603 0.0973 0.2248 0.1205 0.4079 0.0435 0.3428 0.0926 0.7308 0.2859 0.9848 0.44 0.579 0.3244 1.475 0.3146 1.544-0.0015 0.001-0.0036 0.002-0.0065 0.004-0.2711 0.122-0.3423 0.359-0.3944 0.532l-0.0266 0.086c-0.0702 0.226-0.1366 0.44-0.228 0.643-0.0123 0.018-0.0653 0.06-0.134 0.086l-0.2003 0.076c-1.2665 0.486-2.576 0.988-3.7922 1.66-1.1497 0.636-1.3914 4.077-1.4012 4.186-0.0058296 0.066 0.016189 0.131 0.060649 0.18 0.044461 0.048 0.10728 0.076 0.17319 0.076h8.3884 8.3879c0.066 0 0.129-0.028 0.174-0.076 0.044-0.049 0.066-0.114 0.06-0.18-0.01-0.109-0.251-3.55-1.401-4.186-1.216-0.672-2.526-1.174-3.792-1.66l-0.2-0.076c-0.069-0.027-0.122-0.068-0.134-0.086-0.092-0.203-0.158-0.417-0.228-0.643l-0.027-0.086c-0.052-0.173-0.123-0.41-0.394-0.532-0.003-0.002-0.005-0.003-0.007-0.004-0.01-0.069-0.125-0.965 0.315-1.544 0.092-0.121 0.151-0.273 0.193-0.435 0.902 0.622 1.993 1.045 3.189 1.189v2.148c0 0.227 0.138 0.431 0.349 0.515 0.067 0.026 0.136 0.039 0.205 0.039 0.15 0 0.296-0.06 0.403-0.173l2.915-3.08c1.069-0.464 1.976-1.1724 2.628-2.0526 0.704-0.9484 1.076-2.0511 1.076-3.1887 0-3.2242-2.988-5.8473-6.66-5.8473v-0.000034zm2.421 10.113c-0.072 0.029-0.137 0.074-0.19 0.131l-2.038 2.152v-1.265c0-0.295-0.232-0.538-0.526-0.553-1.314-0.067-2.5-0.523-3.408-1.2312 0.258-0.36 0.301-1.106 0.283-1.4165-0.019-0.3191-0.099-0.2883-0.25-0.3566-0.01-0.0044-0.02-0.0082-0.031-0.0112v-0.0001-1.4642c-0.001-0.7452-0.181-1.2464-0.553-1.5322-0.164-0.1266-0.353-0.245-0.535-0.3596-0.1-0.0628-0.202-0.1265-0.293-0.1885 0.84-1.7072 2.819-2.9091 5.12-2.9091 3.061 0 5.551 2.1258 5.551 4.7388 0 1.8017-1.228 3.4758-3.13 4.2654z"/> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 24 24" id="icone-busca"><title>lupa</title><g id="busca-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="busca-P&#xE1;gina-Inicial" transform="translate(-926 -139)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="busca-topo" sketch:type="MSLayerGroup"> <g id="busca-busca" sketch:type="MSShapeGroup" transform="translate(388 128)"> <g id="busca-lupa" transform="translate(538 11)"> <g id="busca-Group" transform="translate(.000626)"> <path id="busca-Shape" d="m22.954 20.127l-5.414-5.498c-0.807 1.274-1.875 2.358-3.13 3.177l5.416 5.497c0.864 0.878 2.266 0.878 3.128 0 0.865-0.876 0.865-2.299 0-3.176z"/> <path id="busca-Shape" d="m17.702 8.9854c0-4.962-3.963-8.9854-8.8508-8.9854-4.8879 0-8.8512 4.0234-8.8512 8.9854 0.000048645 4.9626 3.9633 8.9856 8.8512 8.9856 4.8878 0 8.8508-4.023 8.8508-8.9856zm-8.8508 6.7386c-3.6606 0-6.6383-3.022-6.6383-6.7385 0-3.7162 2.9777-6.739 6.6383-6.739 3.6608 0 6.6388 3.0228 6.6388 6.739 0 3.7165-2.978 6.7385-6.6388 6.7385z"/> <path id="busca-Shape" d="m3.6881 8.9854h1.475c0-2.0643 1.6546-3.7439 3.688-3.7439v-1.4975c-2.8465 0-5.163 2.3517-5.163 5.2414z"/> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 24 20" id="icone-carrinho"><title>carrinho</title><g id="carrinho-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="carrinho-P&#xE1;gina-Inicial" transform="translate(-1170 -142)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="carrinho-topo" sketch:type="MSLayerGroup"> <g id="carrinho-compras" sketch:type="MSShapeGroup" transform="translate(1161 135)"> <g id="carrinho-carrinho" transform="translate(9 7)"> <path id="carrinho-Shape" d="m20.311 13.105l2.732-8.3042c0.137-0.3993 0.033-0.6393-0.077-0.7977-0.284-0.4054-0.866-0.4093-0.979-0.4093l-15.439-0.003-0.4118-1.9854c-0.1114-0.4685-0.4401-0.9154-1.1028-0.9154h-4.3389c-0.44987 0-0.6945 0.21385-0.6945 0.6408v1.1461c0 0.4131 0.24387 0.5208 0.71041 0.5208h3.6634l2.8037 12.09c-0.4453 0.479-0.6877 1.177-0.6877 1.829 0 1.434 1.124 2.755 2.5599 2.755 1.3553 0 2.3723-1.29 2.5373-2.059h5.46c0.166 0.769 0.989 2.09 2.537 2.09 1.411 0 2.558-1.241 2.558-2.673 0-1.424-0.852-2.686-2.544-2.686-0.703 0-1.538 0.384-1.926 0.961h-6.709c-0.487-0.769-1.1521-1.007-1.8247-1.034l-0.0931-0.504h10.209c0.769 0 0.921-0.285 1.057-0.662zm-0.708 2.917c0.531 0 0.962 0.437 0.962 0.976 0 0.54-0.431 0.977-0.962 0.977s-0.963-0.437-0.963-0.977c0.001-0.539 0.432-0.976 0.963-0.976zm-9.602 0.976c0 0.546-0.4363 0.99-0.9717 0.99-0.537-0.002-0.974-0.444-0.974-0.99 0-0.545 0.437-0.989 0.974-0.989 0.5354 0 0.9717 0.444 0.9717 0.989z"/> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 24 20" id="icone-cartoes"><title>credit2</title><g id="cartoes-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="cartoes-Carrinho" transform="translate(-1069 -110)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="cartoes-carrinho" transform="translate(30 110)" sketch:type="MSLayerGroup"> <g id="cartoes-passos" sketch:type="MSShapeGroup" transform="translate(777)"> <g id="cartoes-credit2-+-Pagamento" transform="translate(242)"> <g id="cartoes-credit2" transform="translate(20)"> <g id="cartoes-Group"> <path id="cartoes-Shape" d="m2.6628 8.8842c0-0.9626 0.7756-1.7456 1.729-1.7456h16.741l-1.981-6.3317c-0.185-0.5885-0.81-0.91767-1.396-0.73064l-16.955 5.4114c-0.58582 0.1871-0.90938 0.818-0.72414 1.409l2.5884 8.2643v-6.2768h-0.0025z"/> <path id="cartoes-Shape" d="m3.4038 18.999c0 0.551 0.4421 0.997 0.988 0.997h18.03c0.546 0 0.988-0.446 0.988-0.997v-1.347h-20.006l-0.0002 1.347z"/> <path id="cartoes-Shape" d="m23.41 8.8842c0-0.5511-0.442-0.9975-0.988-0.9975h-1.055-16.975c-0.5461 0-0.9882 0.4464-0.9882 0.9975v6.8958h4.9324 15.074v-6.8958z"/> </g> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 26 20" id="icone-check"><title>checked21</title><g id="check-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="check-Carrinho" transform="translate(-1201 -110)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="check-carrinho" transform="translate(30 110)" sketch:type="MSLayerGroup"> <g id="check-passos" sketch:type="MSShapeGroup" transform="translate(777)"> <g id="check-checked21-+-Confirma&#xE7;&#xE3;o-da-compr" transform="translate(371)"> <g id="check-checked21" transform="translate(23)"> <path id="check-Shape" d="m25.129 3.1647l-2.829-2.8505c-0.335-0.33734-0.878-0.33734-1.212 0.00002l-11.784 11.874-5.0118-5.0499c-0.3347-0.3374-0.8776-0.3374-1.2123 0l-2.8288 2.8505c-0.33477 0.3374-0.33477 0.8844 0.00002 1.2214l8.4468 8.512c0.1674 0.168 0.3868 0.253 0.6062 0.253s0.4388-0.085 0.6061-0.253l15.219-15.336c0.161-0.1617 0.251-0.3814 0.251-0.6105 0-0.2292-0.09-0.4488-0.251-0.6108z"/> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 22 40" id="icone-facebook"><title>facebook</title><g id="facebook-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="facebook-P&#xE1;gina-Inicial" transform="translate(-532 -2653)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="facebook-rodap&#xE9;" transform="translate(0 2625)" sketch:type="MSLayerGroup"> <g id="facebook-social" sketch:type="MSShapeGroup"> <g id="facebook-Group" transform="translate(532 28)"> <g id="facebook-facebook"> <path id="facebook-Shape" d="m20.655 0.0082474l-5.14-0.0082474c-5.7748 0-9.5068 3.8289-9.5068 9.7551v4.4979h-5.1682c-0.4466 0-0.80825 0.362-0.80825 0.808l0.000003 6.517c0 0.447 0.36206 0.808 0.80825 0.808h5.1682v16.444c0 0.447 0.3617 0.808 0.8083 0.808h6.7435c0.446 0 0.808-0.362 0.808-0.808v-16.444h6.043c0.446 0 0.808-0.361 0.808-0.808l0.002-6.517c0-0.214-0.085-0.419-0.236-0.571-0.152-0.152-0.358-0.237-0.572-0.237h-6.045v-3.813c0-1.8326 0.437-2.7629 2.824-2.7629l3.462-0.0012c0.447 0 0.808-0.3621 0.808-0.8083v-6.0511c0-0.44578-0.361-0.80743-0.807-0.80825v-0.0000026z"/> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 23 16" id="icone-frete"><title>truck</title><g id="frete-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="frete-P&#xE1;gina-Inicial" transform="translate(-156 -1209)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="frete-galeria-produtos" transform="translate(34 816)" sketch:type="MSLayerGroup"> <g id="frete-produto" sketch:type="MSShapeGroup"> <g id="frete-selo-frete" transform="translate(122 392)"> <g id="frete-truck" transform="translate(0 1)"> <path id="frete-Shape" d="m0.78598 10.649v-9.6439c0-0.53588 0.42932-0.97032 0.95892-0.97032l9.5251-0.000002c0.53 0 0.959 0.43444 0.959 0.97032v9.6439c0 0.179-0.143 0.323-0.319 0.323h-10.804c-0.17695 0-0.32002-0.144-0.32002-0.323zm7.6921 3.304c0 1.111-0.8905 2.012-1.9888 2.012-1.0984 0-1.9889-0.901-1.9889-2.012 0-1.112 0.8905-2.013 1.9889-2.013 1.0983 0 1.9888 0.901 1.9888 2.013zm-0.9945 0c0-0.556-0.4452-1.007-0.9944-1.007s-0.9944 0.451-0.9944 1.007c0 0.555 0.4452 1.006 0.9944 1.006s0.9944-0.451 0.9944-1.006zm-2.7073-2.013h-4.4567c-0.17649 0-0.3196 0.145-0.3196 0.324v0.981c0 0.179 0.14311 0.323 0.31964 0.323h3.5723c0.0932-0.647 0.4162-1.219 0.8844-1.628zm14.108 2.013c0 1.111-0.89 2.012-1.989 2.012-1.098 0-1.989-0.901-1.989-2.012 0-1.112 0.891-2.013 1.989-2.013 1.099 0 1.989 0.901 1.989 2.013zm-0.994 0c0-0.556-0.446-1.007-0.995-1.007s-0.994 0.451-0.994 1.007c0 0.555 0.445 1.006 0.994 1.006s0.995-0.451 0.995-1.006zm4.51-1.689v0.981c0 0.179-0.143 0.323-0.32 0.323h-2.587c-0.186-1.284-1.278-2.275-2.598-2.275s-2.412 0.991-2.597 2.275h-5.2113c-0.0933-0.647-0.4163-1.219-0.8845-1.628h4.9408v-9.2513c0-0.3573 0.286-0.6469 0.639-0.6469h3.018c0.85 0 1.644 0.4271 2.119 1.1396l1.946 2.9156c0.285 0.4277 0.437 0.9319 0.437 1.4479v4.3951h0.778c0.177 0 0.32 0.145 0.32 0.324zm-3.243-6.0923l-1.555-2.2368c-0.06-0.0861-0.158-0.1373-0.262-0.1373h-2.426c-0.177 0-0.32 0.1448-0.32 0.3234v2.2368c0 0.1787 0.143 0.3235 0.32 0.3235h3.982c0.259 0 0.41-0.2953 0.261-0.5096z"/> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 41 40" id="icone-instagram"><title>instagram</title><g id="instagram-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="instagram-P&#xE1;gina-Inicial" transform="translate(-614 -2653)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="instagram-rodap&#xE9;" transform="translate(0 2625)" sketch:type="MSLayerGroup"> <g id="instagram-social" sketch:type="MSShapeGroup"> <g id="instagram-Group" transform="translate(532 28)"> <g id="instagram-instagram" transform="translate(82.296)"> <path id="instagram-Shape" d="m5.1024 0h29.549c2.806 0 5.102 2.0776 5.102 5.102v29.549c0 3.025-2.296 5.102-5.102 5.102h-29.549c-2.8065 0-5.102-2.077-5.102-5.102v-29.549c0-3.0244 2.2955-5.102 5.1024-5.102zm23.856 4.4167c-0.984 0-1.789 0.8049-1.789 1.7898v4.2835c0 0.985 0.805 1.79 1.789 1.79h4.493c0.985 0 1.79-0.805 1.79-1.79v-4.2835c0-0.9849-0.805-1.7898-1.79-1.7898h-4.493zm6.302 12.394h-3.499c0.331 1.081 0.51 2.226 0.51 3.411 0 6.612-5.533 11.972-12.357 11.972-6.823 0-12.356-5.36-12.356-11.972 0.0004-1.186 0.1791-2.33 0.5106-3.411h-3.651v16.793c0 0.869 0.711 1.58 1.5804 1.58h27.682c0.87 0 1.581-0.711 1.581-1.58v-16.793h-0.001zm-15.346-4.757c-4.409 0-7.984 3.464-7.984 7.736 0 4.273 3.575 7.736 7.984 7.736s7.984-3.463 7.984-7.736c0-4.272-3.574-7.736-7.984-7.736z"/> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 24 17" id="icone-mail"><title>Group</title><g id="mail-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="mail-Produto" transform="translate(-1034 -625)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="mail-detalhes" transform="translate(28 374)" sketch:type="MSLayerGroup"> <g id="mail-mail" sketch:type="MSShapeGroup" transform="translate(1006 251)"> <g id="mail-Group"> <path id="mail-Shape" d="m12 11.9l-2.9691-2.5772-8.4892 7.2162c0.30859 0.284 0.7252 0.461 1.1846 0.461h20.548c0.457 0 0.872-0.177 1.179-0.461l-8.484-7.2162-2.969 2.5772z"/> <path id="mail-Shape" d="m23.458 0.4607c-0.308-0.2856-0.723-0.4607-1.184-0.4607h-20.548c-0.4574 0-0.87229 0.1768-1.1809 0.4641l11.455 9.7359 11.458-9.7393z"/> <path id="mail-Shape" d="m0 1.4926v14.123l8.2851-6.9817-8.2851-7.1417z"/> <path id="mail-Shape" d="m15.715 8.6343l8.285 6.9817v-14.128l-8.285 7.1463z"/> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 9 6" id="icone-seta-baixo"><title>Triangle 1 Copy</title><g id="seta-baixo-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="seta-baixo-P&#xE1;gina-Inicial" transform="translate(-1098 -160)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="seta-baixo-topo" sketch:type="MSLayerGroup"> <g id="seta-baixo-Triangle-1-Copy-+-Ol&#xE1;-Gustavo." sketch:type="MSShapeGroup" transform="translate(1004 129.5)"> <path id="seta-baixo-Triangle-1-Copy" d="m94 31h9l-4.5 5.255-4.5-5.255z"/> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 11 20" id="icone-seta"><title>Shape</title><g id="seta-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="seta-P&#xE1;gina-Inicial" transform="translate(-32 -712)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="seta-marcas" transform="translate(32 698)" sketch:type="MSLayerGroup"> <g id="seta-arrow395" sketch:type="MSShapeGroup" transform="translate(0 14)"> <path id="seta-Shape" d="m10.241 19.876l-10.196-9.9383 10.196-9.9377 0.528 0.54553-9.6347 9.3922 9.6347 9.3923-0.528 0.546z"/> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 21 21" id="icone-telefone"><title>Shape</title><g id="telefone-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="telefone-P&#xE1;gina-Inicial" transform="translate(-785 -28)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="telefone-topo" sketch:type="MSLayerGroup"> <g id="telefone-nav" sketch:type="MSShapeGroup" transform="translate(28 25)"> <g id="telefone-telefone" transform="translate(757.5 3.1855)"> <path id="telefone-Shape" d="m19.501 15.809l-3.071-3.089c-0.612-0.613-1.625-0.595-2.259 0.042l-1.547 1.557c-0.098-0.055-0.199-0.111-0.305-0.171-0.978-0.545-2.315-1.291-3.7228-2.708-1.4116-1.42-2.1545-2.7669-2.6976-3.7504-0.0573-0.1042-0.1123-0.2047-0.1665-0.3001l1.0386-1.043 0.5107-0.5142c0.6341-0.6379 0.6516-1.6567 0.0412-2.2713l-3.0717-3.0901c-0.6104-0.61377-1.6241-0.59515-2.2582 0.04278l-0.8657 0.87572 0.0236 0.0236c-0.29026 0.3725-0.53283 0.8022-0.71334 1.2655-0.1664 0.441-0.27 0.8619-0.31737 1.2836-0.4056 3.3819 1.131 6.4729 5.301 10.667 5.7641 5.797 10.409 5.359 10.61 5.337 0.436-0.052 0.855-0.157 1.28-0.323 0.456-0.179 0.883-0.423 1.253-0.714l0.019 0.017 0.877-0.864c0.633-0.638 0.651-1.657 0.041-2.273z"/> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 20 20" id="icone-usuario"><title>user168</title><g id="usuario-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="usuario-Carrinho" transform="translate(-943 -110)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="usuario-carrinho" transform="translate(30 110)" sketch:type="MSLayerGroup"> <g id="usuario-passos" sketch:type="MSShapeGroup" transform="translate(777)"> <g id="usuario-user168-+-Identifica&#xE7;&#xE3;o" transform="translate(110)"> <g id="usuario-user168" transform="translate(26)"> <g id="usuario-Group"> <path id="usuario-Shape" d="m10.004 10.598c2.465 0 4.464-2.3623 4.464-5.2765 0-4.0411-1.998-5.2765-4.464-5.2765-2.4656 0.000012-4.4642 1.2354-4.4642 5.2765 0.0001 2.9142 1.9987 5.2765 4.4642 5.2765z"/> <path id="usuario-Shape" d="m19.862 18.364l-2.252-5.123c-0.103-0.234-0.284-0.429-0.51-0.547l-3.495-1.838c-0.077-0.04-0.17-0.032-0.24 0.021-0.988 0.755-2.151 1.154-3.361 1.154-1.2105 0-2.3728-0.399-3.3614-1.154-0.0695-0.053-0.1628-0.061-0.2399-0.021l-3.4952 1.838c-0.2253 0.118-0.4061 0.312-0.5092 0.547l-2.2523 5.123c-0.15525 0.354-0.12342 0.758 0.08519 1.082 0.20852 0.324 0.56141 0.517 0.94401 0.517h17.658c0.382 0 0.735-0.193 0.944-0.517 0.208-0.324 0.24-0.728 0.085-1.082z"/> </g> </g> </g> </g> </g> </g> </g> </symbol><symbol viewBox="0 0 35 40" id="icone-youtube"><title>youtube</title><g id="youtube-Desktop" fill-rule="evenodd" sketch:type="MSPage" fill="none"> <g id="youtube-P&#xE1;gina-Inicial" transform="translate(-714 -2653)" fill="currentColor" sketch:type="MSArtboardGroup"> <g id="youtube-rodap&#xE9;" transform="translate(0 2625)" sketch:type="MSLayerGroup"> <g id="youtube-social" sketch:type="MSShapeGroup"> <g id="youtube-Group" transform="translate(532 28)"> <g id="youtube-youtube" transform="translate(182.74)"> <path id="youtube-YouTube" d="m28.417 29.259h-2.195l0.01-1.275c0-0.567 0.466-1.031 1.034-1.031h0.14c0.57 0 1.037 0.464 1.037 1.031l-0.026 1.275zm-8.233-2.733c-0.557 0-1.013 0.374-1.013 0.833v6.201c0 0.457 0.456 0.831 1.013 0.831 0.559 0 1.015-0.374 1.015-0.831v-6.201c0-0.459-0.456-0.833-1.015-0.833zm13.372-3.469v11.798c0 2.829-2.454 5.145-5.454 5.145h-22.426c-3.0004 0-5.4538-2.316-5.4538-5.145l0.00002-11.798c0-2.829 2.4534-5.145 5.4538-5.145h22.426c3 0 5.454 2.316 5.454 5.145zm-26.384 13.079l-0.0018-12.428 2.78 0.001v-1.841l-7.4106-0.011v1.81l2.3133 0.007v12.462h2.3191zm8.333-10.576h-2.317v6.636c0 0.96 0.058 1.44-0.004 1.609-0.188 0.515-1.036 1.061-1.366 0.055-0.056-0.176-0.006-0.707-0.007-1.619l-0.01-6.681h-2.3046l0.0072 6.576c0.0017 1.008-0.0227 1.76 0.008 2.102 0.0564 0.603 0.0364 1.306 0.5964 1.708 1.042 0.751 3.041-0.112 3.541-1.186l-0.005 1.371 1.862 0.002-0.001-10.573zm7.415 7.599l-0.005-5.523c-0.002-2.105-1.576-3.366-3.714-1.663l0.01-4.106-2.315 0.004-0.012 14.173 1.904-0.028 0.174-0.882c2.432 2.231 3.962 0.702 3.958-1.975zm7.254-0.732l-1.738 0.009c-0.001 0.069-0.004 0.148-0.004 0.235v0.97c0 0.519-0.429 0.942-0.95 0.942h-0.341c-0.521 0-0.95-0.423-0.95-0.942v-0.108-1.066-1.377h3.979v-1.498c0-1.094-0.028-2.188-0.118-2.814-0.285-1.98-3.064-2.294-4.468-1.281-0.44 0.317-0.776 0.74-0.972 1.309-0.197 0.57-0.296 1.347-0.296 2.335v3.294c0.001 5.474 6.651 4.701 5.858-0.008zm-8.915-17.879c0.12 0.29 0.305 0.526 0.557 0.704 0.249 0.175 0.568 0.263 0.949 0.263 0.334 0 0.63-0.091 0.888-0.277 0.257-0.185 0.473-0.462 0.65-0.831l-0.044 0.909h2.584v-10.987l-2.034-0.0001v8.5511c0 0.463-0.381 0.842-0.848 0.842-0.463 0-0.846-0.379-0.846-0.842v-8.5511h-2.123v7.4111c0 0.944 0.018 1.573 0.046 1.892 0.029 0.317 0.102 0.621 0.221 0.916zm-7.829-6.2058c0-1.0546 0.088-1.8782 0.262-2.472 0.176-0.5915 0.492-1.0671 0.95-1.4258 0.457-0.3604 1.041-0.5408 1.752-0.5408 0.598 0 1.11 0.1173 1.537 0.3471 0.43 0.2311 0.761 0.5311 0.991 0.9013 0.234 0.3716 0.394 0.7533 0.478 1.144 0.087 0.396 0.13 0.9933 0.13 1.7991v2.7789c0 1.019-0.041 1.769-0.121 2.245-0.078 0.477-0.247 0.92-0.509 1.335-0.258 0.41-0.591 0.718-0.994 0.914-0.408 0.197-0.875 0.294-1.402 0.294-0.589 0-1.085-0.081-1.494-0.251-0.41-0.169-0.727-0.423-0.953-0.762-0.228-0.338-0.388-0.751-0.485-1.232-0.097-0.48-0.144-1.203-0.144-2.166l0.002-2.9088zm2.023 4.3648c0 0.622 0.463 1.13 1.027 1.13 0.565 0 1.026-0.508 1.026-1.13v-5.8492c0-0.6214-0.461-1.1294-1.026-1.1294-0.564 0-1.027 0.508-1.027 1.1294v5.8492zm-7.1499 2.953h2.4369l0.003-8.4267 2.88-7.2186-2.666-0.000033-1.5308 5.3617-1.5529-5.3764h-2.6382l3.064 7.2373 0.004 8.4227z"/> </g> </g> </g> </g> </g> </g> </symbol></svg>
<!-- fecha ícones SVG -->

<!-- abre scripts -->
<script src="<?php echo URL_SSL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo URL_SSL; ?>js/plugins.min.js" defer></script>
<script src="<?php echo URL_SSL; ?>js/_bower.min.js" defer></script>
<script src="<?php echo URL_SSL; ?>js/main.min.js" defer></script>
<script>
	$(document).ready(function(){
		$("#esqueci").click(function(){
			if ($("#email-login").val()!="")
				window.location.href="<?php echo URL_SSL; ?>actions/login.php?acao=esqueci&email="+$("#email-login").val();
			else{
				alert("Informe o e-mail antes de prosseguir!");
				$("#email-login").focus();
			}
		});
		
	});
</script>
<!-- fecha scripts -->

</body>
</html>
