class TypeExam {
    static EGE = {
        value: 1,
        label: 'ЕГЭ',
        color: 'primary',
        name: 'EGE'
    };

    static INTERNAL_EXAM = {
        value: 2,
        label: 'Вступительное испытание',
        color: 'info',
        name: 'INTERNAL_EXAM'
    };

    static AVG_SCORE = {
        value: 3,
        label: 'Ср. балл аттестата',
        color: 'success',
        name: 'AVG_SCORE'
    };

    static ACCREDITATION = {
        value: 4,
        label: 'Аккредитация',
        color: 'warning',
        name: 'ACCREDITATION'
    };

    static fromName(name) {
        return this[name] || null;
    }

    static fromValue(value) {
        return Object.values(this).find(item => item.value == value) || null;
    }

    static getAll() {
        return [
            this.EGE,
            this.INTERNAL_EXAM,
            this.AVG_SCORE,
            this.ACCREDITATION
        ];
    }
}

export default TypeExam;