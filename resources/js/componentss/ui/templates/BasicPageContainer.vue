<template>
  <main class="flex-grow">
    <div class="relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10">
      <div class="px-0 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto w-full max-w-screen-2xl">
        <div>
          <div :class="'max-w-screen-' + breakpoint"  class="container mx-auto xl:px-5">
            <slot/>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>

export default {
	name: "BasicPageContainer",
  props: {
    breakpoint: {
      type: String,
      default: "lg"
    }
  }
}

</script>
