const statusLabel = document.getElementById('status-label');
const statusText = document.getElementById('status-text');
const controlButton = document.getElementById('bot-control-btn');

let botIsActive = false;
let isChangingState = false;

const socket = new WebSocket('ws://' + window.location.host + '/ws/');

socket.addEventListener('open', () => {
    console.log('🟢 WebSocket conectado');
});

socket.addEventListener('message', (event) => {
    try {
        //const data = JSON.parse(event.data);
        //const isActive = data.isActive;
        console.log(event.data);

        //updateUI(isActive);
    } catch (err) {
        console.error('❌ Error al procesar mensaje WebSocket:', err);
    }
});

socket.addEventListener('close', () => {
    updateUI(false);
});

socket.addEventListener('error', (err) => {
    console.error('🔴 Error en WebSocket:', err);
    updateUI(false);
});

function updateUI(isActive) {
    botIsActive = isActive;

    const labelText = getLabelText(isActive);
    const labelColorClass = isActive ? 'online' : 'offline';
    const buttonClass = 'bot-button ' + (isActive ? 'stop' : 'start');
    const buttonText = isActive ? 'Detener' : 'Iniciar';

    const expectedTransition =
        (isChangingState && isActive && controlButton.textContent === 'Iniciando...') ||
        (isChangingState && !isActive && controlButton.textContent === 'Deteniendo...');

    if (expectedTransition) {
        isChangingState = false;

        statusText.textContent = labelText;
        requestAnimationFrame(() => {
            statusLabel.className = 'status-label ' + labelColorClass;
        });

        controlButton.disabled = false;
        controlButton.className = buttonClass;
        controlButton.textContent = buttonText;

    } else if (!isChangingState) {
        statusText.textContent = labelText;
        requestAnimationFrame(() => {
            statusLabel.className = 'status-label ' + labelColorClass;
        });

        controlButton.disabled = false;
        controlButton.className = buttonClass;
        controlButton.textContent = buttonText;

    } else {
        statusText.textContent = getLabelText(isActive);
        requestAnimationFrame(() => {
            statusLabel.className = 'status-label offline';
        });

        controlButton.disabled = true;
    }
}

function getLabelText(isActive) {
    return isActive ? 'Bot activo' : 'Bot detenido';
}

controlButton.addEventListener('click', () => {
    const command = botIsActive ? 'stop' : 'start';
    const processingText = !botIsActive ? 'Iniciando...' : 'Deteniendo...';

    isChangingState = true;
    controlButton.disabled = true;
    controlButton.textContent = processingText;

    socket.send('login'); //command);
});
