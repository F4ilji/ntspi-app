class EducationForm {
    static FULL_TIME = { value: 1, label: 'Очное обучение', color: 'info', name: 'FULL_TIME', type_label: 'Форма обучения' };
    static PART_TIME = { value: 2, label: 'Заочное обучение', color: 'primary', name: 'PART_TIME', type_label: 'Форма обучения' };
    static FULL_PART_TIME = { value: 3, label: 'Очно-заочное обучение', color: 'success', name: 'FULL_PART_TIME', type_label: 'Форма обучения' };

    // Метод для получения статуса по имени
    static fromName(name) {
        return this[name] || null;
    }

    static fromValue(value) {
        return Object.values(this).find(item => item.value === value) || null;
    }


    // Метод для получения имени
    getName() {
        return this.name;
    }
}

export default EducationForm;