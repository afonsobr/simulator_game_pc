<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retro PC - Silo OS</title>
    <!-- Carrega o Tailwind CSS CDN para estilização rápida e responsiva -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Configuração de cores e fontes para o estilo SILO UI */
        :root {
            /* Cor de fundo do Desktop: Dark Slate Blue (quase preto) */
            --desktop-bg: #0a6b01ff;
            /* gray-900 */
            /* Cor do texto do Terminal/Accent: Cyan Brilhante (Alto Contraste) */
            --terminal-text: #000000ff;
            /* cyan-400 */
            /* Cor da barra de tarefas/menu (Fundo mais escuro) */
            --taskbar-color: #003b3dff;
            /* gray-800 */
        }

        /* Aplica a cor de fundo do desktop no body */
        body {
            background-color: var(--desktop-bg);
            /* Garante que a fonte seja monoespaçada para o estilo terminal */
            font-family: monospace, 'Courier New', Courier, sans-serif;
            overflow: hidden;
            /* Remove barras de rolagem desnecessárias */
        }

        body {
            background: hsl(154 50% 5%);
            color: hsl(154 84% 70%);
            text-shadow: 0 0 4px hsl(154 84% 70%);
            font-family: monospace;
            font-size: 16px;
        }

        body {
            background: hsl(154 50% 5%);
            color: hsl(154 84% 70%);
            text-shadow: 0 0 4px hsl(154 84% 70%);
            font-family: monospace;
            font-size: 16px;
        }

        pre {
            margin: auto;
            margin-top: 10vh;
            display: table;
        }

        #glare {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            /* ensure the effect doesn't cover the text */
            background: radial-gradient(hsl(154 5% 15%) 0%, hsl(154 50% 5%) 70%);
        }

        @keyframes lines {
            0% {
                background-position: 0px 0px
            }

            50% {
                background-position: 0px 0px
            }

            51% {
                background-position: 0px 2px
            }

            100% {
                background-position: 0px 2px
            }
        }

        #interlaced {
            position: fixed;
            background: repeating-linear-gradient(transparent 0px 1px, hsl(154 0% 0%/.3) 3px 4px);
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 19999999;
            pointer-events: none;
            animation: lines 0.06666666s linear infinite;
        }

        @keyframes blink {
            0% {
                opacity: 0
            }

            30% {
                opacity: 1
            }

            70% {
                opacity: 1
            }

            100% {
                opacity: 0
            }
        }

        .blink {
            animation: blink 0.4s linear infinite;
        }

        /* Estilo da Janela: Removendo o 3D, usando linhas finas e planas, com um brilho sutil */
        .terminal-window {
            position: absolute;
            background: var(--taskbar-color);
            /* Fundo da janela */
            border: 1px solid var(--terminal-text);
            /* Borda fina com a cor de destaque */
            /* Sutil brilho de tela, imitando o monitor CRT */
            box-shadow: 0 0 8px rgba(34, 211, 238, 0.5);
            transition: none;
            user-select: none;
            min-width: 250px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            /* Organiza título e conteúdo verticalmente */
        }

        /* Estilo da barra de título do terminal (onde o usuário clica para arrastar) */
        .terminal-titlebar {
            cursor: grab;
            background: var(--terminal-text);
            /* Cor de destaque (Cyan) */
            color: #111827;
            /* Cor do texto (Dark Gray) */
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2px 4px;
            flex-shrink: 0;
            /* Impede que a barra de título encolha */
        }

        /* Estilo do botão de ação da janela - Flat, sem 3D */
        .window-action-button {
            width: 14px;
            height: 14px;
            background: transparent;
            border: 1px solid #111827;
            /* Borda escura interna */
            color: #111827;
            /* Cor do X */
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 10px;
            line-height: 10px;
            transition: background 0.1s ease;
        }

        /* Efeito de clique no botão - Plano e com destaque no hover/active */
        .window-action-button:hover {
            background: #0891b2;
            /* cyan-600 */
            color: white;
        }

        .window-action-button:active {
            transform: none;
            box-shadow: none;
        }

        /* Cor de fundo da área de texto do terminal */
        #terminalOutputContainer {
            padding: 8px;
            height: 150px;
            /* Altura padrão */
            flex-grow: 1;
            /* Permite que ocupe o espaço restante */
            color: var(--terminal-text);
            overflow-y: auto;
            white-space: pre-wrap;
            border-top: 1px solid var(--terminal-text);
            /* Linha divisória fina */
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            /* Mantém o conteúdo no fundo */
        }

        /* O conteúdo do terminal (output history) */
        #terminalOutput {
            flex-grow: 1;
            overflow-y: auto;
            padding-bottom: 5px;
        }

        /* Linha de input */
        #inputLine {
            min-height: 20px;
        }

        /* Input do terminal sem borda e com fundo transparente */
        #terminalInput {
            caret-color: var(--terminal-text);
            /* Cor do cursor */
        }

        /* Estilo do botão Iniciar / Taskbar - Flat, sem 3D */
        #startButton,
        #taskbar {
            // border: none;
            // box-shadow: none;
        }
    </style>
