import { createApp } from 'vue';
import LessonEditor from './components/LessonEditor.vue';
import TypingLesson from './components/TypingLesson.vue';
import { setupThemeToggle } from './theme';

setupThemeToggle();

const components = {
    'lesson-editor': LessonEditor,
    'typing-lesson': TypingLesson,
};

// Blade pages mark interactive islands with data-vue="name" and pass the
// component props as JSON in data-props.
document.querySelectorAll('[data-vue]').forEach((element) => {
    const component = components[element.dataset.vue];

    if (component) {
        createApp(component, JSON.parse(element.dataset.props || '{}')).mount(element);
    }
});
