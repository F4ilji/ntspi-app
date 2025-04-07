class EducationForm {
    static BUDGET_QUANTITY_POSITION = { value: 1, label: 'Бюджетные места', color: 'info', name: 'BUDGET_QUANTITY_POSITION' };
    static NON_BUDGET_QUANTITY_POSITION = { value: 2, label: 'С оплатой обучения', color: 'primary', name: 'NON_BUDGET_QUANTITY_POSITION' };

    // Метод для получения статуса по имени
    static fromName(name) {
        return this[name] || null;
    }

    static fromValue(value) {
        return Object.values(this).find(item => item.value == value) || null;
    }


    // Метод для получения имени
    getName() {
        return this.name;
    }
}

export default EducationForm;