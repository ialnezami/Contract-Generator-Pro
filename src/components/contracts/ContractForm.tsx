'use client'

import { useState, useEffect } from 'react'
import { useForm, useFieldArray } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useContractStore } from '@/store/contract-store'
import { apiService } from '@/lib/api'
import { 
  FileText, 
  Plus, 
  Trash2, 
  Save, 
  Eye,
  Download,
  Loader2,
  User,
  Mail,
  Building
} from 'lucide-react'
import { ContractTemplate, ContractParty, ContractGenerationRequest } from '@/types'

const contractSchema = z.object({
  title: z.string().min(1, 'Contract title is required'),
  templateId: z.string().min(1, 'Please select a template'),
  variables: z.record(z.any()),
  parties: z.array(z.object({
    name: z.string().min(1, 'Party name is required'),
    email: z.string().email('Valid email is required'),
    role: z.enum(['client', 'vendor', 'witness']),
    company: z.string().optional()
  })).min(1, 'At least one party is required')
})

type ContractFormData = z.infer<typeof contractSchema>

export default function ContractForm() {
  const [templates, setTemplates] = useState<ContractTemplate[]>([])
  const [selectedTemplate, setSelectedTemplate] = useState<ContractTemplate | null>(null)
  const [isLoading, setIsLoading] = useState(false)
  const [isGenerating, setIsGenerating] = useState(false)
  const { addContract, setError } = useContractStore()

  const {
    register,
    handleSubmit,
    control,
    watch,
    setValue,
    formState: { errors },
    reset
  } = useForm<ContractFormData>({
    resolver: zodResolver(contractSchema),
    defaultValues: {
      title: '',
      templateId: '',
      variables: {},
      parties: [
        { name: '', email: '', role: 'client', company: '' }
      ]
    }
  })

  const { fields, append, remove } = useFieldArray({
    control,
    name: 'parties'
  })

  const watchedTemplateId = watch('templateId')

  useEffect(() => {
    loadTemplates()
  }, [])

  useEffect(() => {
    if (watchedTemplateId) {
      const template = templates.find(t => t.id === watchedTemplateId)
      if (template) {
        setSelectedTemplate(template)
        // Initialize variables with default values
        const initialVariables: Record<string, any> = {}
        template.variables.forEach(variable => {
          initialVariables[variable.key] = variable.defaultValue || ''
        })
        setValue('variables', initialVariables)
      }
    }
  }, [watchedTemplateId, templates, setValue])

  const loadTemplates = async () => {
    try {
      const response = await apiService.getTemplates()
      if (response.success && response.data) {
        setTemplates(response.data)
      }
    } catch (error: any) {
      setError(error.message)
    }
  }

  const onSubmit = async (data: ContractFormData) => {
    setIsLoading(true)
    try {
      const contractData: ContractGenerationRequest = {
        templateId: data.templateId,
        variables: data.variables,
        title: data.title,
        parties: data.parties
      }

      const response = await apiService.createContract(contractData)
      
      if (response.success && response.data) {
        addContract(response.data)
        reset()
        // Redirect to the new contract or show success message
        alert('Contract created successfully!')
      }
    } catch (error: any) {
      setError(error.message)
    } finally {
      setIsLoading(false)
    }
  }

  const addParty = () => {
    append({ name: '', email: '', role: 'client', company: '' })
  }

  const removeParty = (index: number) => {
    if (fields.length > 1) {
      remove(index)
    }
  }

  const generatePreview = async () => {
    if (!selectedTemplate) return
    
    setIsGenerating(true)
    try {
      // This would call the preview endpoint
      console.log('Generating preview...')
    } catch (error: any) {
      setError(error.message)
    } finally {
      setIsGenerating(false)
    }
  }

  const downloadTemplate = async () => {
    if (!selectedTemplate) return
    
    try {
      // This would download the template as a sample
      console.log('Downloading template...')
    } catch (error: any) {
      setError(error.message)
    }
  }

  return (
    <div className="max-w-4xl mx-auto p-6 space-y-6">
      <div className="text-center">
        <h1 className="text-3xl font-bold text-gray-900">Create New Contract</h1>
        <p className="text-gray-600 mt-2">
          Fill in the details below to generate your contract
        </p>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* Basic Information */}
        <Card>
          <CardHeader>
            <CardTitle>Basic Information</CardTitle>
            <CardDescription>
              Provide the basic details for your contract
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <Label htmlFor="title">Contract Title</Label>
              <Input
                id="title"
                placeholder="Enter contract title"
                {...register('title')}
                className={errors.title ? 'border-red-500' : ''}
              />
              {errors.title && (
                <p className="mt-1 text-sm text-red-600">{errors.title.message}</p>
              )}
            </div>

            <div>
              <Label htmlFor="templateId">Contract Template</Label>
              <select
                {...register('templateId')}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Select a template</option>
                {templates.map((template) => (
                  <option key={template.id} value={template.id}>
                    {template.name} - {template.category}
                  </option>
                ))}
              </select>
              {errors.templateId && (
                <p className="mt-1 text-sm text-red-600">{errors.templateId.message}</p>
              )}
            </div>

            {selectedTemplate && (
              <div className="p-4 bg-blue-50 rounded-lg">
                <h4 className="font-medium text-blue-900 mb-2">Template: {selectedTemplate.name}</h4>
                <p className="text-blue-700 text-sm mb-3">{selectedTemplate.description}</p>
                <div className="flex gap-2">
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={generatePreview}
                    disabled={isGenerating}
                  >
                    {isGenerating ? (
                      <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                    ) : (
                      <Eye className="w-4 h-4 mr-2" />
                    )}
                    Preview
                  </Button>
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={downloadTemplate}
                  >
                    <Download className="w-4 h-4 mr-2" />
                    Download Sample
                  </Button>
                </div>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Template Variables */}
        {selectedTemplate && selectedTemplate.variables.length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle>Contract Details</CardTitle>
              <CardDescription>
                Fill in the specific details for your contract
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {selectedTemplate.variables.map((variable) => (
                  <div key={variable.key}>
                    <Label htmlFor={variable.key}>
                      {variable.label}
                      {variable.required && <span className="text-red-500 ml-1">*</span>}
                    </Label>
                    {variable.type === 'textarea' ? (
                      <textarea
                        {...register(`variables.${variable.key}`)}
                        placeholder={`Enter ${variable.label.toLowerCase()}`}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        rows={3}
                      />
                    ) : variable.type === 'select' && variable.options ? (
                      <select
                        {...register(`variables.${variable.key}`)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                        <option value="">Select {variable.label.toLowerCase()}</option>
                        {variable.options.map((option) => (
                          <option key={option} value={option}>
                            {option}
                          </option>
                        ))}
                      </select>
                    ) : (
                      <Input
                        type={variable.type === 'date' ? 'date' : 'text'}
                        placeholder={`Enter ${variable.label.toLowerCase()}`}
                        {...register(`variables.${variable.key}`)}
                      />
                    )}
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}

        {/* Contract Parties */}
        <Card>
          <CardHeader>
            <CardTitle>Contract Parties</CardTitle>
            <CardDescription>
              Add the parties involved in this contract
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {fields.map((field, index) => (
                <div key={field.id} className="p-4 border border-gray-200 rounded-lg">
                  <div className="flex items-center justify-between mb-3">
                    <h4 className="font-medium text-gray-900">Party {index + 1}</h4>
                    {fields.length > 1 && (
                      <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() => removeParty(index)}
                      >
                        <Trash2 className="w-4 h-4" />
                      </Button>
                    )}
                  </div>
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor={`parties.${index}.name`}>Name *</Label>
                      <Input
                        placeholder="Enter party name"
                        {...register(`parties.${index}.name`)}
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor={`parties.${index}.email`}>Email *</Label>
                      <Input
                        type="email"
                        placeholder="Enter party email"
                        {...register(`parties.${index}.email`)}
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor={`parties.${index}.role`}>Role *</Label>
                      <select
                        {...register(`parties.${index}.role`)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                        <option value="client">Client</option>
                        <option value="vendor">Vendor</option>
                        <option value="witness">Witness</option>
                      </select>
                    </div>
                    
                    <div>
                      <Label htmlFor={`parties.${index}.company`}>Company</Label>
                      <Input
                        placeholder="Enter company name (optional)"
                        {...register(`parties.${index}.company`)}
                      />
                    </div>
                  </div>
                </div>
              ))}
              
              <Button
                type="button"
                variant="outline"
                onClick={addParty}
                className="w-full"
              >
                <Plus className="w-4 h-4 mr-2" />
                Add Another Party
              </Button>
            </div>
            
            {errors.parties && (
              <p className="mt-2 text-sm text-red-600">{errors.parties.message}</p>
            )}
          </CardContent>
        </Card>

        {/* Form Actions */}
        <div className="flex justify-end gap-4">
          <Button type="button" variant="outline">
            Save as Draft
          </Button>
          <Button type="submit" disabled={isLoading}>
            {isLoading ? (
              <>
                <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                Creating Contract...
              </>
            ) : (
              <>
                <FileText className="w-4 h-4 mr-2" />
                Create Contract
              </>
            )}
          </Button>
        </div>
      </form>
    </div>
  )
}
