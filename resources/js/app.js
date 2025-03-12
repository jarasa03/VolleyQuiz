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