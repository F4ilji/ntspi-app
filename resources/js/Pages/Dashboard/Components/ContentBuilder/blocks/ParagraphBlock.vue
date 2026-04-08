<template>
  <div class="space-y-3">
    <div class="flex items-center gap-2 mb-2">
      <input
        type="checkbox"
        :checked="modelValue.seo_active"
        @change="update('seo_active', $event.target.checked)"
        class="h-4 w-4 text-primary focus:ring-primary border-layer-line rounded"
      />
      <label class="text-sm text-foreground">
        Активировать SEO для этого блока
      </label>
    </div>

    <div>
      <label class="block text-sm font-medium text-foreground mb-1">
        Текст <span class="text-danger">*</span>
      </label>
      <textarea
        :id="editorId"
        :value="modelValue.content"
        rows="8"
        required
        class="w-full px-3 py-2 border border-layer-line rounded-lg bg-white text-foreground placeholder-muted-foreground-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-y"
      ></textarea>
      <p v-if="loading" class="mt-1 text-xs text-primary">
        Загрузка редактора...
      </p>
    </div>
  </div>
</template>

<script>
let editorCounter = 0;

export default {
  name: 'ParagraphBlock',
  props: {
    modelValue: {
      type: Object,
      required: true
    }
  },
  emits: ['update:modelValue', 'update'],
  data() {
    return {
      editorId: `paragraph-editor-${++editorCounter}`,
      editor: null,
      loading: true
    };
  },
  mounted() {
    this.waitForTinyMCE();
  },
  beforeUnmount() {
    this.destroyEditor();
  },
  methods: {
    update(field, value) {
      this.$emit('update:modelValue', {
        ...this.modelValue,
        [field]: value
      });
      this.$emit('update');
    },
    waitForTinyMCE() {
      if (typeof tinymce !== 'undefined') {
        this.initEditor();
        return;
      }

      // Ждем загрузки TinyMCE с таймаутом
      let attempts = 0;
      const maxAttempts = 50; // 5 секунд
      const interval = setInterval(() => {
        attempts++;
        if (typeof tinymce !== 'undefined') {
          clearInterval(interval);
          this.initEditor();
        } else if (attempts >= maxAttempts) {
          clearInterval(interval);
          this.loading = false;
          console.error('TinyMCE не загрузился в течение 5 секунд');
        }
      }, 100);
    },
    initEditor() {
      this.loading = true;

      tinymce.init({
        selector: `#${this.editorId}`,
        license_key: 'gpl',
        autoresize_bottom_margin: 20,
        autoresize_overflow_padding: 20,
        max_height: 600,
        menubar: false,
        statusbar: true,
        branding: false,
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
          'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount',
          'autoresize'
        ],
        toolbar: 'undo redo | blocks | ' +
          'bold italic forecolor | alignleft aligncenter ' +
          'alignright alignjustify | bullist numlist outdent indent | ' +
          'removeformat | help',
        content_style: 'body { font-family: Inter, -apple-system, sans-serif; font-size: 14px; }',
        setup: (editor) => {
          this.editor = editor;
          editor.on('init', () => {
            editor.setContent(this.modelValue.content || '');
            this.loading = false;
            // Emit initial content to ensure parent has correct value
            this.update('content', editor.getContent());
          });
          editor.on('change', () => {
            this.update('content', editor.getContent());
          });
          editor.on('Remove', () => {
            this.editor = null;
          });
        }
      });
    },
    destroyEditor() {
      if (this.editor && typeof tinymce !== 'undefined') {
        tinymce.remove(this.editor);
        this.editor = null;
      }
    }
  }
}
</script>
