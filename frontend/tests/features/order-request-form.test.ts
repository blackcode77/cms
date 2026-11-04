import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { readBody } from 'h3'
import { ContactsPage } from '@pages/contacts'

let bodies: Record<string, string>[] = []
let reply: { status: number, body: unknown } = { status: 201, body: { status: 'success', data: null } }

registerEndpoint('/api/v1/orders/request', {
  method: 'POST',
  handler: async (event) => {
    bodies.push(await readBody(event))
    event.node.res.statusCode = reply.status
    return reply.body
  },
})

async function mountForm() {
  const wrapper = await mountSuspended(ContactsPage)
  return { wrapper, form: wrapper.get('[data-testid="order-request-form"]') }
}

describe('order request form', () => {
  beforeEach(() => {
    bodies = []
    reply = { status: 201, body: { status: 'success', data: null } }
    useState('cms-content').value = {}
    useState('cms-content-loaded').value = []
  })

  it('needs only the phone and shows the processing message after sending', async () => {
    const { wrapper, form } = await mountForm()

    await form.get('input[name="phone"]').setValue('+7 999 123-45-67')
    await form.get('input[name="from"]').setValue('Шанхай')
    await form.trigger('submit')

    await vi.waitFor(() => expect(wrapper.find('[data-testid="order-request-success"]').exists()).toBe(true))
    expect(wrapper.text()).toContain('Ваш запрос в обработке, мы скоро с вами свяжемся.')
    expect(bodies).toHaveLength(1)
    expect(bodies[0]).toMatchObject({ phone: '+7 999 123-45-67', from: 'Шанхай', name: '' })
  })

  it('does not send without a phone', async () => {
    const { wrapper, form } = await mountForm()

    await form.get('input[name="name"]').setValue('Иван')
    await form.trigger('submit')

    expect(wrapper.get('[data-testid="order-request-phone-error"]').text()).toBe('Укажите номер телефона.')
    expect(bodies).toHaveLength(0)
  })

  it('shows the field error from the backend', async () => {
    reply = {
      status: 422,
      body: { status: 'failed', error_message: 'Validation failed.', error_data: { phone: ['Неверный номер.'] } },
    }
    const { wrapper, form } = await mountForm()

    await form.get('input[name="phone"]').setValue('123')
    await form.trigger('submit')

    await vi.waitFor(() => expect(wrapper.find('[data-testid="order-request-phone-error"]').text()).toBe('Неверный номер.'))
  })

  it('shows the quota message when too many requests were sent', async () => {
    reply = {
      status: 400,
      body: { status: 'failed', error_message: 'Вы уже отправили несколько запросов, мы скоро с вами свяжемся.' },
    }
    const { wrapper, form } = await mountForm()

    await form.get('input[name="phone"]').setValue('+79991234567')
    await form.trigger('submit')

    await vi.waitFor(() => expect(wrapper.find('[data-testid="order-request-error"]').text())
      .toBe('Вы уже отправили несколько запросов, мы скоро с вами свяжемся.'))
    expect(wrapper.find('[data-testid="order-request-success"]').exists()).toBe(false)
  })
})
