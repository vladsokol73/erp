import React, { useEffect } from "react"
import { Button } from "@/components/ui/button"
import { X } from "lucide-react"
import { SearchableSelectField } from "@/components/ticket/fields/searchable-select-field"
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from "@/components/ui/select"

export type ResponsibleModelName = "User" | "Role" | "Permission"
export type EntityDto = { id: string | number; name?: string; title?: string }

interface ResponsibleUsersSelectorProps {
    value: Array<{
        responsible_model_name: string | null
        responsible_id: string | number | null
        responsible_title: string | null
    }>
    onChange: (
        v: Array<{
            responsible_model_name: string | null
            responsible_id: string | number | null
            responsible_title: string | null
        }>
    ) => void
    error?: string | null
    allUsers: EntityDto[]
    allRoles: EntityDto[]
    allPermissions: EntityDto[]
    addLabel: string
}

export const ResponsibleUsersSelector: React.FC<ResponsibleUsersSelectorProps> = ({
                                                                                      value = [],
                                                                                      onChange,
                                                                                      error,
                                                                                      allUsers,
                                                                                      allRoles,
                                                                                      allPermissions,
                                                                                      addLabel
                                                                                  }) => {
    const types: ResponsibleModelName[] = ["User", "Role", "Permission"]

    const defaultEntry = (): ResponsibleUsersSelectorProps["value"][number] => ({
        responsible_model_name: "User",
        responsible_id: null,
        responsible_title: null,
    })

    useEffect(() => {
        if (value.length === 0) {
            onChange([defaultEntry()])
        }
    }, [])

    const handleAdd = () => onChange([...value, defaultEntry()])

    const handleRemove = (index: number) => {
        const updated = [...value]
        updated.splice(index, 1)
        onChange(updated)
    }

    const handleTypeChange = (index: number, type: ResponsibleModelName) => {
        const updated = [...value]
        updated[index] = {
            responsible_model_name: type,
            responsible_id: null,
            responsible_title: null,
        }
        onChange(updated)
    }

    const handleIdChange = (
        index: number,
        id: string,
        list: EntityDto[],
        selectedType: ResponsibleModelName
    ) => {
        const entity = list.find((item) => item.id.toString() === id)
        if (!entity) return
        const updated = [...value]
        updated[index] = {
            responsible_model_name: selectedType,
            responsible_id: entity.id,
            responsible_title: entity.title ?? entity.name ?? null,
        }
        onChange(updated)
    }

    const listMap: Record<ResponsibleModelName, EntityDto[]> = {
        User: allUsers,
        Role: allRoles,
        Permission: allPermissions,
    }

    return (
        <div className="flex flex-col gap-3">
            {value.map((item, index) => {
                const selectedType: ResponsibleModelName =
                    types.includes(item.responsible_model_name as ResponsibleModelName)
                        ? (item.responsible_model_name as ResponsibleModelName)
                        : "User"

                const selectedId = item.responsible_id?.toString() ?? ""
                const list = listMap[selectedType]

                return (
                    <div key={index} className="flex gap-2 items-center">
                        <Select
                            value={selectedType}
                            onValueChange={(type) =>
                                handleTypeChange(index, type as ResponsibleModelName)
                            }
                        >
                            <SelectTrigger className="w-56">
                                <SelectValue placeholder="Type" />
                            </SelectTrigger>
                            <SelectContent>
                                {types.map((type) => (
                                    <SelectItem key={type} value={type}>
                                        {type}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>

                        <SearchableSelectField
                            value={selectedId ? Number(selectedId) : null}
                            onChange={(id) => handleIdChange(index, id?.toString() ?? "", list, selectedType)}
                            options={list.map((entity) => ({
                                id: Number(entity.id), 
                                label: entity.title ?? entity.name ?? "",
                            }))}
                            placeholder={`Select ${selectedType}`}
                        />

                        {value.length > 1 && (
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                onClick={() => handleRemove(index)}
                                className="text-muted-foreground hover:text-destructive"
                            >
                                <X className="w-4 h-4" />
                            </Button>
                        )}
                    </div>
                )
            })}

            <Button
                type="button"
                variant="ghost"
                onClick={handleAdd}
                className="text-sm text-muted-foreground hover:text-primary self-start flex items-center gap-1"
            >
                <span className="text-xl">＋</span> {addLabel}
            </Button>

            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    )
}
