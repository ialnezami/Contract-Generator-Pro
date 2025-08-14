export interface User {
  id: string
  email: string
  name: string
  avatar?: string
  role: 'user' | 'admin' | 'enterprise'
  createdAt: Date
  updatedAt: Date
}

export interface ContractTemplate {
  id: string
  name: string
  description: string
  category: ContractCategory
  content: string
  variables: TemplateVariable[]
  isActive: boolean
  createdAt: Date
  updatedAt: Date
}

export interface Contract {
  id: string
  title: string
  templateId: string
  content: string
  variables: Record<string, any>
  status: ContractStatus
  parties: ContractParty[]
  createdBy: string
  createdAt: Date
  updatedAt: Date
  signedAt?: Date
  expiresAt?: Date
}

export interface ContractParty {
  id: string
  name: string
  email: string
  role: 'client' | 'vendor' | 'witness'
  signature?: string
  signedAt?: Date
}

export interface TemplateVariable {
  key: string
  label: string
  type: 'text' | 'number' | 'date' | 'email' | 'select' | 'textarea'
  required: boolean
  defaultValue?: any
  options?: string[]
  validation?: ValidationRule
}

export interface ValidationRule {
  minLength?: number
  maxLength?: number
  pattern?: string
  min?: number
  max?: number
}

export type ContractCategory = 
  | 'freelance'
  | 'employment'
  | 'partnership'
  | 'nda'
  | 'consulting'
  | 'software'
  | 'marketing'
  | 'real-estate'
  | 'custom'

export type ContractStatus = 
  | 'draft'
  | 'pending'
  | 'review'
  | 'approved'
  | 'signed'
  | 'expired'
  | 'cancelled'

export interface ApiResponse<T> {
  success: boolean
  data?: T
  message?: string
  errors?: Record<string, string[]>
}

export interface PaginatedResponse<T> {
  data: T[]
  pagination: {
    page: number
    perPage: number
    total: number
    totalPages: number
  }
}

export interface ContractFilters {
  status?: ContractStatus
  category?: ContractCategory
  search?: string
  dateFrom?: Date
  dateTo?: Date
  page?: number
  perPage?: number
}

export interface ContractGenerationRequest {
  templateId: string
  variables: Record<string, any>
  title: string
  parties: Omit<ContractParty, 'id' | 'signature' | 'signedAt'>[]
}

export interface ContractExportOptions {
  format: 'pdf' | 'docx' | 'html'
  includeSignatures?: boolean
  watermark?: boolean
}