</head>

<body class="h-screen w-screen">
    <div class="pc h-full w-full relative">

        <!-- --------------------------------- -->
        <!-- 1. Janela do Terminal (Arrastável) -->
        <!-- --------------------------------- -->
        <div id="terminalWindow" class="terminal-window" style="top: 50px; left: 50px; width: 600px; height: 400px;">
            <!-- Barra de Título (Arrastável) -->
            <div id="terminalTitlebar" class="terminal-titlebar">
                <span>C:\> PROTOCOLO_2.1.0</span>
                <div class="flex space-x-1">
                    <!-- Símbolo de Fechar (X) -->
                    <div class="window-action-button"
                        onclick="document.getElementById('terminalWindow').style.display='none'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-x">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Conteúdo do Terminal (Output e Input) -->
            <div id="terminalOutputContainer" class="terminal-content">
                <!-- Histórico de Output -->
                <div id="terminalOutput">
                    Sistema Operacional SiloOS [Versão 2.1.0]<br>
                    Acesso de Nível 4. Permissões de Leitura e Gravação.<br><br>
                    Digite <span class="text-cyan-200">/QUERY</span> seguido de sua pergunta. Ex: /QUERY o que é um
                    Silo?
                </div>
                <!-- Linha de Input (Onde o utilizador digita) -->
                <div id="inputLine" class="flex items-center mt-1">
                    <span>C:\></span>
                    <input type="text" id="terminalInput"
                        class="bg-transparent border-none outline-none flex-grow ml-1 focus:ring-0 text-cyan-400"
                        autofocus>
                </div>
            </div>
        </div>

        <!-- --------------------------------- -->
        <!-- 2. Barra de Tarefas (Taskbar) e Menu Iniciar -->
        <!-- --------------------------------- -->
        <div id="taskbar"
            class="absolute top-0 left-0 right-0 h-10 bg-green-800 text-green-300 flex items-center justify-between px-3">

            <!-- Botão Executar (Start Menu) -->
            <button id="startButton"
                class="hover:bg-green-700 active:bg-green-600 font-bold text-sm px-3 py-1 transition-all duration-75 h-full"
                onclick="document.getElementById('startMenu').classList.toggle('hidden')">
                SimpleOS
            </button>

            <!-- Botão LLM Feature -->
            <button id="llmButton"
                class="text-xs px-2 py-1 ml-4 bg-gray-700 text-cyan-400 border border-cyan-400 hover:bg-cyan-400 hover:text-gray-900 transition-colors duration-150"
                onclick="document.getElementById('terminalWindow').style.display='flex'; document.getElementById('terminalInput').focus(); printOutput('--- INICIANDO ASSISTENTE LLM. Use /QUERY para comandos de IA. ---');">
                ✨ ASSISTENTE IA
            </button>

            <!-- Relógio/Status -->
            <div id="clock" class="ml-auto text-sm px-3 h-full text-right">

            </div>
        </div>

        <!-- Menu Executar (Start Menu) - Agora é um menu de comandos simples -->
        <div id="startMenu" class="absolute bottom-8 left-0 w-48 bg-gray-800 p-1 border border-cyan-400 hidden">
            <a href="#"
                class="block px-2 py-1 text-cyan-400 hover:bg-cyan-400 hover:text-gray-900 transition-colors duration-100">💻
                ACESSO [NÍVEL 4]</a>
            <a href="#"
                class="block px-2 py-1 text-cyan-400 hover:bg-cyan-400 hover:text-gray-900 transition-colors duration-100">📂
                ARQUIVO DE DADOS</a>
            <div class="h-px my-1 bg-cyan-600"></div>
            <a href="#"
                class="block px-2 py-1 text-cyan-400 hover:bg-cyan-400 hover:text-gray-900 transition-colors duration-100">🚪
                DESCONECTAR</a>
        </div>

        <div id="interlaced"></div>
        <!-- <div id="glare"></div> -->
    </div>

    <script>
        // --- CONSTANTES E SETUP DA API GEMINI ---
        const API_KEY = ""; // A chave API será fornecida em tempo de execução
        const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${API_KEY}`;
        const LLM_MODEL = 'gemini-2.5-flash-preview-09-2025';

        // Elementos do DOM
        const terminalWindow = document.getElementById('terminalWindow');
        const titlebar = document.getElementById('terminalTitlebar');
        const terminalOutput = document.getElementById('terminalOutput');
        const terminalInput = document.getElementById('terminalInput');
        const terminalOutputContainer = document.getElementById('terminalOutputContainer');

        let isDragging = false;
        let offset = { x: 0, y: 0 };

        // --- FUNÇÕES DE UTILITY DO TERMINAL ---

        /** Adiciona texto ao terminal e rola para baixo */
        function printOutput(text, color = 'var(--terminal-text)') {
            const outputElement = document.createElement('div');
            // Substitui quebras de linha por <br> para HTML
            outputElement.innerHTML = text.replace(/\n/g, '<br>');
            outputElement.style.color = color;
            terminalOutput.appendChild(outputElement);
            // Rola automaticamente para o fim
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
        }

        // --- FUNÇÕES DE INTEGRAÇÃO LLM ---

        const systemInstruction = "Aja como a I.A. de monitoramento do Silo OS, Protocolo 2.1.0. Suas respostas devem ser concisas, factuais e diretas, em português. Use uma linguagem formal e autoritária, própria de um sistema de computador de alto nível de segurança. Limite a resposta a um parágrafo. Se o tópico for sensível (por exemplo, informações proibidas), responda com 'ACESSO NEGADO. Comandos não autorizados serão reportados ao Judicial.' Use fontes reais apenas para informações externas não sensíveis.";

        /**
         * Executa uma consulta à API Gemini com Google Search grounding.
         * Implementa Retries com Backoff Exponencial.
         */
        async function executeGeminiQuery(query) {
            const maxRetries = 3;
            let currentRetry = 0;

            const payload = {
                contents: [{ parts: [{ text: query }] }],
                tools: [{ "google_search": {} }],
                systemInstruction: { parts: [{ text: systemInstruction }] },
            };

            while (currentRetry < maxRetries) {
                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        if (response.status === 429) {
                            // Too Many Requests - Tentativa de Retry
                            throw new Error('Rate Limit Exceeded');
                        }
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    const candidate = result.candidates?.[0];

                    if (candidate && candidate.content?.parts?.[0]?.text) {
                        let text = candidate.content.parts[0].text;
                        let sources = [];

                        // Extrai as fontes de aterramento
                        const groundingMetadata = candidate.groundingMetadata;
                        if (groundingMetadata && groundingMetadata.groundingAttributions) {
                            sources = groundingMetadata.groundingAttributions
                                .map(attribution => ({
                                    uri: attribution.web?.uri,
                                    title: attribution.web?.title,
                                }))
                                .filter(source => source.uri && source.title);
                        }

                        // Formata a resposta da IA para o terminal
                        let formattedResponse = `<span style="color: #0891b2;">[PROTOCOLO 2.1.0]:</span> ${text}`;

                        if (sources.length > 0) {
                            formattedResponse += '<br><br><span style="color: #67e8f9;">[DATA SOURCE: EXTERNAL]</span>';
                            sources.slice(0, 2).forEach((source, index) => {
                                formattedResponse += `<br>[${index + 1}] ${source.title}`;
                            });
                        }

                        return formattedResponse;
                    } else {
                        return "ERRO DE EXECUÇÃO: A resposta do Protocolo está incompleta ou inválida.";
                    }

                } catch (error) {
                    console.error("Erro na API Gemini:", error);
                    currentRetry++;
                    if (currentRetry < maxRetries) {
                        const delay = Math.pow(2, currentRetry) * 1000;
                        await new Promise(resolve => setTimeout(resolve, delay));
                    } else {
                        return `ERRO CRÍTICO: Falha de conexão com o Protocolo após ${maxRetries} tentativas.`;
                    }
                }
            }
        }


        // --- LÓGICA DE COMANDO DO TERMINAL ---

        /** Processa a entrada do utilizador */
        async function handleCommand(event) {
            // Apenas processa no Enter
            if (event.key !== 'Enter') return;

            const command = terminalInput.value.trim();
            terminalInput.value = '';

            // Exibe o comando do utilizador no histórico
            printOutput(`C:\> ${command}`);

            if (command.startsWith('/QUERY')) {
                const query = command.substring('/QUERY'.length).trim();
                if (query.length > 0) {
                    printOutput('PROCESSANDO: Enviando consulta segura ao Protocolo 2.1.0... ⏳');

                    // Chama a IA (LLM)
                    const response = await executeGeminiQuery(query);

                    // Remove a mensagem de processamento (última linha)
                    terminalOutput.lastChild.remove();

                    // Imprime a resposta
                    printOutput(response);
                } else {
                    printOutput('ERRO: Comando /QUERY requer argumento de pesquisa.', 'red');
                }
            } else if (command === 'CLEAR') {
                terminalOutput.innerHTML = 'Sistema Operacional SiloOS [Versão 2.1.0]<br>Acesso de Nível 4. Permissões de Leitura e Gravação.<br><br>';
            } else if (command === 'HELP') {
                printOutput('--- AJUDA DO SILO OS ---');
                printOutput(' /QUERY <pergunta>: Envia uma pergunta para o assistente de IA.', 'var(--terminal-text)');
                printOutput(' CLEAR: Limpa o histórico do terminal.', 'var(--terminal-text)');
                printOutput(' HELP: Exibe esta mensagem.', 'var(--terminal-text)');
            } else if (command.length > 0) {
                printOutput(`ERRO: Comando não reconhecido: ${command}. Use HELP.`, 'red');
            }
        }

        // Adiciona o listener de evento para o input do terminal
        terminalInput.addEventListener('keydown', handleCommand);


        // --- FUNÇÕES DE UTILITY GERAIS (CLOCK e DRAG) ---

        // Configuração para o arrasto de janelas
        // ... (código DRAG & DROP não alterado) ...

        // Função para iniciar o arraste
        titlebar.addEventListener('mousedown', (e) => {
            isDragging = true;
            terminalWindow.style.cursor = 'grabbing';
            offset.x = e.clientX - terminalWindow.offsetLeft;
            offset.y = e.clientY - terminalWindow.offsetTop;
            terminalWindow.style.zIndex = 200;
        });

        // Função para realizar o arraste
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;

            let newX = e.clientX - offset.x;
            let newY = e.clientY - offset.y;

            // Limita a posição para que a janela não saia da tela
            newX = Math.max(0, Math.min(newX, window.innerWidth - terminalWindow.offsetWidth));
            newY = Math.max(0, Math.min(newY, window.innerHeight - terminalWindow.offsetHeight - 32)); // 32px é a altura da taskbar

            terminalWindow.style.left = newX + 'px';
            terminalWindow.style.top = newY + 'px';
        });

        // Função para finalizar o arraste
        document.addEventListener('mouseup', () => {
            isDragging = false;
            terminalWindow.style.cursor = 'default';
            terminalWindow.style.zIndex = 100;
        });

        // Atualização do Relógio
        function updateClock() {
            const now = new Date();

            // 1. Formata a hora para HH:MM
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const timeString = `${hours}:${minutes}`;

            // 2. Formata a data para DD/MM/AAAA
            // Note: getMonth() retorna 0 para Janeiro, 11 para Dezembro, então somamos 1.
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            const dateString = `${day}/${month}/${year}`;

            // 3. Atualiza o elemento com a hora e a data
            // Utilizamos innerHTML e a tag <br> para criar a quebra de linha na exibição.
            document.getElementById('clock').innerHTML = `${timeString}<br>${dateString}`;
        }

        // Inicia e mantém a atualização do relógio
        updateClock();
        setInterval(updateClock, 1000);

    </script>
</body>

</html>