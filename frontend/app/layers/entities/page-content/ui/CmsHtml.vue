<script lang="ts">
import { useCmsElement } from '../model/use-cms-element'

export default defineComponent({
  name: 'CmsHtml',
  props: {
    page: { type: String, required: true },
    path: { type: String, required: true },
    as: { type: String, default: 'div' },
  },
  setup(props) {
    const { element, visible, editAttrs } = useCmsElement(() => props.page, () => props.path)

    // Saved html is sanitized by the backend and the default comes from our own code, so innerHTML is safe here.
    return () => visible.value && element.value
      ? h(props.as, { ...editAttrs.value, innerHTML: element.value.value })
      : null
  },
})
</script>
