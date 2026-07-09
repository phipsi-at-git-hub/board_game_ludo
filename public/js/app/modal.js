function openModal(title, body, actions = []) {
    const overlay =
        document.getElementById('modal-overlay');

    const titleElement =
        document.getElementById('modal-title');

    const bodyElement =
        document.getElementById('modal-body');

    const actionsElement =
        document.getElementById('modal-actions');

    titleElement.textContent = title;
    bodyElement.innerHTML = body;

    actionsElement.innerHTML = '';

    actions.forEach(action => {
        const button =
            document.createElement('button');

        button.className =
            action.className || 'btn';

        button.textContent =
            action.label;

        button.addEventListener(
            'click',
            action.onClick
        );

        actionsElement.appendChild(button);
    });

    overlay.classList.add('active');
}

function closeModal() {
    document
        .getElementById('modal-overlay')
        .classList.remove('active');
}
