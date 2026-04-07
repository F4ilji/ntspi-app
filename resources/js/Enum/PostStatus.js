class PostStatus {
    static DRAFT = { value: 'draft', label: 'Черновик', color: 'gray', name: 'DRAFT', type_label: 'Статус публикации' };
    static PUBLISHED = { value: 'published', label: 'Опубликовано', color: 'success', name: 'PUBLISHED', type_label: 'Статус публикации' };
    static VERIFICATION = { value: 'verification', label: 'На проверке', color: 'warning', name: 'VERIFICATION', type_label: 'Статус публикации' };
    static REJECTED = { value: 'rejected', label: 'Отклонено', color: 'danger', name: 'REJECTED', type_label: 'Статус публикации' };

    // Метод для получения статуса по имени
    static fromName(name) {
        return this[name] || null;
    }

    static fromValue(value) {
        return Object.values(this).find(item => item.value == value) || null;
    }

    static getLabel(value) {
        const status = this.fromValue(value);
        return status ? status.label : value;
    }

    static getColor(value) {
        const status = this.fromValue(value);
        return status ? status.color : 'gray';
    }

    getName() {
        return this.name;
    }
}

export default PostStatus;
