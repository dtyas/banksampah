import { nextTick, type Ref } from "vue";

type ScrollTarget = Ref<HTMLElement | null>;
type OpenState = Ref<boolean>;

const scrollOptions: ScrollIntoViewOptions = {
  behavior: "smooth",
  block: "start",
};

export function useScrollToSection(target: ScrollTarget, openState: OpenState) {
  async function scrollToSection() {
    await nextTick();
    target.value?.scrollIntoView(scrollOptions);
  }

  async function openAndScroll() {
    openState.value = true;
    await scrollToSection();
  }

  async function toggleAndScroll() {
    openState.value = !openState.value;
    if (openState.value) {
      await scrollToSection();
    }
  }

  return {
    openAndScroll,
    scrollToSection,
    toggleAndScroll,
  };
}
