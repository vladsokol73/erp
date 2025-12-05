// src/components/common/OptionsField.tsx
"use client"

import React, { useId, useState, useEffect, KeyboardEvent } from "react"
import { Input } from "@/components/ui/input"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { X } from "lucide-react"

interface OptionsFieldProps {
    value?: string[]
    onChange: (options: string[]) => void
    error?: string
}

export function OptionsField({
                                 value = [],
                                 onChange,
                                 error,
                             }: OptionsFieldProps) {
    const id = useId()
    const [options, setOptions] = useState<string[]>(value)
    const [inputValue, setInputValue] = useState("")

    useEffect(() => {
        setOptions(value)
    }, [value])

    const handleAdd = () => {
        const trimmed = inputValue.trim()
        if (trimmed && !options.includes(trimmed)) {
            const newOpts = [...options, trimmed]
            setOptions(newOpts)
            onChange(newOpts)
        }
        setInputValue("")
    }

    const handleKeyDown = (e: KeyboardEvent<HTMLInputElement>) => {
        if (e.key === "Enter") {
            e.preventDefault()
            handleAdd()
        }
    }

    const handleRemove = (opt: string) => {
        const newOpts = options.filter((o) => o !== opt)
        setOptions(newOpts)
        onChange(newOpts)
    }

    return (
        <div className="flex flex-col gap-2">
            <Input
                id={id}
                value={inputValue}
                onChange={(e) => setInputValue(e.target.value)}
                onKeyDown={handleKeyDown}
                placeholder="Add option and press Enter"
            />
            <div className="flex flex-wrap gap-2">
                {options.map((opt) => (
                    <Badge
                        key={opt}
                        variant="secondary"
                        className="flex items-center space-x-1"
                    >
                        <span>{opt}</span>
                        <button
                            onClick={() => handleRemove(opt)}
                        >
                            <X size={12} onClick={() => handleRemove(opt)} />
                        </button>
                    </Badge>
                ))}
            </div>
            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    )
}
