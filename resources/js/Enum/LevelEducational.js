// LevelEducational.js
class LevelEducational {
    static PREPARATION_OF_QUALIFIED_WORKERS = { value: 1, label: 'Подготовка квалифицированных рабочих, служащих', color: 'info', name: 'PREPARATION_OF_QUALIFIED_WORKERS', type_label: 'Среднее образование' };
    static MIDDLE_LEVEL_SPECIALIST_TRAINING = { value: 2, label: 'Среднее профессиональное образование', color: 'primary', name: 'MIDDLE_LEVEL_SPECIALIST_TRAINING', type_label: 'Среднее образование', };
    static BACHELOR = { value: 3, label: 'Бакалавриат', color: 'success', name: 'BACHELOR', type_label: 'Высшее образование', };
    static MASTER = { value: 4, label: 'Магистратура', color: 'success', name: 'MASTER', type_label: 'Высшее образование', };
    static SPECIALIST = { value: 5, label: 'Специалитет', color: 'warning', name: 'SPECIALIST', type_label: 'Высшее образование', };
    static POSTGRADUATE = { value: 6, label: 'Аспирантура', color: 'warning', name: 'POSTGRADUATE', type_label: 'Высшее образование', };
    static ADJUNCTURE = { value: 7, label: 'Адъюнктура', color: 'secondary', name: 'ADJUNCTURE' };
    static RESIDENCY = { value: 8, label: 'Ординатура', color: 'secondary', name: 'RESIDENCY' };
    static INTERNSHIP = { value: 9, label: 'Ассистентура - стажировка', color: 'light', name: 'INTERNSHIP' };
    static PROFESSIONAL_TRAINING = { value: 10, label: 'Профессиональная подготовка по профессиям рабочих, должностям служащих', color: 'info', name: 'PROFESSIONAL_TRAINING' };
    static RETRAINING = { value: 11, label: 'Переподготовка рабочих, служащих', color: 'danger', name: 'RETRAINING' };
    static ADVANCED_TRAINING = { value: 12, label: 'Повышение квалификации рабочих, служащих', color: 'success', name: 'ADVANCED_TRAINING' };
    static ADDITIONAL_GENERAL_DEVELOPMENT_PROGRAM = { value: 13, label: 'Дополнительная общеразвивающая программа', color: 'info', name: 'ADDITIONAL_GENERAL_DEVELOPMENT_PROGRAM' };
    static ADDITIONAL_PREPROFESSIONAL_PROGRAM = { value: 14, label: 'Дополнительная предпрофессиональная программа', color: 'info', name: 'ADDITIONAL_PREPROFESSIONAL_PROGRAM' };
    static ADDITIONAL_PREPROFESSIONAL_ART_PROGRAM = { value: 15, label: 'Дополнительная предпрофессиональная программа в сфере искусств', color: 'info', name: 'ADDITIONAL_PREPROFESSIONAL_ART_PROGRAM' };
    static PROFESSIONAL_ADVANCEMENT = { value: 16, label: 'Повышение квалификации', color: 'success', name: 'PROFESSIONAL_ADVANCEMENT' };
    static PROFESSIONAL_RETRAINING = { value: 17, label: 'Профессиональная переподготовка', color: 'danger', name: 'PROFESSIONAL_RETRAINING' };
    static PRESCHOOL_EDUCATION = { value: 18, label: 'Дошкольное образование', color: 'success', name: 'PRESCHOOL_EDUCATION' };
    static PRIMARY_GENERAL_EDUCATION = { value: 19, label: 'Начальное общее образование', color: 'success', name: 'PRIMARY_GENERAL_EDUCATION' };
    static BASIC_GENERAL_EDUCATION = { value: 20, label: 'Основное общее образование', color: 'success', name:'BASIC_GENERAL_EDUCATION'};
    static SECONDARY_GENERAL_EDUCATION = { value :21 , label : "Среднее общее образование", color : "success", name : "SECONDARY_GENERAL_EDUCATION" };
    static INTERNSHIP_PROGRAM = { value :22 , label : "Интернатура", color : "light", name : "INTERNSHIP_PROGRAM" };
    static ADDITIONAL_PREPROFESSIONAL_SPORT_PROGRAM = { value :23 , label : "Дополнительная предпрофессиональная программа в сфере физической культуры и спорта", color : "info", name : "ADDITIONAL_PREPROFESSIONAL_SPORT_PROGRAM" };
    static BASIC_HIGHER_EDUCATION = { value :24 , label : "Базовое высшее образование", color : "success", name : "BASIC_HIGHER_EDUCATION" };
    static SPECIALIZED_HIGHER_EDUCATION = { value :25 , label : "Специализированное высшее образование", color : "success", name : "SPECIALIZED_HIGHER_EDUCATION" };

    // Метод для получения статуса по имени
    static fromName(name) {
        return this[name] || null;
    }

    // Метод для получения имени
    getName() {
        return this.name;
    }
}

export default LevelEducational;