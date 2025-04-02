<?php

namespace App\Enums;

enum CacheKeys: string {
    case USER_PREFIX = 'person_';
    case POST_PREFIX = 'post_';
    case RECENT_POSTS_PREFIX = 'recent_posts_';
    case POSTS_PREFIX = 'posts_';
    case EDUCATION_PROGRAM_PREFIX = 'education_program_';
    case EDUCATION_PROGRAMS_PREFIX = 'education_programs_';
    case ADDITIONAL_EDUCATIONAL_PROGRAM_PREFIX = 'additional_education_program_';
    case ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX = 'additional_education_programs_';
    case ACADEMIC_JOURNAL_PREFIX = 'academic_journal_';
    case ACADEMIC_JOURNALS_PREFIX = 'academic_journals_';
    case CATEGORIES_PREFIX = 'categories_';
    case CONTACT_WIDGET_PREFIX = 'contact_widget_';
    case CONTACT_WIDGETS_PREFIX = 'contact_widgets_';
    // Department
    case DEPARTMENT_PREFIX = 'department_';
    case DEPARTMENTS_PREFIX = 'departments_';

    // Division
    case DIVISION_PREFIX = 'division_';
    case DIVISIONS_PREFIX = 'divisions_';

    // Event
    case EVENT_PREFIX = 'event_';
    case EVENTS_PREFIX = 'events_';

    // Faculty
    case FACULTY_PREFIX = 'faculty_';
    case FACULTIES_PREFIX = 'faculties_';

    // Navigation
    case NAVIGATION_PREFIX = 'navigation';

    // Page
    case PAGE_PREFIX = 'page_';
    case PAGE_DATA_PREFIX = 'page_data_';
    case PAGE_REFERENCE_LIST_PREFIX = 'page_reference_list_';
    case PAGE_REFERENCE_LISTS_PREFIX = 'page_reference_lists_';

    // Schedule
    case SCHEDULE_PREFIX = 'schedule_';
    case SCHEDULES_PREFIX = 'schedules_';

    // Slider
    case SLIDER_PREFIX = 'slider_';

    // Tag
    case TAG_IDS_PREFIX = 'tag_ids_';
    case TAGS_PREFIX = 'tags_';
    case TAG_CONTENT_PREFIX = 'tag_content_';


}
