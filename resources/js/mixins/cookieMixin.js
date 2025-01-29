export default {
    methods: {
        // Устанавливаем куки
        setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000)); // Устанавливаем срок действия куки
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/"; // Устанавливаем куки
        },

        // Получаем куки
        getCookie(name) {
            const nameEQ = name + "="; // Форматируем имя куки
            const ca = document.cookie.split(';'); // Разбиваем куки по точке с запятой
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length); // Убираем пробелы
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length); // Если найдено, возвращаем значение
            }
            return null; // Если кука не найдена
        },

        // Удаляем куки
        deleteCookie(name) {
            // Получаем текущий домен
            const domain = window.location.hostname.includes('.') ? '.' + window.location.hostname : window.location.hostname;
            document.cookie = name + '=; Max-Age=-99999999; path=/; domain=' + domain; // Устанавливаем срок действия в прошлом
        },

        deleteCookiesWithPrefix(prefix) {
            const cookies = document.cookie.split(';'); // Получаем все куки
            for (let cookie of cookies) {
                const cookieName = cookie.split('=')[0].trim(); // Получаем имя куки
                if (cookieName.startsWith(prefix)) { // Проверяем, начинается ли имя куки с заданного префикса
                    this.deleteCookie(cookieName); // Удаляем куку
                }
            }
        }
    }
};
