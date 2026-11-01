import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type { HealthStatus } from '../model/types'

export async function fetchHealth(): Promise<HealthStatus> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<HealthStatus>>('/v1/health'))
}
