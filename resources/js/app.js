import './bootstrap';

document.addEventListener("DOMContentLoaded", () => {
    function togglePasswordVisibility(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = passwordInput.nextElementSibling;

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.innerHTML = "🙈"; // Cambia a icono de "cerrar ojos"
            toggleIcon.classList.add("active");
        } else {
            passwordInput.type = "password";
            toggleIcon.innerHTML = "👁️"; // Cambia a icono de "ver"
            toggleIcon.classList.remove("active");
        }
    }

    // Hacer accesible la función globalmente
    window.togglePasswordVisibility = togglePasswordVisibility;
});

document.addEventListener("DOMContentLoaded", function() {
    const colorInput = document.getElementById("color-picker");
    const randomColorBtn = document.getElementById("randomColorBtn");

    // Función para generar un color aleatorio en formato HEX
    function getRandomColor() {
        const letters = "0123456789ABCDEF";
        let color = "#";
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Cambia el color de la entrada de color cuando se hace click en el botón
    if (randomColorBtn && colorInput) {
        randomColorBtn.addEventListener("click", function() {
            const newColor = getRandomColor();
            colorInput.value = newColor; // Solo cambia el valor del input de color
        });
    }
});

document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('.icon');
        const isOpen = answer.classList.contains('open');

        // Opcional: cerrar todos primero
        document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('open'));
        document.querySelectorAll('.faq-question').forEach(b => {
            b.classList.remove('active');
            const i = b.querySelector('.icon');
            if (i) i.textContent = '+';
        });

        // Si no estaba abierto, abrirlo
        if (!isOpen) {
            answer.classList.add('open');
            button.classList.add('active');
            if (icon) icon.textContent = '–';
        }
    });
});