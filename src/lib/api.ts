import axios, { AxiosInstance, AxiosResponse } from 'axios'
import { 
  ApiResponse, 
  PaginatedResponse, 
  Contract, 
  ContractTemplate, 
  User,
  ContractGenerationRequest,
  ContractFilters
} from '@/types'

class ApiService {
  private api: AxiosInstance
  private baseURL: string

  constructor() {
    this.baseURL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api'
    this.api = axios.create({
      baseURL: this.baseURL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })

    // Request interceptor for adding auth token
    this.api.interceptors.request.use(
      (config) => {
        const token = this.getAuthToken()
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
        return config
      },
      (error) => Promise.reject(error)
    )

    // Response interceptor for handling errors
    this.api.interceptors.response.use(
      (response: AxiosResponse) => response,
      (error) => {
        if (error.response?.status === 401) {
          this.clearAuthToken()
          window.location.href = '/auth/login'
        }
        return Promise.reject(error)
      }
    )
  }

  // Auth token management
  private getAuthToken(): string | null {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('auth_token')
    }
    return null
  }

  private setAuthToken(token: string): void {
    if (typeof window !== 'undefined') {
      localStorage.setItem('auth_token', token)
    }
  }

  private clearAuthToken(): void {
    if (typeof window !== 'undefined') {
      localStorage.removeItem('auth_token')
    }
  }

  // Authentication endpoints
  async login(email: string, password: string): Promise<ApiResponse<{ user: User; token: string }>> {
    try {
      const response = await this.api.post('/auth/login', { email, password })
      const { user, token } = response.data.data
      this.setAuthToken(token)
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async register(userData: { name: string; email: string; password: string; password_confirmation: string }): Promise<ApiResponse<{ user: User; token: string }>> {
    try {
      const response = await this.api.post('/auth/register', userData)
      const { user, token } = response.data.data
      this.setAuthToken(token)
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async logout(): Promise<void> {
    try {
      await this.api.post('/auth/logout')
      this.clearAuthToken()
    } catch (error: any) {
      console.error('Logout error:', error)
      this.clearAuthToken()
    }
  }

  async getCurrentUser(): Promise<ApiResponse<User>> {
    try {
      const response = await this.api.get('/auth/me')
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  // Contract endpoints
  async getContracts(filters?: ContractFilters): Promise<PaginatedResponse<Contract>> {
    try {
      const response = await this.api.get('/contracts', { params: filters })
      return response.data.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async getContract(id: string): Promise<ApiResponse<Contract>> {
    try {
      const response = await this.api.get(`/contracts/${id}`)
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async createContract(contractData: ContractGenerationRequest): Promise<ApiResponse<Contract>> {
    try {
      const response = await this.api.post('/contracts', contractData)
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async updateContract(id: string, updates: Partial<Contract>): Promise<ApiResponse<Contract>> {
    try {
      const response = await this.api.put(`/contracts/${id}`, updates)
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async deleteContract(id: string): Promise<ApiResponse<void>> {
    try {
      const response = await this.api.delete(`/contracts/${id}`)
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async generatePDF(id: string, options?: { watermark?: boolean }): Promise<Blob> {
    try {
      const response = await this.api.post(`/contracts/${id}/generate/pdf`, options, {
        responseType: 'blob'
      })
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  // Template endpoints
  async getTemplates(): Promise<ApiResponse<ContractTemplate[]>> {
    try {
      const response = await this.api.get('/templates')
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  async getTemplate(id: string): Promise<ApiResponse<ContractTemplate>> {
    try {
      const response = await this.api.get(`/templates/${id}`)
      return response.data
    } catch (error: any) {
      throw this.handleError(error)
    }
  }

  // Error handling
  private handleError(error: any): Error {
    if (error.response?.data?.message) {
      return new Error(error.response.data.message)
    }
    if (error.response?.data?.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat()
      return new Error(errorMessages.join(', '))
    }
    if (error.message) {
      return new Error(error.message)
    }
    return new Error('An unexpected error occurred')
  }

  // Utility methods
  isAuthenticated(): boolean {
    return !!this.getAuthToken()
  }

  getAuthHeaders(): Record<string, string> {
    const token = this.getAuthToken()
    return token ? { Authorization: `Bearer ${token}` } : {}
  }
}

export const apiService = new ApiService()
export default apiService
