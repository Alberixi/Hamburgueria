document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('darkModeToggle');
    if (!toggle) return;

    // Verifica preferência do sistema
    const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    let isDark = localStorage.getItem('darkMode') === 'enabled' || 
                 (localStorage.getItem('darkMode') === null && systemPrefersDark);

    applyDarkMode(isDark);

    toggle.addEventListener('click', () => {
        isDark = !isDark;
        applyDarkMode(isDark);
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    });

    function applyDarkMode(enable) {
        document.documentElement.classList.toggle('dark', enable);
        document.cookie = `dark_mode=${enable ? 'enabled' : 'disabled'}; path=/; max-age=31536000`;
    }
});