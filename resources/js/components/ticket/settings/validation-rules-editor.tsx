import React from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { X } from "lucide-react"

export type ValidationRuleType =
    | "email"
    | "url"
    | "max_length"
    | "min_length"
    | "max_number"
    | "min_number"
    | "min_date"
    | "max_date"
    | "file_type"
    | "contains"
    | "not_contains"

export type ValidationRuleDto = {
    type: ValidationRuleType
    value: string | number | null
}

const RULE_TYPES: { type: ValidationRuleType; label: string; requiresValue: boolean }[] = [
    { type: "email", label: "Email", requiresValue: false },
    { type: "url", label: "URL", requiresValue: false },
    { type: "max_length", label: "Max Length", requiresValue: true },
    { type: "min_length", label: "Min Length", requiresValue: true },
    { type: "max_number", label: "Max Number", requiresValue: true },
    { type: "min_number", label: "Min Number", requiresValue: true },
    { type: "max_date", label: "Max Date", requiresValue: true },
    { type: "min_date", label: "Min Date", requiresValue: true },
    { type: "file_type", label: "File Type", requiresValue: true },
    { type: "contains", label: "Contains", requiresValue: true },
    { type: "not_contains", label: "Not Contains", requiresValue: true },
]

interface ValidationRulesEditorProps {
    value?: ValidationRuleDto[] // может быть неопределён
    onChange: (rules: ValidationRuleDto[]) => void
    error?: string | null
}

export const ValidationRulesEditor: React.FC<ValidationRulesEditorProps> = ({
                                                                                value = [],
                                                                                onChange,
                                                                                error,
                                                                            }) => {
    const handleAdd = () => {
        onChange([
            ...value,
            {
                type: "email",
                value: null,
            },
        ])
    }

    const handleRemove = (index: number) => {
        const updated = [...value]
        updated.splice(index, 1)
        onChange(updated)
    }

    const handleTypeChange = (index: number, type: ValidationRuleType) => {
        const updated = [...value]
        updated[index] = {
            ...updated[index],
            type,
            value: null,
        }
        onChange(updated)
    }

    const handleValueChange = (index: number, val: string) => {
        const updated = [...value]
        updated[index] = {
            ...updated[index],
            value: val,
        }
        onChange(updated)
    }

    return (
        <div className="flex flex-col gap-3">
            {value.map((rule, index) => {
                const typeMeta = RULE_TYPES.find((t) => t.type === rule.type)!

                return (
                    <div key={index} className="flex gap-2 items-center">
                        <Select
                            value={rule.type}
                            onValueChange={(t) => handleTypeChange(index, t as ValidationRuleType)}
                        >
                            <SelectTrigger className="w-full">
                                <SelectValue placeholder="Rule Type" />
                            </SelectTrigger>
                            <SelectContent>
                                {RULE_TYPES.map(({ type, label }) => (
                                    <SelectItem key={type} value={type}>
                                        {label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>

                        {typeMeta.requiresValue && (
                            <Input
                                className="w-full"
                                placeholder="Value"
                                value={rule.value?.toString() ?? ""}
                                onChange={(e) => handleValueChange(index, e.target.value)}
                            />
                        )}

                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => handleRemove(index)}
                            className="text-muted-foreground hover:text-destructive"
                        >
                            <X className="w-4 h-4" />
                        </Button>
                    </div>
                )
            })}

            <Button
                type="button"
                variant="ghost"
                onClick={handleAdd}
                className="text-sm text-muted-foreground hover:text-primary self-start flex items-center gap-1"
            >
                <span className="text-xl">＋</span> Add Rule
            </Button>

            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    )
}
