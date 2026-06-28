import { env } from '@/config/env'

type HttpMethod = 'GET' | 'POST' | 'PUT' | 'DELETE'

export type Pagination = {
  total:        number
  per_page:     number
  current_page: number
  last_page:    number
}

async function doFetch(method: HttpMethod, path: string, body?: unknown): Promise<Response> {
  const isFormData = body instanceof FormData
  const res = await fetch(`${env.apiBaseUrl}${path}`, {
    method,
    credentials: 'include',
    headers: body !== undefined && !isFormData ? { 'Content-Type': 'application/json' } : {},
    body: body !== undefined ? (isFormData ? body : JSON.stringify(body)) : undefined,
  })

  if (res.status === 401) {
    const refreshed = await tryRefresh()
    if (refreshed) return doFetch(method, path, body)
    window.location.href = '/login'
    throw new Error('Session expired')
  }

  return res
}

async function request<T>(method: HttpMethod, path: string, body?: unknown): Promise<T> {
  const res  = await doFetch(method, path, body)
  const json = await res.json() as { status: string; data?: T; message?: string }

  if (!res.ok || json.status === 'error') {
    throw new Error(json.message ?? `HTTP ${res.status}`)
  }

  return json.data as T
}

async function requestPaginated<T>(path: string): Promise<{ data: T[]; pagination: Pagination }> {
  const res  = await doFetch('GET', path)
  const json = await res.json() as { status: string; data?: T[]; pagination?: Pagination; message?: string }

  if (!res.ok || json.status === 'error') {
    throw new Error(json.message ?? `HTTP ${res.status}`)
  }

  return { data: json.data ?? [], pagination: json.pagination ?? { total: 0, per_page: 10, current_page: 1, last_page: 1 } }
}

async function tryRefresh(): Promise<boolean> {
  try {
    const res = await fetch(`${env.apiBaseUrl}/auth/refresh`, {
      method: 'POST',
      credentials: 'include',
    })
    return res.ok
  } catch {
    return false
  }
}

export const apiClient = {
  get:          <T>(path: string)                => request<T>('GET', path),
  post:         <T>(path: string, body: unknown) => request<T>('POST', path, body),
  put:          <T>(path: string, body: unknown) => request<T>('PUT', path, body),
  delete:       <T>(path: string)                => request<T>('DELETE', path),
  getPaginated: <T>(path: string)                => requestPaginated<T>(path),
}
