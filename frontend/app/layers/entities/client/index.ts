export {
  fetchClients,
  fetchClient,
  findClientByPhone,
  createClient,
  updateClient,
  deleteClient,
} from './api/client-api'
export { default as ClientFieldsInput } from './ui/ClientFieldsInput.vue'
export type { ClientItem, ClientInput } from './model/types'
