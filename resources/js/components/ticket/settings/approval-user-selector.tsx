import React from "react"
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from "@/components/ui/select"
import { Button } from "@/components/ui/button"
import { X } from "lucide-react"

export type EntityDto = { id: string | number; name?: string; title?: string }

interface ApprovalUserSelectorProps {
    value: {
        responsible_model_name: string | null
        responsible_id: string | number | null
        responsible_title: string | null
    } | null
    onChange: (
        v: {
            responsible_model_name: string | null
            responsible_id: string | number | null
            responsible_title: string | null
        } | null
    ) => void
    error?: string | null
    allUsers: EntityDto[]
    allRoles: EntityDto[]
    allPermissions: EntityDto[]
}

const types = ["User", "Role", "Permission"] as const
type ResponsibleModelName = typeof types[number]

export const ApprovalUserSelector: React.FC<ApprovalUserSelectorProps> = ({
                                                                              value,
                                                                              onChange,
                                                                              error,
                                                                              allUsers,
                                                                              allRoles,
                                                                              allPermissions,
                                                                          }) => {
    const selectedType: ResponsibleModelName =
        types.includes(value?.responsible_model_name as ResponsibleModelName)
            ? (value?.responsible_model_name as ResponsibleModelName)
            : "User"

    const selectedId = value?.responsible_id?.toString() ?? ""

    const listMap: Record<ResponsibleModelName, EntityDto[]> = {
        User: allUsers,
        Role: allRoles,
        Permission: allPermissions,
    }

    const list = listMap[selectedType]

    const handleTypeChange = (type: string) => {
        onChange({
            responsible_model_name: type as ResponsibleModelName,
            responsible_id: null,
            responsible_title: null,
        })
    }

    const handleItemChange = (id: string) => {
        const entity = list.find((item) => item.id.toString() === id)
        if (!entity) return
        onChange({
            responsible_model_name: selectedType,
            responsible_id: typeof entity.id === "string" ? Number(entity.id) || entity.id : entity.id,
            responsible_title: entity.title ?? entity.name ?? null,
        })
    }

    return (
        <div className="flex flex-col gap-2">
            <div className="flex gap-2 items-center">
                <Select value={selectedType} onValueChange={handleTypeChange}>
                    <SelectTrigger className="w-52">
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

                <Select value={selectedId} onValueChange={handleItemChange}>
                    <SelectTrigger className="w-full">
                        <SelectValue placeholder={`Select ${selectedType}`} />
                    </SelectTrigger>
                    <SelectContent>
                        {list.map((item) => (
                            <SelectItem key={item.id} value={item.id.toString()}>
                                {item.title ?? item.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    )
}
