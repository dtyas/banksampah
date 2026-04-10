import { computed, ref, watch, type Ref } from "vue";

export function usePagination<T>(source: Ref<T[]>, pageSize = 10) {
    const currentPage = ref(1);

    const totalPages = computed(() => {
        const total = Math.ceil(source.value.length / pageSize);
        return Math.max(total, 1);
    });

    const pagedRows = computed(() => {
        const start = (currentPage.value - 1) * pageSize;
        const end = start + pageSize;
        return source.value.slice(start, end);
    });

    function setPage(page: number) {
        if (page < 1) {
            currentPage.value = 1;
            return;
        }
        if (page > totalPages.value) {
            currentPage.value = totalPages.value;
            return;
        }
        currentPage.value = page;
    }

    watch([source, totalPages], () => {
        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }
        if (currentPage.value < 1) {
            currentPage.value = 1;
        }
    });

    return { currentPage, totalPages, pagedRows, pageSize, setPage };
}
