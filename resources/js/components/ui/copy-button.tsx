"use client"
import { useId, useRef, useState } from "react"
import { CheckIcon, CopyIcon } from "lucide-react"
import { cn } from "@/lib/utils"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip"

interface CopyButtonProps {
    textToCopy: string
    label?: string
    showLabel?: boolean
    tooltipText?: string
    className?: string
}

export default function CopyButton({
                                       textToCopy,
                                       label = "Copy to clipboard",
                                       showLabel = true,
                                       tooltipText = "Copy to clipboard",
                                       className,
                                   }: CopyButtonProps) {
    // Создаем уникальный ID для связи label и input
    const id = useId()
    // Состояние для отслеживания был ли текст скопирован
    const [copied, setCopied] = useState<boolean>(false)
    // Ссылка на input элемент
    const inputRef = useRef<HTMLInputElement>(null)

    // Функция для копирования текста в буфер обмена
    const handleCopy = () => {
        if (inputRef.current) {
            navigator.clipboard.writeText(textToCopy)
            setCopied(true)
            setTimeout(() => setCopied(false), 1500)
        }
    }

    return (
        <div className={cn("*:not-first:mt-2", className)}>
            {showLabel && <Label htmlFor={id}>{label}</Label>}
            <div className="relative">
                <Input
                    ref={inputRef}
                    id={id}
                    className="pe-9"
                    type="text"
                    defaultValue={textToCopy}
                    readOnly
                />
                <Tooltip>
                    <TooltipTrigger asChild>
                        <button
                            onClick={handleCopy}
                            className="text-muted-foreground/80 hover:text-foreground focus-visible:border-ring focus-visible:ring-ring/50 absolute inset-y-0 end-0 flex h-full w-9 items-center justify-center rounded-e-md transition-[color,box-shadow] outline-none focus:z-10 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed"
                            aria-label={copied ? "Copied" : tooltipText}
                            disabled={copied}
                        >
                            <div
                                className={cn(
                                    "transition-all",
                                    copied ? "scale-100 opacity-100" : "scale-0 opacity-0"
                                )}
                            >
                                <CheckIcon
                                    className="stroke-emerald-500"
                                    size={16}
                                    aria-hidden="true"
                                />
                            </div>
                            <div
                                className={cn(
                                    "absolute transition-all",
                                    copied ? "scale-0 opacity-0" : "scale-100 opacity-100"
                                )}
                            >
                                <CopyIcon size={16} aria-hidden="true" />
                            </div>
                        </button>
                    </TooltipTrigger>
                    <TooltipContent className="px-2 py-1 text-xs">
                        {copied ? "Copied!" : tooltipText}
                    </TooltipContent>
                </Tooltip>
            </div>
        </div>
    )
}
