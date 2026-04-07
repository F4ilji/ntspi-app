import slugify from "slugify";

export const helpers = {
    methods: {
        HAS_ACTIVE_PAGE(section) {
            if (!this.$page?.props?.ziggy) return false;

            if (section.pages) {
                return section.pages.some(page => this.IS_SAME_ROUTE(page.path));
            }
            if (section.subSections) {
                return section.subSections.some(subSection => this.HAS_ACTIVE_PAGE(subSection));
            }
            return false;
        },

        IS_SAME_ROUTE(route) {
            if (!this.$page?.props?.ziggy) return false;

            const currentLocation = this.$page.props.ziggy.location;
            if (!currentLocation) return false;

            if (route === currentLocation) {
                return true;
            }

            try {
                const currentUrl = this.$page.props.ziggy.url + '/' + route;
                return currentLocation === currentUrl;
            } catch {
                return false;
            }
        },

        TEXT_LIMIT(text, symbols) {
            if (!text) return '';
            if (typeof text !== 'string') return String(text);

            return text.length > symbols
                ? text.substring(0, symbols) + "..."
                : text;
        },

        GENERATE_SLUG(text) {
            if (typeof text !== 'string') return '';

            try {
                return slugify(text, {
                    lower: true,
                    strict: true,
                    locale: 'ru'
                });
            } catch {
                return text.toLowerCase().replace(/\s+/g, '-');
            }
        },

        GET_BASE_URL() {
            if (typeof window === 'undefined') {
                return import.meta.env.VITE_APP_URL || '/';
            }
            return window.location.origin + '/';
        },

        GET_BASE_STORAGE_URL() {
            if (typeof window === 'undefined') {
                return (import.meta.env.VITE_APP_URL || '/') + '/storage/';
            }
            return window.location.origin + '/storage/';
        },

        SET_DOCUMENT_TITLE(title, subtitle = 'Dashboard') {
            if (typeof window === 'undefined') return;

            if (title) {
                document.title = `${title} | ${subtitle}`;
            } else {
                document.title = subtitle;
            }
        },

        // ==========================================
        // НОВЫЕ МЕТОДЫ (добавлены после анализа)
        // ==========================================

        /**
         * Форматирование даты с различными опциями
         * @param {string|Date} date - Дата для форматирования
         * @param {string} format - 'full' (с временем) или 'short' (без времени)
         * @returns {string} Отформатированная дата или '—'
         */
        FORMAT_DATE(date, format = 'full') {
            if (!date) return '—';
            
            const options = format === 'short'
                ? { day: 'numeric', month: 'short', year: 'numeric' }
                : { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            
            return new Date(date).toLocaleDateString('ru-RU', options);
        },

        /**
         * Форматирование размера файла в читаемый вид
         * @param {number} bytes - Размер в байтах
         * @returns {string} Отформатированный размер (Bytes, KB, MB, GB)
         */
        FORMAT_FILE_SIZE(bytes) {
            if (!bytes) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },

        /**
         * CSS классы для бейджа статуса (активен/неактивен)
         * @param {boolean} isActive - Статус активности
         * @returns {string} Tailwind CSS классы для бейджа
         */
        STATUS_BADGE_CLASS(isActive) {
            const base = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border';
            return isActive
                ? `${base} bg-success/10 text-success border-success/20`
                : `${base} bg-danger/10 text-danger border-danger/20`;
        },

        /**
         * Текстовая метка для статуса публикации
         * @param {string} status - Статус (verification, published, rejected)
         * @returns {string} Текстовая метка статуса
         */
        STATUS_LABEL(status) {
            const labels = {
                verification: 'На модерации',
                published: 'Опубликовано',
                rejected: 'Отклонено',
            };
            return labels[status] || status || '—';
        },

        /**
         * Разрешение URL для изображений (строка или File объект)
         * @param {string|File} source - URL/File объект
         * @returns {string|null} Разрешённый URL или null
         */
        RESOLVE_ASSET_URL(source) {
            if (!source) return null;

            if (typeof source === 'string') {
                // Data URL (base64) — возвращаем как есть
                if (source.startsWith('data:')) return source;
                // Absolute URL — возвращаем как есть
                if (source.startsWith('http')) return source;
                // Relative path — добавляем базовый URL хранилища
                return this.GET_BASE_STORAGE_URL() + source;
            }

            // File или Blob объект
            if (source instanceof File || source instanceof Blob) {
                return URL.createObjectURL(source);
            }

            // Неизвестный тип — логируем и возвращаем null
            console.warn('[RESOLVE_ASSET_URL] Unknown source type:', typeof source, source);
            return null;
        },

        /**
         * Валидация PDF файла
         * @param {File} file - Файл для валидации
         * @param {number} maxSizeMB - Максимальный размер в MB (по умолчанию 10)
         * @returns {string|null} Сообщение об ошибке или null
         */
        VALIDATE_PDF(file, maxSizeMB = 10) {
            if (file.type !== 'application/pdf') {
                return 'Разрешены только PDF файлы';
            }
            
            if (file.size > maxSizeMB * 1024 * 1024) {
                return `Размер файла не должен превышать ${maxSizeMB}MB`;
            }
            
            return null;
        },

        /**
         * Получение инициалов из имени (Фамилия Имя -> ФИ)
         * @param {string} name - Полное имя
         * @returns {string} Инициалы (первые 2 буквы)
         */
        GET_INITIALS(name) {
            if (!name) return '';
            
            return name
                .split(' ')
                .map(n => n[0])
                .join('')
                .toUpperCase()
                .slice(0, 2);
        },

        /**
         * Подтверждение и удаление через Inertia
         * @param {Object} entity - Объект для удаления
         * @param {string} routeName - Имя роута для удаления
         * @param {Object} options - Дополнительные опции
         */
        CONFIRM_AND_DELETE(entity, routeName, options = {}) {
            const message = options.message || `Вы уверены, что хотите удалить "${entity.title || entity.name}"?`;
            
            if (confirm(message)) {
                this.$inertia.delete(route(routeName, entity.id), {
                    preserveScroll: options.preserveScroll ?? true,
                    ...options.inertiaOptions
                });
            }
        },

        /**
         * Сброс фильтров и переход к чистой странице
         * @param {Array<string>} filterKeys - Ключи фильтров для сброса
         * @param {string} routeName - Имя роута
         */
        RESET_FILTERS(filterKeys, routeName) {
            filterKeys.forEach(key => {
                if (Object.prototype.hasOwnProperty.call(this, key)) {
                    this[key] = '';
                }
            });
            
            this.$inertia.get(route(routeName), {}, {
                preserveState: true,
                replace: true
            });
        },

        /**
         * Фильтрация через Inertia с сохранением состояния
         * @param {string} routeName - Имя роута
         * @param {Object} params - Параметры фильтрации
         */
        INERTIA_FILTER(routeName, params) {
            this.$inertia.get(route(routeName), params, {
                preserveState: true
            });
        },

        /**
         * Получение базового URL для сущности
         * @param {string} entityType - Тип сущности (faculty, division и т.д.)
         * @returns {string} Базовый URL
         */
        GET_ENTITY_BASE_URL(entityType) {
            return this.GET_BASE_URL() + entityType;
        },

        /**
         * Обработка выбора файла через input
         * @param {Event} event - Событие изменения input
         * @param {Function} callback - Callback для обработки файла
         */
        HANDLE_FILE_SELECT(event, callback) {
            const file = event.target?.files?.[0];
            if (file && callback) {
                callback(file);
            }
        },

        /**
         * Обработка drag & drop файла
         * @param {DragEvent} event - Событие drop
         * @param {Function} callback - Callback для обработки файла
         */
        HANDLE_FILE_DROP(event, callback) {
            this.isDragging = false;

            const file = event.dataTransfer?.files?.[0];
            if (file && callback) {
                callback(file);
            }
        },

        /**
         * Форматирование JSON-полей в читаемый вид
         * @param {string|Array|Object|null} value - JSON строка или объект
         * @returns {string|null} Отформатированный текст или null
         */
        FORMAT_JSON_FIELD(value) {
            if (!value) return null;

            let data = value;
            if (typeof value === 'string') {
                try {
                    data = JSON.parse(value);
                } catch {
                    return value;
                }
            }

            if (Array.isArray(data)) {
                if (data.length === 0) return null;

                const items = data.flatMap(item => {
                    if (typeof item === 'string') return item;
                    if (item && typeof item === 'object') {
                        // Образование: { year, content, institution }
                        if (item.content && item.institution) {
                            return `${item.institution} (${item.year}) — ${item.content}`;
                        }
                        // Публикации: { category_publication, publication: [{ item }] }
                        if (item.publication && Array.isArray(item.publication)) {
                            return item.publication.map(pub => pub.item || pub).filter(Boolean);
                        }
                        // Простой item: { item: "..." }
                        if (item.item) return item.item;
                        // Fallback: все значения объекта
                        return Object.values(item).join(', ');
                    }
                    return String(item);
                });
                return items.join('\n');
            }

            if (data && typeof data === 'object') {
                // Стаж работы: { total, byProf }
                if (data.total !== undefined && data.byProf !== undefined) {
                    return `Общий: ${data.total} лет, по профилю: ${data.byProf} лет`;
                }
                return Object.entries(data)
                    .map(([key, val]) => `${key}: ${val}`)
                    .join('\n');
            }

            return String(data);
        }
    }
};