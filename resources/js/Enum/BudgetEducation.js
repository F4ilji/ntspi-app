class EducationForm {
    static MAIN_QUOTA = {
        value: 1,
        label: 'Основные места',
        color: 'primary',
        name: 'MAIN_QUOTA'
    };

    static TARGET_QUOTA = {
        value: 2,
        label: 'Целевая квота',
        color: 'info',
        name: 'TARGET_QUOTA'
    };

    static SPECIAL_QUOTA = {
        value: 3,
        label: 'Особая квота',
        color: 'danger',
        name: 'SPECIAL_QUOTA'
    };

    static PAID_EDUCATION = {
        value: 4,
        label: 'С оплатой обучения',
        color: 'warning',
        name: 'PAID_EDUCATION'
    };

    static OTHER_SOURCES = {
        value: 5,
        label: 'За счёт иных средств',
        color: 'gray',
        name: 'OTHER_SOURCES'
    };

    static SEPARATE_QUOTA = {
        value: 6,
        label: 'Отдельная квота',
        color: 'success',
        name: 'SEPARATE_QUOTA'
    };

    static COMBINED_QUOTA_ALL = {
        value: 7,
        label: 'Совмещенная квота (целевая, особая и отдельная квоты)',
        color: 'indigo',
        name: 'COMBINED_QUOTA_ALL'
    };

    static COMBINED_QUOTA_TARGET_SPECIAL = {
        value: 8,
        label: 'Совмещенная квота (целевая и особая квоты)',
        color: 'violet',
        name: 'COMBINED_QUOTA_TARGET_SPECIAL'
    };

    static COMBINED_QUOTA_TARGET_SEPARATE = {
        value: 9,
        label: 'Совмещенная квота (целевая и отдельная квоты)',
        color: 'fuchsia',
        name: 'COMBINED_QUOTA_TARGET_SEPARATE'
    };

    static COMBINED_QUOTA_SPECIAL_SEPARATE = {
        value: 10,
        label: 'Совмещенная квота (особая и отдельная квоты)',
        color: 'pink',
        name: 'COMBINED_QUOTA_SPECIAL_SEPARATE'
    };

    static DETAILED_TARGET_QUOTA = {
        value: 11,
        label: 'Детализированная целевая квота',
        color: 'amber',
        name: 'DETAILED_TARGET_QUOTA'
    };

    // Метод для получения статуса по имени
    static fromName(name) {
        return this[name] || null;
    }

    static fromValue(value) {
        return Object.values(this).find(item => item.value == value) || null;
    }

    // Метод для получения всех значений
    static getAll() {
        return [
            this.MAIN_QUOTA,
            this.TARGET_QUOTA,
            this.SPECIAL_QUOTA,
            this.PAID_EDUCATION,
            this.OTHER_SOURCES,
            this.SEPARATE_QUOTA,
            this.COMBINED_QUOTA_ALL,
            this.COMBINED_QUOTA_TARGET_SPECIAL,
            this.COMBINED_QUOTA_TARGET_SEPARATE,
            this.COMBINED_QUOTA_SPECIAL_SEPARATE,
            this.DETAILED_TARGET_QUOTA
        ];
    }
}

export default EducationForm;