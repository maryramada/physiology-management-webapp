<body>
    <script src="assets/css/bootstrap/bootstrap.bundle.min.js"></script>
    <header class="container-cabecalho">
        <a href="index.html">
            <img src="assets/imagens/logo_empresa.png" alt="Logo da Empresa" title="BioBalance Studio" class="logo">
        </a>
        <h3><?= APP_NAME ?></h3>
    </header>
    <nav class="container-navegacao">
        <a href="quemsomos.html">Quem Somos</a>
        <a href="servicos.html">Nossa Equipa</a>
        <a href="contactos.html">Contactos</a>
        <div class="dropdown">
            <button class="dropdown-toggle" type="button">
                <img src="assets/imagens/utilizador.png" alt="Foto do Utilizador" id="user-photo">
                <span id="username-display"></span>
            </button>
            <div class="dropdown-content">
                <a href="alterarpalavrapasse.html"><i class="fa-solid fa-key me-2"></i> Alterar Palavra-passe</a>
                <a href="clientes.html" onclick="logout()"><i class="fa-solid fa-right-from-bracket me-2"></i> Sair</a>
            </div>
        </div>
    </nav>