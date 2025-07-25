<template>
  <template v-for="file in block.data.file">
    <div class="mb-2">
      <a target="_blank" class="" :href="'/storage/' + file.path" type="button">
        <div
          class="flex border rounded-lg px-4 py-2 items-center justify-between duration-300 hover:bg-gray-100"
        >
          <div class="flex items-center justify-between">
            <div class="min-w-[30px] min-h-[30px] bg-[#303030] flex justify-center items-center rounded-md mr-3">
              <BasicIcon
                :name="file.expansion"
                class="w-5 h-5 text-white flex-shrink-0"
              />
            </div>
            <div class="flex flex-col">
              <span class="text-sm font-medium">{{ file.title }}</span>
              <div class="flex items-center gap-1">
                <span v-if="file?.time_added" class="text-xs text-gray-400">Обновлено {{ new Date(file.time_added * 1000).toLocaleDateString() }},</span>
                <span class="text-xs text-gray-400">{{ file.size }}</span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  </template>
</template>

<script>
import slugify from "slugify";
import BasicIcon from "@/componentss/ui/icons/BasicIcon.vue";

export default {
  name: "FileBlock",
  components: { BasicIcon },
  data() {
    return {
      toggler: false,
      domainPath: null,
    };
  },
  methods: {
    generateSlug: function (text) {
      return slugify(text, {
        lower: true,
        strict: true,
        locale: "ru",
      });
    },
    textLimit(text, symbols) {
      if (text.length > symbols) {
        let LimitedText;
        LimitedText = text.substring(0, symbols);
        return LimitedText + "...";
      }
      return text;
    },
  },
  mounted() {
    this.domainPath = window.location.origin;
  },
  props: {
    block: {
      type: Object,
    },
  },
};
</script>

<style scoped>
.mask-fade {
  mask-image: linear-gradient(to right, black 90%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, black 90%, transparent 100%);
}
</style>
