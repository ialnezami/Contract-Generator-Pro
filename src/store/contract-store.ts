import { create } from 'zustand'
import { devtools, persist } from 'zustand/middleware'
import { Contract, ContractTemplate, User, ContractFilters } from '@/types'

interface ContractState {
  // User state
  user: User | null
  isAuthenticated: boolean
  
  // Contracts state
  contracts: Contract[]
  selectedContract: Contract | null
  contractFilters: ContractFilters
  
  // Templates state
  templates: ContractTemplate[]
  selectedTemplate: ContractTemplate | null
  
  // UI state
  isLoading: boolean
  error: string | null
  
  // Actions
  setUser: (user: User | null) => void
  setAuthenticated: (status: boolean) => void
  setContracts: (contracts: Contract[]) => void
  addContract: (contract: Contract) => void
  updateContract: (id: string, updates: Partial<Contract>) => void
  deleteContract: (id: string) => void
  setSelectedContract: (contract: Contract | null) => void
  setContractFilters: (filters: Partial<ContractFilters>) => void
  setTemplates: (templates: ContractTemplate[]) => void
  setSelectedTemplate: (template: ContractTemplate | null) => void
  setLoading: (loading: boolean) => void
  setError: (error: string | null) => void
  clearError: () => void
  reset: () => void
}

const initialState = {
  user: null,
  isAuthenticated: false,
  contracts: [],
  selectedContract: null,
  contractFilters: {
    page: 1,
    perPage: 10
  },
  templates: [],
  selectedTemplate: null,
  isLoading: false,
  error: null
}

export const useContractStore = create<ContractState>()(
  devtools(
    persist(
      (set, get) => ({
        ...initialState,
        
        setUser: (user) => set({ user }),
        
        setAuthenticated: (status) => set({ isAuthenticated: status }),
        
        setContracts: (contracts) => set({ contracts }),
        
        addContract: (contract) => set((state) => ({
          contracts: [contract, ...state.contracts]
        })),
        
        updateContract: (id, updates) => set((state) => ({
          contracts: state.contracts.map(contract =>
            contract.id === id ? { ...contract, ...updates } : contract
          ),
          selectedContract: state.selectedContract?.id === id
            ? { ...state.selectedContract, ...updates }
            : state.selectedContract
        })),
        
        deleteContract: (id) => set((state) => ({
          contracts: state.contracts.filter(contract => contract.id !== id),
          selectedContract: state.selectedContract?.id === id
            ? null
            : state.selectedContract
        })),
        
        setSelectedContract: (contract) => set({ selectedContract: contract }),
        
        setContractFilters: (filters) => set((state) => ({
          contractFilters: { ...state.contractFilters, ...filters }
        })),
        
        setTemplates: (templates) => set({ templates }),
        
        setSelectedTemplate: (template) => set({ selectedTemplate: template }),
        
        setLoading: (loading) => set({ isLoading: loading }),
        
        setError: (error) => set({ error }),
        
        clearError: () => set({ error: null }),
        
        reset: () => set(initialState)
      }),
      {
        name: 'contract-store',
        partialize: (state) => ({
          user: state.user,
          isAuthenticated: state.isAuthenticated,
          contractFilters: state.contractFilters
        })
      }
    )
  )
)
