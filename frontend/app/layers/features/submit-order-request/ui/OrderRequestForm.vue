<script setup lang="ts">
import { CmsText } from '@entities/page-content'
import { cargoTypes, submitOrderRequest } from '@entities/order'
import { apiErrorMessage, apiFieldErrors } from '@shared/lib'

/**
 * The public request form («Запросить расчёт»). After a successful submit it is replaced by the message
 * from the Content elements `request.success_title` / `request.success_text` of `cmsPage`.
 */
defineProps<{ cmsPage: string }>()

const blank = () => ({ from: '', to: '', cargo_type: '', weight: '', name: '', phone: '', email: '', message: '' })
const state = reactive(blank())
const sent = ref(false)
const pending = ref(false)
const error = ref('')
const fieldErrors = ref<Record<string, string>>({})

async function onSubmit() {
  error.value = ''
  fieldErrors.value = {}
  if (state.phone.trim() === '') {
    fieldErrors.value = { phone: 'Укажите номер телефона.' }
    return
  }
  pending.value = true
  try {
    await submitOrderRequest({ ...state })
    Object.assign(state, blank())
    sent.value = true
  }
  catch (e) {
    fieldErrors.value = apiFieldErrors(e)
    error.value = Object.keys(fieldErrors.value).length > 0
      ? ''
      : apiErrorMessage(e, 'Не удалось отправить запрос. Позвоните нам или попробуйте позже.')
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <div v-if="sent" class="form" role="status" data-testid="order-request-success">
    <CmsText as="h3" :page="cmsPage" path="request.success_title" />
    <CmsText as="p" :page="cmsPage" path="request.success_text" />
  </div>
  <form v-else class="form" novalidate data-testid="order-request-form" @submit.prevent="onSubmit">
    <div class="row">
      <label>Откуда<input v-model="state.from" name="from" placeholder="Например, Шанхай" maxlength="255"></label>
      <label>Куда<input v-model="state.to" name="to" placeholder="Например, Москва" maxlength="255"></label>
    </div>
    <div class="row">
      <label>
        Тип груза
        <select v-model="state.cargo_type" name="cargo_type">
          <option value="">Не выбран</option>
          <option v-for="cargo in cargoTypes" :key="cargo" :value="cargo">{{ cargo }}</option>
        </select>
      </label>
      <label>Масса, т<input v-model="state.weight" name="weight" inputmode="decimal" maxlength="255"></label>
    </div>
    <div class="row">
      <label>Имя<input v-model="state.name" name="name" autocomplete="name" maxlength="100"></label>
      <label>
        Телефон *
        <input
          v-model="state.phone"
          name="phone"
          type="tel"
          autocomplete="tel"
          required
          :aria-invalid="fieldErrors.phone ? 'true' : undefined"
          data-testid="order-request-phone"
        >
        <span v-if="fieldErrors.phone" class="form__error" data-testid="order-request-phone-error">{{ fieldErrors.phone }}</span>
      </label>
    </div>
    <label>
      Email
      <input v-model="state.email" name="email" type="email" autocomplete="email" maxlength="255">
      <span v-if="fieldErrors.email" class="form__error">{{ fieldErrors.email }}</span>
    </label>
    <label>Комментарий<textarea v-model="state.message" name="message" rows="3" maxlength="5000" /></label>
    <p v-if="error" class="form__error" role="alert" data-testid="order-request-error">{{ error }}</p>
    <button class="btn" type="submit" :disabled="pending">Отправить заявку</button>
  </form>
</template>
