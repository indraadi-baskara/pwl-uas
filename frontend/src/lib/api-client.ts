import { env } from '@/config/env'

type HttpMethod = 'GET' | 'POST' | 'PUT' | 'DELETE'

async function request<T>(method: HttpMethod, path: string, body?: unknown): Promise<T> {
  const res = await fetch(`${env.apiBaseUrl}${path}`, {
    method,
    credentials: 'include',
    headers: body !== undefined ? { 'Content-Type': 'application/json' } : {},
    body: body !== undefined ? JSON.stringify(body) : undefined,
  })

  if (res.status === 401) {
    const refreshed = await tryRefresh()

    if (refreshed) {
      return request<T>(method, path, body)
    }

    window.location.href = '/login'
    throw new Error('Session expired')
  }

  const json = await res.json() as { status: string; data?: T; message?: string }

  if (!res.ok || json.status === 'error') {
    throw new Error(json.message ?? `HTTP ${res.status}`)
  }

  return json.data as T
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
  get: <T>(path: string) => request<T>('GET', path),
  post: <T>(path: string, body: unknown) => request<T>('POST', path, body),
  put: <T>(path: string, body: unknown) => request<T>('PUT', path, body),
  delete: <T>(path: string) => request<T>('DELETE', path),
}
