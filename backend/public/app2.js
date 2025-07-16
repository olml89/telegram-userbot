const statusTextElem = document.getElementById('status-text');
const controlButton = document.getElementById('control-button');
const codeContainer = document.getElementById('code-container');
const codeInput = document.getElementById('code-input');
const codeSubmitButton = document.getElementById('code-submit-button');

let currentState = null; // Uno de: "Connecting", "LoggedOut", "WaitingCode", "LoggedIn"
let socket;

// Inicializar WebSocket
function initWebSocket() {
    // Ajusta la URL si es necesario: ws://host/ws o wss://...
    const wsProtocol = (window.location.protocol === 'https:') ? 'wss://' : 'ws://';
    const wsUrl = wsProtocol + window.location.host + '/ws/';
    socket = new WebSocket(wsUrl);

    // Estado inicial: Connecting
    updateUI({ type: 'Connecting', message: null });

    socket.addEventListener('open', () => {
        console.log('🟢 WebSocket conectado');
        // Opcionalmente, puedes notificar al servidor que el cliente está listo
    });

    socket.addEventListener('message', (event) => {
        let data;
        try {
            console.log(event.data);
            data = JSON.parse(event.data);
        } catch (err) {
            console.error('❌ Error al parsear mensaje WebSocket:', err, event.data);
            return;
        }
        // Esperamos objetos { type: string, message: string|null }
        if (typeof data.type !== 'string') {
            console.warn('Mensaje inesperado sin campo type:', data);
            return;
        }
        updateUI(data);
    });

    socket.addEventListener('close', () => {
        console.warn('🔴 WebSocket cerrado');
        updateUI({ type: 'Connecting', message: 'Conexión perdida, reintentando...' });
        // Intentar reconectar con delay
        setTimeout(initWebSocket, 2000);
    });

    socket.addEventListener('error', (err) => {
        console.error('🔴 Error en WebSocket:', err);
        updateUI({ type: 'Connecting', message: 'Error en conexión WebSocket' });
    });
}

// Lógica central de UI según estado
function updateUI({ type, message }) {
    currentState = type;

    // Mostrar texto de estado o mensaje
    if (message) {
        statusTextElem.textContent = message;
    } else {
        // Texto por defecto según estado
        switch (type) {
            case 'Connecting':
                statusTextElem.textContent = 'Conectando...';
                break;
            case 'LoggedOut':
                statusTextElem.textContent = 'Desconectado. Inicia sesión.';
                break;
            case 'WaitingCode':
                statusTextElem.textContent = 'Introduce el código OTP';
                break;
            case 'LoggedIn':
                statusTextElem.textContent = 'Sesión iniciada';
                break;
            case 'BotRunning':
                statusTextElem.textContent = 'Bot activo';
                break;
            case 'BotStopped':
                statusTextElem.textContent = 'Bot detenido';
                break;
            default:
                statusTextElem.textContent = '';
        }
    }

    // Ajustar clase de color (puedes adaptar nombres de clases CSS)
    // Por ejemplo: online para estados en que esté conectado al backend, offline para desconectado.
    requestAnimationFrame(() => {
        statusTextElem.classList.remove('online', 'offline', 'connecting');
        switch (type) {
            case 'Connecting':
                statusTextElem.classList.add('connecting');
                break;
            case 'LoggedOut':
                statusTextElem.classList.add('offline');
                break;
            case 'WaitingCode':
                statusTextElem.classList.add('offline');
                break;
            case 'LoggedIn':
                statusTextElem.classList.add('online');
                break;
            case 'BotRunning':
                statusTextElem.classList.add('online');
                break;
            case 'BotStopped':
                statusTextElem.classList.add('offline');
                break;
        }
    });

    // Mostrar/ocultar y configurar controlButton y codeContainer
    // Primero, ocultamos el codeContainer por defecto:
    codeContainer.style.display = 'none';
    codeInput.value = '';
    codeSubmitButton.disabled = true;

    // Resetear controlButton
    controlButton.disabled = false;
    controlButton.className = 'bot-button';
    controlButton.textContent = '';

    switch (type) {
        case 'Connecting':
            // Botón deshabilitado con texto "Connecting..."
            controlButton.disabled = true;
            controlButton.textContent = 'Conectando...';
            controlButton.classList.add('connecting');
            break;

        case 'LoggedOut':
            // Mostrar botón de "Iniciar sesión"
            controlButton.disabled = false;
            controlButton.textContent = 'Iniciar sesión';
            controlButton.classList.add('start');
            break;

        case 'WaitingCode':
            // Mostrar input y botón de envío de código
            codeContainer.style.display = 'block';
            // Deshabilitar el botón de envío hasta que haya algo en el input
            codeSubmitButton.disabled = true;
            // También deshabilitamos el controlButton principal mientras estamos en esta fase:
            controlButton.disabled = true;
            controlButton.textContent = 'Esperando código...';
            controlButton.classList.add('waiting-code');
            break;

        case 'LoggedIn':
            // Sesión iniciada, mostrar botón de "Start Bot"
            controlButton.disabled = false;
            controlButton.textContent = 'Iniciar Bot';
            controlButton.classList.add('start');
            break;

        case 'BotRunning':
            // Bot activo: mostrar botón de "Detener Bot"
            controlButton.disabled = false;
            controlButton.textContent = 'Detener Bot';
            controlButton.classList.add('stop');
            break;

        case 'BotStopped':
            // Bot detenido tras haber estado en marcha: mostrar "Iniciar Bot"
            controlButton.disabled = false;
            controlButton.textContent = 'Iniciar Bot';
            controlButton.classList.add('start');
            break;

        default:
            // Estados desconocidos: deshabilitar todo
            controlButton.disabled = true;
            controlButton.textContent = '';
    }
}

// Manejo de eventos del botón principal
controlButton.addEventListener('click', () => {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.warn('WebSocket no conectado aún');
        return;
    }
    switch (currentState) {
        case 'LoggedOut':
            // Pedir login: enviar comando al backend
            socket.send(JSON.stringify({type: 'Login'}));
            // Después de enviar, pasamos a estado Connecting o WaitingCode según flujo:
            updateUI({type: 'Connecting', message: 'Iniciando login...'});
            break;

        case 'LoggedIn':
        case 'BotStopped':
            // Iniciar bot
            socket.send(JSON.stringify({type: 'Start'}));
            updateUI({type: 'Connecting', message: 'Iniciando bot...'});
            break;

        case 'BotRunning':
            // Detener bot
            socket.send(JSON.stringify({type: 'Stop'}));
            updateUI({type: 'Connecting', message: 'Deteniendo bot...'});
            break;

        // En WaitingCode no usamos este botón, está deshabilitado
        default:
            console.warn('Acción no permitida en estado', currentState);
    }
});

// Manejo de input de código OTP
codeInput.addEventListener('input', () => {
    // Habilitar botón enviar código sólo si hay algo escrito
    codeSubmitButton.disabled = codeInput.value.trim() === '';
});

codeSubmitButton.addEventListener('click', () => {
    const code = codeInput.value.trim();
    if (!code) return;
    // Enviar código al backend
    if (socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({type: 'CompleteLogin', code: code}));
        // Opcional: deshabilitar input y botón para evitar reenvíos hasta la respuesta
        codeSubmitButton.disabled = true;
        codeInput.disabled = true;
        updateUI({type: 'Connecting', message: 'Verificando código...'});
    }
});

// Inicializar la conexión WebSocket
initWebSocket();
