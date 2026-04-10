<template>
  <div class="space-y-3" style="overflow: visible;">
    <div style="overflow: visible;">
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
        min_height: 400,
        autoresize_bottom_margin: 30,
        autoresize_overflow_padding: 30,
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
          'bold italic forecolor link | alignleft aligncenter ' +
          'alignright alignjustify | bullist numlist outdent indent | ' +
          'removeformat | help | fullscreen',
        content_style: 'body { font-family: Inter, -apple-system, sans-serif; font-size: 14px; }',
        setup: (editor) => {
          this.editor = editor;
          editor.on('init', () => {
            editor.setContent(this.modelValue.content || '');
            this.loading = false;
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
